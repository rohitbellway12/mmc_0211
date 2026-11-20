<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\PromotionManagement\Entities\PushNotification;
use Modules\ProviderManagement\Entities\Provider;
use Modules\UserManagement\Entities\Serviceman;
use Modules\UserManagement\Entities\User;

class SendScheduledPushNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled and recurring push notifications when their time arrives';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $todayDate = $now->toDateString();

        // 1. One-Time Scheduled Notifications due now
        $oneTimeNotifications = PushNotification::where('is_active', 1)
            ->where('send_type', 'scheduled')
            ->where('repeat_type', 'once')
            ->where('is_sent', 0)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        // 2. Daily Recurring Notifications due today and not yet sent today
        $dailyNotifications = PushNotification::where('is_active', 1)
            ->where('send_type', 'scheduled')
            ->where('repeat_type', 'daily')
            ->whereNotNull('scheduled_time')
            ->where('scheduled_time', '<=', $currentTime)
            ->where(function ($query) use ($todayDate) {
                $query->whereNull('last_sent_at')
                      ->orWhereDate('last_sent_at', '<', $todayDate);
            })
            ->get();

        $allNotifications = $oneTimeNotifications->merge($dailyNotifications);

        if ($allNotifications->isEmpty()) {
            $this->info('No scheduled push notifications due right now.');
            return Command::SUCCESS;
        }

        \Log::info("[Scheduled Push] Processing {$allNotifications->count()} scheduled push notification(s) at {$now->toDateTimeString()}");
        $this->info("Found {$allNotifications->count()} scheduled notifications to process.");

        foreach ($allNotifications as $notification) {
            $this->dispatchNotification($notification);
        }

        return Command::SUCCESS;
    }

    /**
     * Dispatch topic and direct device notifications.
     */
    protected function dispatchNotification(PushNotification $notification): void
    {
        $title = $notification->title;
        $description = $notification->description;
        $coverImage = $notification->cover_image;
        $toUsers = is_array($notification->to_users) ? $notification->to_users : [];
        $zoneIds = is_array($notification->zone_ids) ? $notification->zone_ids : [];

        $permissionsMap = [
            'customer' => true,
            'provider-admin' => true,
            'provider-serviceman' => true
        ];

        $filteredUsers = array_filter(array_map(function ($user) use ($permissionsMap) {
            return ($permissionsMap[$user] ?? false) ? $user : null;
        }, $toUsers));

        if (in_array('all', $toUsers, true)) {
            $filteredUsers = array_keys(array_filter($permissionsMap));
        }

        // Send Topic Notifications
        foreach ($filteredUsers as $user_type) {
            foreach ($zoneIds as $zone_id) {
                try {
                    topic_notification("{$user_type}-{$zone_id}", $title, $description, $coverImage, null, 'general');
                } catch (\Exception $e) {
                    // Ignore topic send failure
                }
            }
        }

        // Send Direct Device Notifications via FCM Tokens
        $this->sendDeviceNotifications($filteredUsers, $zoneIds, $title, $description, $coverImage);

        // Update notification state
        $notification->update([
            'is_sent' => 1,
            'last_sent_at' => Carbon::now(),
        ]);

        \Log::info("[Scheduled Push] Dispatched successfully: ID {$notification->id}, Title: {$notification->title}");
        $this->info("Notification ID {$notification->id} ({$notification->title}) sent successfully.");
    }

    /**
     * Send direct device notifications to targeted users with active FCM tokens.
     */
    protected function sendDeviceNotifications(array $filteredUsers, array $zoneIds, string $title, string $description, ?string $coverImage): void
    {
        $tokens = [];
        $imageUrl = null;
        if (!empty($coverImage)) {
            if (filter_var($coverImage, FILTER_VALIDATE_URL)) {
                $imageUrl = $coverImage;
            } else {
                $imageUrl = function_exists('getSingleImageFullPath')
                    ? getSingleImageFullPath('push-notification/' . $coverImage, null, asset('storage/app/public/push-notification/' . $coverImage))
                    : asset('storage/app/public/push-notification/' . $coverImage);
            }
        }

        // 1. Customers
        if (in_array('customer', $filteredUsers, true)) {
            $customerTokens = User::where('user_type', 'customer')
                ->where('is_active', 1)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->when(!empty($zoneIds), function ($query) use ($zoneIds) {
                    $query->where(function ($sub) use ($zoneIds) {
                        $sub->whereHas('addresses', function ($aq) use ($zoneIds) {
                            $aq->whereIn('zone_id', $zoneIds);
                        })->orWhereDoesntHave('addresses');
                    });
                })
                ->pluck('fcm_token')
                ->toArray();

            $tokens = array_merge($tokens, $customerTokens);
        }

        // 2. Providers
        if (in_array('provider-admin', $filteredUsers, true)) {
            $providerUserIds = Provider::where('is_active', 1)
                ->when(!empty($zoneIds), function ($query) use ($zoneIds) {
                    $query->whereIn('zone_id', $zoneIds);
                })
                ->pluck('user_id')
                ->toArray();

            $providerTokens = User::where('user_type', 'provider-admin')
                ->whereIn('id', $providerUserIds)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')
                ->toArray();

            $tokens = array_merge($tokens, $providerTokens);
        }

        // 3. Servicemen
        if (in_array('provider-serviceman', $filteredUsers, true)) {
            $providerIds = Provider::where('is_active', 1)
                ->when(!empty($zoneIds), function ($query) use ($zoneIds) {
                    $query->whereIn('zone_id', $zoneIds);
                })
                ->pluck('id')
                ->toArray();

            $servicemanUserIds = Serviceman::whereIn('provider_id', $providerIds)
                ->pluck('user_id')
                ->toArray();

            $servicemanTokens = User::where('user_type', 'provider-serviceman')
                ->whereIn('id', $servicemanUserIds)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')
                ->toArray();

            $tokens = array_merge($tokens, $servicemanTokens);
        }

        $tokens = array_unique(array_filter($tokens));

        foreach ($tokens as $token) {
            try {
                device_notification($token, $title, $description, $imageUrl, null, 'general');
            } catch (\Exception $e) {
                // Ignore failure for individual device token
            }
        }
    }
}
