<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\BookingModule\Entities\Booking;
use Modules\BusinessSettingsModule\Entities\BusinessSettings;
use Modules\PromotionManagement\Entities\BookingReminderLog;
use Modules\PromotionManagement\Entities\PushNotification;
use Modules\PromotionManagement\Http\Controllers\Web\Admin\BookingReminderController;

class SendBookingReminders extends Command
{
   
    protected $signature = 'booking:send-reminders';


    protected $description = 'Send automated booking reminder push notifications to providers and customers based on booking schedules';

  
    public function handle(): int
    {
        $rawSettings = BusinessSettings::where('key_name', 'booking_reminder_settings')->first();
        if (!$rawSettings || empty($rawSettings->live_values)) {
            $this->info('[Booking Reminders] No configuration found. Skipping.');
            return 0;
        }

        $defaults = BookingReminderController::getDefaultSettings();
        $saved = is_array($rawSettings->live_values) ? $rawSettings->live_values : json_decode($rawSettings->live_values, true);
        $settings = array_replace_recursive($defaults, $saved ?: []);

        if (empty($settings['is_active'])) {
            $this->info('[Booking Reminders] Feature is globally disabled. Skipping.');
            return 0;
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        $this->info("[Booking Reminders] Running check at {$now->toDateTimeString()} ({$now->timezoneName})");

        $providerSentCount = 0;
        $customerSentCount = 0;

    
        if (!empty($settings['is_active'])) {
            $rules = BookingReminderController::normalizeProviderRules($settings);

            // Fetch upcoming active bookings with an assigned provider (up to 7 days in advance)
            $bookings = Booking::with(['provider.owner', 'customer', 'detail.service'])
                ->whereIn('booking_status', ['accepted', 'ongoing'])
                ->whereNotNull('provider_id')
                ->where('service_schedule', '>', $now)
                ->where('service_schedule', '<=', $now->copy()->addDays(7))
                ->get();

            foreach ($bookings as $booking) {
                $providerUser = $booking->provider?->owner;
                if (!$providerUser) {
                    continue;
                }

                $serviceSchedule = Carbon::parse($booking->service_schedule);
                $minutesRemaining = $now->diffInMinutes($serviceSchedule, false);
                if ($minutesRemaining <= 0) {
                    continue;
                }

                foreach ($rules as $rule) {
                    if (empty($rule['is_active'])) {
                        continue;
                    }

                    $ruleId = $rule['id'] ?? ('rule_' . md5($rule['name'] ?? 'default'));
                    $triggerType = $rule['trigger_type'] ?? 'before_hours';
                    $shouldTrigger = false;

                    if ($triggerType === 'before_hours') {
                        $targetHours = (float) ($rule['trigger_value'] ?? 24);
                        $targetMinutes = $targetHours * 60;
                        $minThreshold = $targetHours > 4 ? ($targetHours / 2) * 60 : 0;

                        if ($minutesRemaining <= $targetMinutes && $minutesRemaining >= $minThreshold) {
                            $shouldTrigger = true;
                        }
                    } elseif ($triggerType === 'day_morning') {
                        $morningTargetTime = $rule['trigger_value'] ?? '07:00';
                        $isBookingToday = $serviceSchedule->toDateString() === $now->toDateString();

                        if ($isBookingToday && $currentTime >= $morningTargetTime) {
                            $shouldTrigger = true;
                        }
                    }

                    if ($shouldTrigger) {
                        $alreadySent = BookingReminderLog::where('booking_id', $booking->id)
                            ->where('target_type', 'provider')
                            ->where('reminder_type', $ruleId)
                            ->exists();

                        if (!$alreadySent) {
                            $this->dispatchReminder($booking, $providerUser, 'provider', $ruleId, $rule, $minutesRemaining);
                            $providerSentCount++;
                        }
                    }
                }
            }
        }

     
        /*
        if (!empty($settings['customer']['is_active'])) {
            $customerSettings = $settings['customer'];

            $bookings = Booking::with(['provider.owner', 'customer', 'detail.service'])
                ->whereIn('booking_status', ['pending', 'accepted', 'ongoing'])
                ->whereNotNull('customer_id')
                ->where('service_schedule', '>', $now)
                ->where('service_schedule', '<=', $now->copy()->addDays(3))
                ->get();

            foreach ($bookings as $booking) {
                $customerUser = $booking->customer;
                if (!$customerUser) {
                    continue;
                }

                $serviceSchedule = Carbon::parse($booking->service_schedule);
                $minutesRemaining = $now->diffInMinutes($serviceSchedule, false);
                if ($minutesRemaining <= 0) {
                    continue;
                }

                // Rule 1: Advance Reminder (e.g. 24h)
                if (!empty($customerSettings['advance']['is_active'])) {
                    $advanceHours = (int) ($customerSettings['advance']['hours'] ?? 24);
                    $targetMinutes = $advanceHours * 60;
                    $minThreshold = max(60, ($advanceHours / 2) * 60);

                    if ($minutesRemaining <= $targetMinutes && $minutesRemaining >= $minThreshold) {
                        $alreadySent = BookingReminderLog::where('booking_id', $booking->id)
                            ->where('target_type', 'customer')
                            ->where('reminder_type', 'advance')
                            ->exists();

                        if (!$alreadySent) {
                            $this->dispatchReminder($booking, $customerUser, 'customer', 'advance', $customerSettings['advance'], $minutesRemaining);
                            $customerSentCount++;
                        }
                    }
                }

                // Rule 2: Booking Day Morning Reminder (e.g. 08:00 AM)
                if (!empty($customerSettings['morning']['is_active'])) {
                    $morningTargetTime = $customerSettings['morning']['time'] ?? '08:00';
                    $isBookingToday = $serviceSchedule->toDateString() === $now->toDateString();

                    if ($isBookingToday && $currentTime >= $morningTargetTime) {
                        $alreadySent = BookingReminderLog::where('booking_id', $booking->id)
                            ->where('target_type', 'customer')
                            ->where('reminder_type', 'morning')
                            ->exists();

                        if (!$alreadySent) {
                            $this->dispatchReminder($booking, $customerUser, 'customer', 'morning', $customerSettings['morning'], $minutesRemaining);
                            $customerSentCount++;
                        }
                    }
                }

                // Rule 3: Final Reminder (e.g. 2h before service)
                if (!empty($customerSettings['final']['is_active'])) {
                    $finalHours = (int) ($customerSettings['final']['hours'] ?? 2);
                    $targetMinutes = $finalHours * 60;

                    if ($minutesRemaining <= $targetMinutes && $minutesRemaining > 0) {
                        $alreadySent = BookingReminderLog::where('booking_id', $booking->id)
                            ->where('target_type', 'customer')
                            ->where('reminder_type', 'final')
                            ->exists();

                        if (!$alreadySent) {
                            $this->dispatchReminder($booking, $customerUser, 'customer', 'final', $customerSettings['final'], $minutesRemaining);
                            $customerSentCount++;
                        }
                    }
                }
            }
        }
        */

        $summary = "Sent {$providerSentCount} provider reminder(s) and {$customerSentCount} customer reminder(s).";
        $this->info("[Booking Reminders] Done. {$summary}");
        Log::info("[Booking Reminders] {$summary}");

        return 0;
    }

   
    protected function dispatchReminder(Booking $booking, $user, string $targetType, string $reminderType, array $rule, int $minutesRemaining): void
    {
        $serviceSchedule = Carbon::parse($booking->service_schedule);
        $serviceName = $booking->detail?->first()?->service?->name ?? 'Scheduled Service';

        $placeholders = [
            '{booking_id}' => $booking->readable_id ?? substr($booking->id, 0, 8),
            '{customer_name}' => trim(($booking->customer?->first_name ?? '') . ' ' . ($booking->customer?->last_name ?? '')),
            '{provider_name}' => ($booking->provider?->company_name ?? $booking->provider?->owner?->first_name ?? 'Provider'),
            '{service_schedule}' => $serviceSchedule->format('d M Y, h:i A'),
            '{hours}' => round($minutesRemaining / 60),
            '{service_name}' => $serviceName,
        ];

        $title = str_replace(array_keys($placeholders), array_values($placeholders), $rule['title'] ?? 'Booking Reminder');
        $body = str_replace(array_keys($placeholders), array_values($placeholders), $rule['message'] ?? 'You have an upcoming booking.');

        $fcmToken = $user->fcm_token;
        $sendSuccess = false;
        $logResponse = '';

        if (!empty($fcmToken)) {
            try {
                $repeatOrRegular = $booking->is_repeated ? 'repeat' : 'regular';
                device_notification($fcmToken, $title, $body, null, $booking->id, 'booking', null, null, null, null, $repeatOrRegular);
                $sendSuccess = true;
                $logResponse = 'Delivered to FCM token: ' . substr($fcmToken, 0, 15) . '...';
            } catch (\Exception $e) {
                $logResponse = 'FCM Dispatch Exception: ' . $e->getMessage();
            }
        } else {
            $logResponse = "User {$user->id} has no registered FCM token.";
        }

        // Save in PushNotification for in-app notification center
        try {
            $pushNotification = new PushNotification();
            $pushNotification->title = $title;
            $pushNotification->description = $body;
            $pushNotification->zone_ids = [$booking->zone_id];
            $pushNotification->to_users = [$targetType === 'provider' ? 'provider-admin' : 'customer'];
            $pushNotification->notification_type = 'booking';
            $pushNotification->booking_id = $booking->id;
            $pushNotification->booking_status = $booking->booking_status;
            $pushNotification->reference_id = $booking->id;
            $pushNotification->is_active = 1;
            $pushNotification->save();
        } catch (\Exception $e) {}

        // Record log to prevent duplicates
        BookingReminderLog::create([
            'booking_id' => $booking->id,
            'target_type' => $targetType,
            'reminder_type' => $reminderType,
            'user_id' => $user->id,
            'fcm_token' => $fcmToken,
            'title' => $title,
            'body' => $body,
            'sent_at' => Carbon::now(),
            'status' => $sendSuccess ? 'sent' : 'failed',
            'response_log' => $logResponse,
        ]);

        $this->line(" -> [{$targetType}] [{$reminderType}] Booking #{$booking->readable_id}: {$title} ({$logResponse})");
    }
}
