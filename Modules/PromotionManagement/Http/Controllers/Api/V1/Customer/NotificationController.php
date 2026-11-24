<?php

namespace Modules\PromotionManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\PromotionManagement\Entities\PushNotification;

class NotificationController extends Controller
{
    private PushNotification $pushNotification;

    public function __construct(PushNotification $pushNotification)
    {
        $this->pushNotification = $pushNotification;
    }

    /**
     * Display a listing of the resource for Customer.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $customer = auth('api')->user();
        if (!$customer) {
            return response()->json(response_formatter(DEFAULT_401), 401);
        }

        $createdAt = $customer->created_at;
        $customer_user_id = $customer->id;
        $zoneId = Config::get('zone_id') ?: $request->header('zoneid');

        $readIds = DB::table('push_notification_reads')
            ->where('user_id', $customer_user_id)
            ->pluck('notification_id')
            ->toArray();

        $query = $this->pushNotification->ofStatus(1)
            ->when(!empty($zoneId), function ($q) use ($zoneId) {
                $q->where(function ($sub) use ($zoneId) {
                    $sub->whereJsonContains('zone_ids', $zoneId)
                        ->orWhereJsonContains('zone_ids', 'all');
                });
            })
            ->where(function ($query) use ($customer_user_id) {
                $query->whereDoesntHave('pushNotificationUser')
                    ->orWhereHas('pushNotificationUser', function ($query) use ($customer_user_id) {
                        $query->where('user_id', $customer_user_id);
                    });
            })
            ->when($createdAt, function ($query) use ($createdAt) {
                $query->where('created_at', '>=', $createdAt);
            })
            ->where(function ($q) {
                $q->whereJsonContains('to_users', 'customer')
                  ->orWhereJsonContains('to_users', 'all')
                  ->orWhere('to_users', 'like', '%"customer"%');
            });

        $unreadCount = (clone $query)->whereNotIn('id', $readIds)->count();

        $paginated = $query->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])
            ->withPath('');

        $paginated->getCollection()->transform(function ($item) use ($readIds) {
            $item->is_read = in_array($item->id, $readIds) ? 1 : 0;
            $item->on_tap_action = $item->on_tap_action ?? 'general';
            $item->category_id = $item->category_id ?? null;
            return $item;
        });

        $result = $paginated->toArray();
        $result['unread_count'] = $unreadCount;

        return response()->json(response_formatter(DEFAULT_200, $result), 200);
    }

    /**
     * Mark an individual notification as read for Customer.
     * @param Request $request
     * @return JsonResponse
     */
    public function markRead(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $customer = auth('api')->user();
        if (!$customer) {
            return response()->json(response_formatter(DEFAULT_401), 401);
        }

        $notificationId = $request->input('notification_id');

        DB::table('push_notification_reads')->insertOrIgnore([
            'user_id' => $customer->id,
            'notification_id' => $notificationId,
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->input('track_click', false)) {
            $this->pushNotification->where('id', $notificationId)->increment('click_count');
        }

        $unreadCount = $this->getCustomerUnreadCount($customer, $request);

        return response()->json(response_formatter(DEFAULT_200, [
            'notification_id' => $notificationId,
            'is_read' => 1,
            'unread_count' => $unreadCount,
        ]), 200);
    }

    /**
     * Mark all notifications as read for Customer.
     * @param Request $request
     * @return JsonResponse
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $customer = auth('api')->user();
        if (!$customer) {
            return response()->json(response_formatter(DEFAULT_401), 401);
        }

        $zoneId = Config::get('zone_id') ?: $request->header('zoneid');
        $customer_user_id = $customer->id;

        $notificationIds = $this->pushNotification->ofStatus(1)
            ->when(!empty($zoneId), function ($q) use ($zoneId) {
                $q->where(function ($sub) use ($zoneId) {
                    $sub->whereJsonContains('zone_ids', $zoneId)
                        ->orWhereJsonContains('zone_ids', 'all');
                });
            })
            ->where(function ($query) use ($customer_user_id) {
                $query->whereDoesntHave('pushNotificationUser')
                    ->orWhereHas('pushNotificationUser', function ($query) use ($customer_user_id) {
                        $query->where('user_id', $customer_user_id);
                    });
            })
            ->where(function ($q) {
                $q->whereJsonContains('to_users', 'customer')
                  ->orWhereJsonContains('to_users', 'all')
                  ->orWhere('to_users', 'like', '%"customer"%');
            })
            ->pluck('id')
            ->toArray();

        $records = [];
        $now = now();
        foreach ($notificationIds as $id) {
            $records[] = [
                'user_id' => $customer->id,
                'notification_id' => $id,
                'read_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($records)) {
            DB::table('push_notification_reads')->insertOrIgnore($records);
        }

        return response()->json(response_formatter(DEFAULT_200, [
            'unread_count' => 0,
            'message' => translate('All notifications marked as read'),
        ]), 200);
    }

    /**
     * Calculate unread notifications count for a Customer.
     * @param $customer
     * @param Request $request
     * @return int
     */
    private function getCustomerUnreadCount($customer, Request $request): int
    {
        $zoneId = Config::get('zone_id') ?: $request->header('zoneid');
        $customer_user_id = $customer->id;
        $readIds = DB::table('push_notification_reads')
            ->where('user_id', $customer_user_id)
            ->pluck('notification_id')
            ->toArray();

        return $this->pushNotification->ofStatus(1)
            ->when(!empty($zoneId), function ($q) use ($zoneId) {
                $q->where(function ($sub) use ($zoneId) {
                    $sub->whereJsonContains('zone_ids', $zoneId)
                        ->orWhereJsonContains('zone_ids', 'all');
                });
            })
            ->where(function ($query) use ($customer_user_id) {
                $query->whereDoesntHave('pushNotificationUser')
                    ->orWhereHas('pushNotificationUser', function ($query) use ($customer_user_id) {
                        $query->where('user_id', $customer_user_id);
                    });
            })
            ->where(function ($q) {
                $q->whereJsonContains('to_users', 'customer')
                  ->orWhereJsonContains('to_users', 'all')
                  ->orWhere('to_users', 'like', '%"customer"%');
            })
            ->whereNotIn('id', $readIds)
            ->count();
    }

    /**
     * Record tap/click on a push notification.
     * @param Request $request
     * @return JsonResponse
     */
    public function trackClick(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $notification = $this->pushNotification->find($request->input('notification_id'));
        if ($notification) {
            $notification->increment('click_count');

            // If user is authenticated, also mark as read
            $customer = auth('api')->user();
            if ($customer) {
                DB::table('push_notification_reads')->insertOrIgnore([
                    'user_id' => $customer->id,
                    'notification_id' => $notification->id,
                    'read_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(response_formatter(DEFAULT_200, [
                'notification_id' => $notification->id,
                'click_count' => $notification->click_count,
            ]), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 404);
    }
}

