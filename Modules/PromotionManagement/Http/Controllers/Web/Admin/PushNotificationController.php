<?php

namespace Modules\PromotionManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\PromotionManagement\Entities\PushNotification;
use Modules\ZoneManagement\Entities\Zone;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\Serviceman;
use Modules\ProviderManagement\Entities\Provider;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PushNotificationController extends Controller
{
    private PushNotification $pushNotification;
    private Zone $zone;

    use AuthorizesRequests;

    public function __construct(PushNotification $pushNotification, Zone $zone)
    {
        $this->pushNotification = $pushNotification;
        $this->zone = $zone;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'string' => 'string',
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'status' => 'required|in:active,inactive,all',
            'to_user_type' => 'required|in:customer,provider,serviceman,all',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $pushNotification = $this->pushNotification
            ->when($request->has('string'), function ($query) use ($request) {
                $keys = explode(' ', base64_decode($request['string']));
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('title', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('status') && $request['status'] != 'all', function ($query) use ($request) {
                return $query->ofStatus(($request['status'] == 'active') ? 1 : 0);
            })->when($request->has('to_user_type') && $request['to_user_type'] != 'all', function ($query) use ($request) {
                return $query->whereJsonContains('to_users', $request['to_user_type']);
            })->orderBy('created_at', 'desc')->paginate(pagination_limit(), ['*'], 'offset', $request['offset'])->withPath('');

        $pushNotification->map(function ($query) {
            $query->zone_ids = $this->zone->select('id', 'name')->whereIn('id', $query->zone_ids)->get();
        });

        return response()->json(response_formatter(DEFAULT_200, $pushNotification), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create(Request $request): View|Factory|Application
    {
        $this->authorize('push_notification_view');
        $search = $request->has('search') ? $request['search'] : '';
        $toUserType = $request->has('to_user_type') ? $request['to_user_type'] : 'all';
        $queryParam = ['search' => $search, 'to_user_type' => $toUserType];

        $zones = $this->zone->ofStatus(1)->latest()->get();

        $pushNotification = $this->pushNotification
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('title', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('to_user_type') && $request['to_user_type'] != 'all', function ($query) use ($request) {
                return $query->whereJsonContains('to_users', $request['to_user_type']);
            })->orderBy('created_at', 'desc')->paginate(pagination_limit())->appends($queryParam);

        $pushNotification->map(function ($query) {
            $query->zone_ids = $this->zone->select('id', 'name')->whereIn('id', $query->zone_ids)->get();
        });

        return view('promotionmanagement::admin.push-notification.create', compact('zones', 'pushNotification', 'toUserType', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('push_notification_add');

        $validatedData = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:200',
            'to_users' => 'required|array',
            'to_users.*' => 'in:customer,provider-admin,provider-serviceman,all',
            'zone_ids' => 'required|array',
            'cover_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10000',
            'send_type' => 'required|in:immediate,scheduled',
            'repeat_type' => 'nullable|required_if:send_type,scheduled|in:once,daily',
            'scheduled_at' => 'nullable',
            'scheduled_time' => 'nullable',
        ]);

        $image_name = file_uploader('push-notification/', 'png', $request->file('cover_image'));

        $permissionsMap = [
            'customer' => true,
            'provider-admin' => true,
            'provider-serviceman' => true
        ];

        $filteredUsers = array_filter(array_map(function($user) use ($permissionsMap) {
            return $permissionsMap[$user] ? $user : null;
        }, $validatedData['to_users']));

        if (in_array('all', $validatedData['to_users'], true)) {
            $filteredUsers = array_keys(array_filter($permissionsMap));
        }

        $rawZoneIds = $validatedData['zone_ids'] ?? [];
        $hasAllZones = in_array('all', $rawZoneIds, true);
        if ($hasAllZones) {
            $savedZoneIds = $this->zone->ofStatus(1)->pluck('id')->toArray();
        } else {
            $savedZoneIds = $rawZoneIds;
        }

        $sendType = $request->input('send_type', 'immediate');
        $repeatType = $request->input('repeat_type', 'once');
        $isImmediate = ($sendType === 'immediate');

        if ($isImmediate) {
            $this->pushNotification->create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'to_users' => $filteredUsers,
                'zone_ids' => $savedZoneIds,
                'cover_image' => $image_name,
                'is_active' => 1,
                'send_type' => 'immediate',
                'repeat_type' => 'once',
                'scheduled_at' => null,
                'scheduled_time' => null,
                'is_sent' => 1,
                'last_sent_at' => now(),
            ]);

            foreach ($filteredUsers as $user_type) {
                try {
                    topic_notification("{$user_type}-all", $validatedData['title'], $validatedData['description'], $image_name, null, 'general');
                } catch (\Exception $e) {}

                foreach ($savedZoneIds as $zone_id) {
                    try {
                        topic_notification("{$user_type}-{$zone_id}", $validatedData['title'], $validatedData['description'], $image_name, null, 'general');
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            }

            $this->sendDeviceNotificationsToUsers($filteredUsers, $savedZoneIds, $validatedData['title'], $validatedData['description'], $image_name, $hasAllZones);

            Toastr::success(translate('Push notification sent successfully'));
        } else {
            // Scheduled Delivery: Multi-date or multi-time support
            if ($repeatType === 'once') {
                $rawDates = $request->input('scheduled_at', []);
                $dates = is_array($rawDates) ? array_values(array_filter($rawDates)) : array_filter([$rawDates]);

                if (empty($dates)) {
                    Toastr::error(translate('Please select at least one date and time slot'));
                    return back()->withInput();
                }

                foreach ($dates as $slot) {
                    $this->pushNotification->create([
                        'title' => $validatedData['title'],
                        'description' => $validatedData['description'],
                        'to_users' => $filteredUsers,
                        'zone_ids' => $savedZoneIds,
                        'cover_image' => $image_name,
                        'is_active' => 1,
                        'send_type' => 'scheduled',
                        'repeat_type' => 'once',
                        'scheduled_at' => $slot,
                        'scheduled_time' => null,
                        'is_sent' => 0,
                        'last_sent_at' => null,
                    ]);
                }

                $slotCount = count($dates);
                Toastr::success(translate("Push notification scheduled successfully for {$slotCount} date/time slot(s)"));
            } elseif ($repeatType === 'daily') {
                $rawTimes = $request->input('scheduled_time', []);
                $times = is_array($rawTimes) ? array_values(array_filter($rawTimes)) : array_filter([$rawTimes]);

                if (empty($times)) {
                    Toastr::error(translate('Please select at least one daily send time'));
                    return back()->withInput();
                }

                foreach ($times as $timeSlot) {
                    $this->pushNotification->create([
                        'title' => $validatedData['title'],
                        'description' => $validatedData['description'],
                        'to_users' => $filteredUsers,
                        'zone_ids' => $savedZoneIds,
                        'cover_image' => $image_name,
                        'is_active' => 1,
                        'send_type' => 'scheduled',
                        'repeat_type' => 'daily',
                        'scheduled_at' => null,
                        'scheduled_time' => $timeSlot,
                        'is_sent' => 0,
                        'last_sent_at' => null,
                    ]);
                }

                $timeCount = count($times);
                Toastr::success(translate("Daily push notification scheduled successfully for {$timeCount} time slot(s)"));
            }
        }

        return back();
    }

    public function resendNotification($id)
    {
        $pushNotification = $this->pushNotification->where(['id' => $id])->first();

        if (!$pushNotification){
            Toastr::error(translate('push_notification_not_found'));
            return back();
        }

        $permissionsMap = [
            'customer' => true,
            'provider-admin' => true,
            'provider-serviceman' => true
        ];

        $filteredUsers = array_filter(array_map(function($user) use ($permissionsMap) {
            return $permissionsMap[$user] ? $user : null;
        }, $pushNotification['to_users']));

        if (in_array('all', $pushNotification['to_users'], true)) {
            $filteredUsers = array_keys(array_filter($permissionsMap));
        }

        $zoneIds = $pushNotification['zone_ids'] ?? [];
        $hasAllZones = in_array('all', $zoneIds, true);

        foreach ($filteredUsers as $user_type) {
            try {
                topic_notification("{$user_type}-all", $pushNotification['title'], $pushNotification['description'], $pushNotification['cover_image'], null, 'general');
            } catch (\Exception $exception) {}

            foreach ($zoneIds as $zone_id) {
                try {
                    topic_notification("{$user_type}-{$zone_id}", $pushNotification['title'], $pushNotification['description'], $pushNotification['cover_image'], null, 'general');
                } catch (\Exception $exception) {}
            }
        }

        $this->sendDeviceNotificationsToUsers($filteredUsers, $zoneIds, $pushNotification['title'], $pushNotification['description'], $pushNotification['cover_image'], $hasAllZones);

        Toastr::success(translate('push notification send successfully'));
        return back();
    }


    /**
     * Show the form for editing the specified resource.
     * @param string $id
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function edit(string $id): View|Factory|Application
    {
        $this->authorize('push_notification_update');
        $pushNotification = $this->pushNotification->where('id', $id)->first();
        $zones = $this->zone->ofStatus(1)->latest()->get();
        $zoneArray = [];
        if ($pushNotification->zone_ids != null) {
            foreach ($pushNotification->zone_ids as $id) {
                $zone = $this->zone::select('id', 'name')->find($id);
                if (!is_null($zone)) {
                    $zoneArray[] = $zone;
                }
            }
            $pushNotification->zone_ids = $zoneArray;
        }
        return view('promotionmanagement::admin.push-notification.edit', compact('pushNotification','zones'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->authorize('push_notification_update');
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|max:200',
            'to_users' => 'required|array',
            'to_users.*' => 'in:customer,provider-admin,provider-serviceman,all',
            'zone_ids' => 'required|array',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10000',
            'send_type' => 'required|in:immediate,scheduled',
            'repeat_type' => 'nullable|required_if:send_type,scheduled|in:once,daily',
            'scheduled_at' => 'nullable',
            'scheduled_time' => 'nullable',
        ]);

        $pushNotification = $this->pushNotification->where(['id' => $id])->first();
        if (!$pushNotification) {
            Toastr::error(translate('Push notification not found'));
            return back();
        }

        $rawZoneIds = $request->input('zone_ids', []);
        if (in_array('all', $rawZoneIds, true)) {
            $savedZoneIds = $this->zone->ofStatus(1)->pluck('id')->toArray();
        } else {
            $savedZoneIds = $rawZoneIds;
        }

        $pushNotification->title = $request['title'];
        $pushNotification->description = $request['description'];
        $pushNotification->to_users = $request['to_users'];
        $pushNotification->zone_ids = $savedZoneIds;

        $sendType = $request->input('send_type', 'immediate');
        $repeatType = $request->input('repeat_type', 'once');

        $rawScheduledAt = $request->input('scheduled_at', []);
        $dates = is_array($rawScheduledAt) ? array_values(array_filter($rawScheduledAt)) : array_filter([$rawScheduledAt]);
        $scheduledAt = ($sendType === 'scheduled' && $repeatType === 'once') ? ($dates[0] ?? null) : null;

        $rawScheduledTime = $request->input('scheduled_time', []);
        $times = is_array($rawScheduledTime) ? array_values(array_filter($rawScheduledTime)) : array_filter([$rawScheduledTime]);
        $scheduledTime = ($sendType === 'scheduled' && $repeatType === 'daily') ? ($times[0] ?? null) : null;

        $pushNotification->send_type = $sendType;
        $pushNotification->repeat_type = $repeatType;
        $pushNotification->scheduled_at = $scheduledAt;
        $pushNotification->scheduled_time = $scheduledTime;

        if ($sendType === 'scheduled') {
            $pushNotification->is_sent = 0;
            $pushNotification->last_sent_at = null;
        }

        if ($request->has('cover_image')) {
            $pushNotification->cover_image = file_uploader('push-notification/', 'png', $request->file('cover_image'), $pushNotification->cover_image);
        }

        $pushNotification->save();

        // Additional slots created if more than 1 slot provided during edit
        if ($sendType === 'scheduled') {
            if ($repeatType === 'once' && count($dates) > 1) {
                for ($i = 1; $i < count($dates); $i++) {
                    $this->pushNotification->create([
                        'title' => $pushNotification->title,
                        'description' => $pushNotification->description,
                        'to_users' => $pushNotification->to_users,
                        'zone_ids' => $pushNotification->zone_ids,
                        'cover_image' => $pushNotification->cover_image,
                        'is_active' => 1,
                        'send_type' => 'scheduled',
                        'repeat_type' => 'once',
                        'scheduled_at' => $dates[$i],
                        'scheduled_time' => null,
                        'is_sent' => 0,
                        'last_sent_at' => null,
                    ]);
                }
            } elseif ($repeatType === 'daily' && count($times) > 1) {
                for ($i = 1; $i < count($times); $i++) {
                    $this->pushNotification->create([
                        'title' => $pushNotification->title,
                        'description' => $pushNotification->description,
                        'to_users' => $pushNotification->to_users,
                        'zone_ids' => $pushNotification->zone_ids,
                        'cover_image' => $pushNotification->cover_image,
                        'is_active' => 1,
                        'send_type' => 'scheduled',
                        'repeat_type' => 'daily',
                        'scheduled_at' => null,
                        'scheduled_time' => $times[$i],
                        'is_sent' => 0,
                        'last_sent_at' => null,
                    ]);
                }
            }
        }

        Toastr::success(translate(DEFAULT_UPDATE_200['message']));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('push_notification_delete');
        $pushNotification = $this->pushNotification->where('id', $id)->withoutGlobalScope('translate')->first();
        if (isset($pushNotification)){
            file_remover('push-notification/', $pushNotification['cover_image']);
            $this->pushNotification->where('id', $id)->withoutGlobalScope('translate')->delete();
        }

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function statusUpdate(Request $request, $id): JsonResponse
    {
        $this->authorize('push_notification_manage_status');
        $pushNotification = $this->pushNotification->where('id', $id)->first();
        $this->pushNotification->where('id', $id)->update(['is_active' => !$pushNotification->is_active]);

        return response()->json(response_formatter(DEFAULT_STATUS_UPDATE_200), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
        $this->authorize('push_notification_export');

        $items = $this->pushNotification
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('title', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('status') && $request['status'] != 'all', function ($query) use ($request) {
                return $query->ofStatus(($request['status'] == 'active') ? 1 : 0);
            })
            ->when($request->has('to_user_type') && $request['to_user_type'] != 'all', function ($query) use ($request) {
                return $query->whereJsonContains('to_users', $request['to_user_type']);
            })
            ->orderBy('created_at', 'desc')
            ->latest()->get();

        return (new FastExcel($items))->download(time().'-file.xlsx');
    }

    /**
     * Send direct device notifications to targeted users with active FCM tokens.
     */
    private function sendDeviceNotificationsToUsers(array $filteredUsers, array $zoneIds, string $title, string $description, ?string $coverImage, bool $hasAllZones = false): void
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
        if (in_array('customer', $filteredUsers)) {
            $customerTokens = User::where('user_type', 'customer')
                ->where('is_active', 1)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->when(!$hasAllZones && !empty($zoneIds), function ($query) use ($zoneIds) {
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
        if (in_array('provider-admin', $filteredUsers)) {
            $providerUserIds = Provider::where('is_active', 1)
                ->when(!$hasAllZones && !empty($zoneIds), function ($query) use ($zoneIds) {
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
        if (in_array('provider-serviceman', $filteredUsers)) {
            $providerIds = Provider::where('is_active', 1)
                ->when(!$hasAllZones && !empty($zoneIds), function ($query) use ($zoneIds) {
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
                // Ignore any single failed token and continue
            }
        }
    }
}
