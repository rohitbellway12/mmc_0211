<?php

namespace Modules\CustomerModule\Http\Controllers\Api\V1\Provider;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Entities\BookingEstimate;
use Modules\ProviderManagement\Entities\Provider;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAddress;

class CustomerController extends Controller
{
    private User $user;
    private Booking $booking;
    private BookingEstimate $estimate;
    private UserAddress $address;
    private Provider $provider;

    public function __construct(User $user, Booking $booking, BookingEstimate $estimate, UserAddress $address, Provider $provider)
    {
        $this->user = $user;
        $this->booking = $booking;
        $this->estimate = $estimate;
        $this->address = $address;
        $this->provider = $provider;
    }

    /**
     * Helper to verify if the authenticated provider is a partner (is_partner == 1).
     */
    protected function getAuthorizedPartner(Request $request): ?Provider
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }

        $provider = $user->provider ?? $this->provider->where('user_id', $user->id)->first();
        if (!$provider || (int)$provider->is_partner !== 1) {
            return null;
        }

        return $provider;
    }

    /**
     * Display a listing of customers for partner.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $provider = $this->getAuthorizedPartner($request);
        if (!$provider) {
            return response()->json(response_formatter(DEFAULT_403, null, [
                ['error_code' => 'access_denied', 'message' => translate('Access denied. This feature is only available for partners.')]
            ]), 403);
        }

        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|numeric|min:1|max:200',
            'offset' => 'nullable|numeric|min:1|max:100000',
            'status' => 'nullable|in:active,inactive,all',
            'string' => 'nullable|string',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $limit = (int) $request->input('limit', 20);
        $offset = (int) $request->input('offset', 1);
        $search = $request->input('search', $request->input('string'));

        $customers = $this->user->withCount([
            'bookings' => function ($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            },
            'estimates' => function ($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            }
        ])
        ->whereIn('user_type', CUSTOMER_USER_TYPES)
        ->when($search, function ($query) use ($search) {
            $decoded = base64_decode($search, true);
            $searchTerm = ($decoded !== false && base64_encode($decoded) === $search) ? $decoded : $search;
            $keys = array_filter(explode(' ', $searchTerm));

            return $query->where(function ($q) use ($keys) {
                foreach ($keys as $key) {
                    $q->orWhere('first_name', 'LIKE', '%' . $key . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $key . '%')
                      ->orWhere('phone', 'LIKE', '%' . $key . '%')
                      ->orWhere('email', 'LIKE', '%' . $key . '%');
                }
            });
        })
        ->when($request->filled('status') && $request->status !== 'all', function ($query) use ($request) {
            return $query->where('is_active', $request->status === 'active' ? 1 : 0);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($limit, ['*'], 'offset', $offset);

        return response()->json(response_formatter(DEFAULT_200, $customers), 200);
    }

    /**
     * Store a newly created customer by partner.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $provider = $this->getAuthorizedPartner($request);
        if (!$provider) {
            return response()->json(response_formatter(DEFAULT_403, null, [
                ['error_code' => 'access_denied', 'message' => translate('Access denied. This feature is only available for partners.')]
            ]), 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:male,female,others',
            'date_of_birth' => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $user->gender = $request->gender ?? 'male';
        $user->date_of_birth = $request->date_of_birth;
        $user->user_type = 'customer';
        $user->is_active = 1;

        if ($request->hasFile('profile_image')) {
            $user->profile_image = file_uploader('user/profile_image/', 'png', $request->file('profile_image'));
        } else {
            $user->profile_image = 'default.png';
        }

        $user->save();

        return response()->json(response_formatter(DEFAULT_STORE_200, $user), 200);
    }

    /**
     * Show customer details, stats, recent addresses, and history with this partner.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $provider = $this->getAuthorizedPartner($request);
        if (!$provider) {
            return response()->json(response_formatter(DEFAULT_403, null, [
                ['error_code' => 'access_denied', 'message' => translate('Access denied. This feature is only available for partners.')]
            ]), 403);
        }

        $customer = $this->user->whereIn('user_type', CUSTOMER_USER_TYPES)->find($id);
        if (!$customer) {
            return response()->json(response_formatter(DEFAULT_404), 404);
        }

        $totalAddressesCount = $this->address->where('user_id', $customer->id)->count();
        $recentAddresses = $this->address->where('user_id', $customer->id)->latest()->take(5)->get();

        // Bookings with THIS partner
        $bookingsQuery = $this->booking->where('customer_id', $customer->id)->where('provider_id', $provider->id);
        $totalBookingsCount = (clone $bookingsQuery)->count();
        $recentBookings = (clone $bookingsQuery)->latest()->take(10)->get();

        // Estimates with THIS partner
        $estimatesQuery = $this->estimate->where('customer_id', $customer->id)->where('provider_id', $provider->id);
        $totalEstimatesCount = (clone $estimatesQuery)->count();
        $recentEstimates = (clone $estimatesQuery)->latest()->take(10)->get();

        $data = [
            'customer' => $customer,
            'stats' => [
                'total_bookings_with_you' => $totalBookingsCount,
                'total_estimates_with_you' => $totalEstimatesCount,
                'total_addresses' => $totalAddressesCount,
            ],
            'recent_addresses' => $recentAddresses,
            'recent_bookings' => $recentBookings,
            'recent_estimates' => $recentEstimates,
        ];

        return response()->json(response_formatter(DEFAULT_200, $data), 200);
    }

    /**
     * Get paginated bookings history of this customer specifically with this partner.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function bookings(Request $request, string $id): JsonResponse
    {
        $provider = $this->getAuthorizedPartner($request);
        if (!$provider) {
            return response()->json(response_formatter(DEFAULT_403, null, [
                ['error_code' => 'access_denied', 'message' => translate('Access denied. This feature is only available for partners.')]
            ]), 403);
        }

        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|numeric|min:1|max:200',
            'offset' => 'nullable|numeric|min:1|max:100000',
            'status' => 'nullable|string',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $limit = (int) $request->input('limit', 20);
        $offset = (int) $request->input('offset', 1);

        $bookings = $this->booking->where('customer_id', $id)
            ->where('provider_id', $provider->id)
            ->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
                $q->where('booking_status', $request->status);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($sub) use ($s) {
                    $sub->orWhere('readable_id', 'LIKE', "%$s%")
                        ->orWhere('id', 'LIKE', "%$s%");
                });
            })
            ->latest()
            ->paginate($limit, ['*'], 'offset', $offset);

        return response()->json(response_formatter(DEFAULT_200, $bookings), 200);
    }

    /**
     * Get paginated quotations/estimates history of this customer specifically with this partner.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function estimates(Request $request, string $id): JsonResponse
    {
        $provider = $this->getAuthorizedPartner($request);
        if (!$provider) {
            return response()->json(response_formatter(DEFAULT_403, null, [
                ['error_code' => 'access_denied', 'message' => translate('Access denied. This feature is only available for partners.')]
            ]), 403);
        }

        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|numeric|min:1|max:200',
            'offset' => 'nullable|numeric|min:1|max:100000',
            'status' => 'nullable|string',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $limit = (int) $request->input('limit', 20);
        $offset = (int) $request->input('offset', 1);

        $estimates = $this->estimate->where('customer_id', $id)
            ->where('provider_id', $provider->id)
            ->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($sub) use ($s) {
                    $sub->orWhere('estimate_number', 'LIKE', "%$s%")
                        ->orWhere('id', 'LIKE', "%$s%")
                        ->orWhere('car_model', 'LIKE', "%$s%");
                });
            })
            ->latest()
            ->paginate($limit, ['*'], 'offset', $offset);

        return response()->json(response_formatter(DEFAULT_200, $estimates), 200);
    }

    /**
     * Get saved addresses for a customer.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function addresses(Request $request, string $id): JsonResponse
    {
        $provider = $this->getAuthorizedPartner($request);
        if (!$provider) {
            return response()->json(response_formatter(DEFAULT_403, null, [
                ['error_code' => 'access_denied', 'message' => translate('Access denied. This feature is only available for partners.')]
            ]), 403);
        }

        $limit = (int) $request->input('limit', 20);
        $offset = (int) $request->input('offset', 1);

        $addresses = $this->address->where('user_id', $id)
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($sub) use ($s) {
                    $sub->orWhere('address', 'LIKE', "%$s%")
                        ->orWhere('city', 'LIKE', "%$s%")
                        ->orWhere('zip_code', 'LIKE', "%$s%");
                });
            })
            ->latest()
            ->paginate($limit, ['*'], 'offset', $offset);

        return response()->json(response_formatter(DEFAULT_200, $addresses), 200);
    }
}
