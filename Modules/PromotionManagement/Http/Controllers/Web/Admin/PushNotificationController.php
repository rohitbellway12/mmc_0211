<?php

namespace Modules\PromotionManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
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
use Modules\CategoryManagement\Entities\Category;
use Modules\BookingModule\Entities\Booking;
use Illuminate\Support\Facades\DB;
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

        $categories = Category::ofStatus(1)->ofType('main')->orderBy('name', 'asc')->get();
        $pushNotification->load('category');

        return view('promotionmanagement::admin.push-notification.create', compact('zones', 'pushNotification', 'toUserType', 'search', 'categories'));
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
            'to_users' => 'nullable|array',
            'to_users.*' => 'in:customer,provider-admin,partner-admin,provider-serviceman,all',
            'zone_ids' => 'nullable|array',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10000',
            'send_type' => 'nullable|in:immediate,scheduled',
            'repeat_type' => 'nullable|in:once,daily',
            'scheduled_at' => 'nullable',
            'scheduled_time' => 'nullable',
            'category_id' => 'nullable',
            'on_tap_action' => 'nullable|string',
            'audience_type' => 'nullable|in:all,targeted',
            'estimated_reach' => 'nullable|numeric',
        ]);

        $image_name = $request->hasFile('cover_image') ? file_uploader('push-notification/', 'png', $request->file('cover_image')) : null;

        

        $permissionsMap = [
            'customer' => true,
            'partner-admin' => true,
            'provider-admin' => true,
            'provider-serviceman' => true
        ];

        $rawUsers = !empty($validatedData['to_users']) ? $validatedData['to_users'] : ['customer'];
        $filteredUsers = array_filter(array_map(function($user) use ($permissionsMap) {
            return ($permissionsMap[$user] ?? false) ? $user : null;
        }, $rawUsers));

        if (empty($filteredUsers) || in_array('all', $rawUsers, true)) {
            $filteredUsers = in_array('all', $rawUsers, true) ? array_keys(array_filter($permissionsMap)) : ['customer'];
        }

        $rawZoneIds = !empty($validatedData['zone_ids']) ? $validatedData['zone_ids'] : ['all'];
        $hasAllZones = in_array('all', $rawZoneIds, true) || empty($rawZoneIds);
        if ($hasAllZones) {
            $savedZoneIds = $this->zone->ofStatus(1)->pluck('id')->toArray();
        } else {
            $savedZoneIds = $rawZoneIds;
        }

        $sendType = $request->input('send_type', 'immediate');
        $repeatType = $request->input('repeat_type', 'once');
        $isImmediate = ($sendType === 'immediate');

        if ($isImmediate) {
            $createdPush = $this->pushNotification->create([
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
                'category_id' => $request->input('category_id') ?: null,
                'on_tap_action' => $request->input('on_tap_action', 'general'),
                'audience_type' => $request->input('audience_type', 'all'),
                'estimated_reach' => (int) $request->input('estimated_reach', 0),
            ]);

            $deepLinkData = [
                'notification_id' => (string) $createdPush->id,
                'action_type' => (string) ($createdPush->on_tap_action ?? 'general'),
                'category_id' => (string) ($createdPush->category_id ?? ''),
            ];

            if ($createdPush->audience_type === 'targeted' && !empty($createdPush->category_id)) {
                // Targeted campaign: Only send directly to eligible targeted device tokens
                $sentCount = $this->sendDeviceNotificationsToUsers($filteredUsers, $savedZoneIds, $validatedData['title'], $validatedData['description'], $image_name, $hasAllZones, $deepLinkData, $createdPush->audience_type, $createdPush->category_id);
                $createdPush->update(['sent_count' => $sentCount]);
            } else {
                // General broadcast: Send ONLY once per audience scope (All zones OR specific zones, never duplicate)
                if ($hasAllZones) {
                    foreach ($filteredUsers as $user_type) {
                        try {
                            topic_notification("{$user_type}-all", $validatedData['title'], $validatedData['description'], $image_name, null, 'general', $deepLinkData);
                        } catch (\Exception $e) {}
                    }
                } else {
                    foreach ($filteredUsers as $user_type) {
                        foreach ($savedZoneIds as $zone_id) {
                            try {
                                topic_notification("{$user_type}-{$zone_id}", $validatedData['title'], $validatedData['description'], $image_name, null, 'general', $deepLinkData);
                            } catch (\Exception $e) {}
                        }
                    }
                }
                $sentCount = (int) ($createdPush->estimated_reach ?: 1);
                $createdPush->update(['sent_count' => $sentCount]);
            }

            Toastr::success(translate('Push notification sent successfully'));
        } else {
            $userTzOffset = $request->input('user_tz_offset') !== null ? (int) $request->input('user_tz_offset') : null;

            // Scheduled Delivery: Multi-date or multi-time support
            if ($repeatType === 'once') {
                $rawDates = $request->input('scheduled_at', []);
                $dates = is_array($rawDates) ? array_values(array_filter($rawDates)) : array_filter([$rawDates]);

                if (empty($dates)) {
                    Toastr::error(translate('Please select at least one date and time slot'));
                    return back()->withInput();
                }

                foreach ($dates as $slot) {
                    $systemSlot = $this->convertToSystemTimezone($slot, $userTzOffset, 'datetime');
                    $this->pushNotification->create([
                        'title' => $validatedData['title'],
                        'description' => $validatedData['description'],
                        'to_users' => $filteredUsers,
                        'zone_ids' => $savedZoneIds,
                        'cover_image' => $image_name,
                        'is_active' => 1,
                        'send_type' => 'scheduled',
                        'repeat_type' => 'once',
                        'scheduled_at' => $systemSlot,
                        'scheduled_time' => null,
                        'is_sent' => 0,
                        'last_sent_at' => null,
                        'category_id' => $request->input('category_id') ?: null,
                        'on_tap_action' => $request->input('on_tap_action', 'general'),
                        'audience_type' => $request->input('audience_type', 'all'),
                        'estimated_reach' => (int) $request->input('estimated_reach', 0),
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
                    $systemTime = $this->convertToSystemTimezone($timeSlot, $userTzOffset, 'time');
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
                        'scheduled_time' => $systemTime,
                        'is_sent' => 0,
                        'last_sent_at' => null,
                        'category_id' => $request->input('category_id') ?: null,
                        'on_tap_action' => $request->input('on_tap_action', 'general'),
                        'audience_type' => $request->input('audience_type', 'all'),
                        'estimated_reach' => (int) $request->input('estimated_reach', 0),
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
            'partner-admin' => true,
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

        $deepLinkData = [
            'notification_id' => (string) $pushNotification->id,
            'action_type' => (string) ($pushNotification->on_tap_action ?? 'general'),
            'category_id' => (string) ($pushNotification->category_id ?? ''),
        ];

        if (($pushNotification->audience_type ?? 'all') === 'targeted' && !empty($pushNotification->category_id)) {
            // Targeted campaign: Only send directly to eligible targeted device tokens
            $sentCount = $this->sendDeviceNotificationsToUsers($filteredUsers, $zoneIds, $pushNotification['title'], $pushNotification['description'], $pushNotification['cover_image'], $hasAllZones, $deepLinkData, $pushNotification->audience_type ?? 'all', $pushNotification->category_id ?? null);
            $pushNotification->increment('sent_count', $sentCount);
        } else {
            // General broadcast: Send ONLY once per scope (All zones OR specific zones, never duplicate)
            if ($hasAllZones) {
                foreach ($filteredUsers as $user_type) {
                    try {
                        topic_notification("{$user_type}-all", $pushNotification['title'], $pushNotification['description'], $pushNotification['cover_image'], null, 'general', $deepLinkData);
                    } catch (\Exception $exception) {}
                }
            } else {
                foreach ($filteredUsers as $user_type) {
                    foreach ($zoneIds as $zone_id) {
                        try {
                            topic_notification("{$user_type}-{$zone_id}", $pushNotification['title'], $pushNotification['description'], $pushNotification['cover_image'], null, 'general', $deepLinkData);
                        } catch (\Exception $exception) {}
                    }
                }
            }
            $pushNotification->increment('sent_count', (int) ($pushNotification->estimated_reach ?: 1));
        }

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
        $categories = Category::ofStatus(1)->ofType('main')->orderBy('name', 'asc')->get();
        return view('promotionmanagement::admin.push-notification.edit', compact('pushNotification', 'zones', 'categories'));
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
            'to_users.*' => 'in:customer,provider-admin,partner-admin,provider-serviceman,all',
            'zone_ids' => 'required|array',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10000',
            'send_type' => 'required|in:immediate,scheduled',
            'repeat_type' => 'nullable|required_if:send_type,scheduled|in:once,daily',
            'scheduled_at' => 'nullable',
            'scheduled_time' => 'nullable',
            'category_id' => 'nullable',
            'on_tap_action' => 'required|string',
            'audience_type' => 'required|in:all,targeted',
            'estimated_reach' => 'nullable|numeric',
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
        $userTzOffset = $request->input('user_tz_offset') !== null ? (int) $request->input('user_tz_offset') : null;

        $rawScheduledAt = $request->input('scheduled_at', []);
        $dates = is_array($rawScheduledAt) ? array_values(array_filter($rawScheduledAt)) : array_filter([$rawScheduledAt]);
        $scheduledAt = ($sendType === 'scheduled' && $repeatType === 'once' && !empty($dates))
            ? $this->convertToSystemTimezone($dates[0], $userTzOffset, 'datetime')
            : null;

        $rawScheduledTime = $request->input('scheduled_time', []);
        $times = is_array($rawScheduledTime) ? array_values(array_filter($rawScheduledTime)) : array_filter([$rawScheduledTime]);
        $scheduledTime = ($sendType === 'scheduled' && $repeatType === 'daily' && !empty($times))
            ? $this->convertToSystemTimezone($times[0], $userTzOffset, 'time')
            : null;

        $pushNotification->send_type = $sendType;
        $pushNotification->repeat_type = $repeatType;
        $pushNotification->scheduled_at = $scheduledAt;
        $pushNotification->scheduled_time = $scheduledTime;
        $pushNotification->category_id = $request->input('category_id') ?: null;
        $pushNotification->on_tap_action = $request->input('on_tap_action', 'general');
        $pushNotification->audience_type = $request->input('audience_type', 'all');
        if ($request->filled('estimated_reach')) {
            $pushNotification->estimated_reach = (int) $request->input('estimated_reach');
        }

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
                    $systemSlot = $this->convertToSystemTimezone($dates[$i], $userTzOffset, 'datetime');
                    $this->pushNotification->create([
                        'title' => $pushNotification->title,
                        'description' => $pushNotification->description,
                        'to_users' => $pushNotification->to_users,
                        'zone_ids' => $pushNotification->zone_ids,
                        'cover_image' => $pushNotification->cover_image,
                        'is_active' => 1,
                        'send_type' => 'scheduled',
                        'repeat_type' => 'once',
                        'scheduled_at' => $systemSlot,
                        'scheduled_time' => null,
                        'is_sent' => 0,
                        'last_sent_at' => null,
                        'category_id' => $request->input('category_id') ?: null,
                        'on_tap_action' => $request->input('on_tap_action', 'general'),
                        'audience_type' => $request->input('audience_type', 'all'),
                        'estimated_reach' => (int) $request->input('estimated_reach', 0),
                    ]);
                }
            } elseif ($repeatType === 'daily' && count($times) > 1) {
                for ($i = 1; $i < count($times); $i++) {
                    $systemTime = $this->convertToSystemTimezone($times[$i], $userTzOffset, 'time');
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
                        'scheduled_time' => $systemTime,
                        'is_sent' => 0,
                        'last_sent_at' => null,
                        'category_id' => $request->input('category_id') ?: null,
                        'on_tap_action' => $request->input('on_tap_action', 'general'),
                        'audience_type' => $request->input('audience_type', 'all'),
                        'estimated_reach' => (int) $request->input('estimated_reach', 0),
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
    private function sendDeviceNotificationsToUsers(
        array $filteredUsers,
        array $zoneIds,
        string $title,
        string $description,
        ?string $coverImage,
        bool $hasAllZones = false,
        array $deepLinkData = [],
        string $audienceType = 'all',
        ?string $categoryId = null
    ): int {
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
            $customerQuery = User::where('user_type', 'customer')
                ->where('is_active', 1)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->when(!$hasAllZones && !empty($zoneIds), function ($query) use ($zoneIds) {
                    $query->where(function ($sub) use ($zoneIds) {
                        $sub->whereHas('addresses', function ($aq) use ($zoneIds) {
                            $aq->whereIn('zone_id', $zoneIds);
                        })->orWhereDoesntHave('addresses');
                    });
                });

            if ($audienceType === 'targeted' && !empty($categoryId)) {
                $subCategoryIds = Category::where('parent_id', $categoryId)->pluck('id')->push($categoryId)->toArray();

                $bookedCustomerIds = Booking::whereIn('category_id', $subCategoryIds)
                    ->orWhereIn('sub_category_id', $subCategoryIds)
                    ->pluck('customer_id')
                    ->filter()
                    ->toArray();

                $estimatedCustomerIds = DB::table('booking_estimates')
                    ->whereIn('category_id', $subCategoryIds)
                    ->orWhereIn('sub_category_id', $subCategoryIds)
                    ->pluck('customer_id')
                    ->filter()
                    ->toArray();

                $targetedCustomerIds = array_unique(array_merge($bookedCustomerIds, $estimatedCustomerIds));
                $customerQuery->whereIn('id', $targetedCustomerIds);
            }

            $customerTokens = $customerQuery->pluck('fcm_token')->toArray();
            $tokens = array_merge($tokens, $customerTokens);
        }

        // 2. Partners (is_partner = 1)
        if (in_array('partner-admin', $filteredUsers, true)) {
            $partnerUserIds = Provider::where('is_active', 1)
                ->where('is_partner', 1)
                ->when(!$hasAllZones && !empty($zoneIds), function ($query) use ($zoneIds) {
                    $query->whereIn('zone_id', $zoneIds);
                })
                ->pluck('user_id')
                ->toArray();

            $partnerTokens = User::where('user_type', 'provider-admin')
                ->whereIn('id', $partnerUserIds)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')
                ->toArray();

            $tokens = array_merge($tokens, $partnerTokens);
        }

        // 3. Providers (is_partner = 0)
        if (in_array('provider-admin', $filteredUsers, true)) {
            $providerUserIds = Provider::where('is_active', 1)
                ->where('is_partner', 0)
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

        // 4. Servicemen
        if (in_array('provider-serviceman', $filteredUsers, true)) {
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
                device_notification($token, $title, $description, $imageUrl, null, 'general', null, null, $deepLinkData);
            } catch (\Exception $e) {
                // Ignore any single failed token and continue
            }
        }

        return count($tokens);
    }

    /**
     * Estimate recipient reach dynamically.
     * @param Request $request
     * @return JsonResponse
     */
    public function estimateReach(Request $request): JsonResponse
    {
        $categoryId = $request->input('category_id');
        $audienceType = $request->input('audience_type', 'all');
        $rawZoneIds = (array) $request->input('zone_ids', []);
        $toUsers = (array) $request->input('to_users', ['customer']);

        $hasAllZones = in_array('all', $rawZoneIds, true) || empty($rawZoneIds);
        $zoneIds = $hasAllZones ? [] : $rawZoneIds;

        $customerCount = 0;
        $partnerCount = 0;
        $providerCount = 0;
        $servicemanCount = 0;

        if (in_array('all', $toUsers, true) || in_array('customer', $toUsers, true)) {
            $customerQuery = User::where('user_type', 'customer')->where('is_active', 1);

            if (!$hasAllZones && !empty($zoneIds)) {
                $customerQuery->where(function ($sub) use ($zoneIds) {
                    $sub->whereHas('addresses', function ($aq) use ($zoneIds) {
                        $aq->whereIn('zone_id', $zoneIds);
                    })->orWhereDoesntHave('addresses');
                });
            }

            if ($audienceType === 'targeted' && !empty($categoryId)) {
                $subCategoryIds = Category::where('parent_id', $categoryId)->pluck('id')->push($categoryId)->toArray();

                $bookedCustomerIds = Booking::whereIn('category_id', $subCategoryIds)
                    ->orWhereIn('sub_category_id', $subCategoryIds)
                    ->pluck('customer_id')
                    ->filter()
                    ->toArray();

                $estimatedCustomerIds = DB::table('booking_estimates')
                    ->whereIn('category_id', $subCategoryIds)
                    ->orWhereIn('sub_category_id', $subCategoryIds)
                    ->pluck('customer_id')
                    ->filter()
                    ->toArray();

                $targetedIds = array_unique(array_merge($bookedCustomerIds, $estimatedCustomerIds));
                $customerQuery->whereIn('id', $targetedIds);
            }

            $customerCount = $customerQuery->count();
        }

        if (in_array('all', $toUsers, true) || in_array('partner-admin', $toUsers, true)) {
            $partnerCount = Provider::where('is_active', 1)
                ->where('is_partner', 1)
                ->when(!$hasAllZones && !empty($zoneIds), fn($q) => $q->whereIn('zone_id', $zoneIds))
                ->count();
        }

        if (in_array('all', $toUsers, true) || in_array('provider-admin', $toUsers, true)) {
            $providerCount = Provider::where('is_active', 1)
                ->where('is_partner', 0)
                ->when(!$hasAllZones && !empty($zoneIds), fn($q) => $q->whereIn('zone_id', $zoneIds))
                ->count();
        }

        if (in_array('all', $toUsers, true) || in_array('provider-serviceman', $toUsers, true)) {
            $providerIds = Provider::where('is_active', 1)
                ->when(!$hasAllZones && !empty($zoneIds), fn($q) => $q->whereIn('zone_id', $zoneIds))
                ->pluck('id')->toArray();
            $servicemanCount = Serviceman::whereIn('provider_id', $providerIds)->count();
        }

        $totalReach = $customerCount + $partnerCount + $providerCount + $servicemanCount;

        return response()->json([
            'success' => true,
            'estimated_reach' => $totalReach,
            'customer_count' => $customerCount,
            'partner_count' => $partnerCount,
            'provider_count' => $providerCount,
            'serviceman_count' => $servicemanCount,
        ]);
    }

    /**
     * Web Click Tracking for Push Notifications
     */
    public function trackClickWeb(Request $request): \Illuminate\Http\JsonResponse
    {
        $notificationId = $request->input('notification_id');
        if (!empty($notificationId)) {
            $notif = $this->pushNotification->find($notificationId);
            if ($notif) {
                $notif->increment('click_count');
                return response()->json([
                    'status' => 'success',
                    'notification_id' => $notif->id,
                    'click_count' => $notif->click_count
                ]);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
    }

    private function convertToSystemTimezone(?string $timeString, ?int $userTzOffset = null, string $type = 'datetime'): ?string
    {
        if (empty($timeString)) {
            return null;
        }

        try {
            if ($type === 'time') {
                return Carbon::parse($timeString)->format('H:i:s');
            } else {
                return Carbon::parse($timeString)->format('Y-m-d H:i:s');
            }
        } catch (\Exception $e) {
            return $timeString;
        }
    }
}
