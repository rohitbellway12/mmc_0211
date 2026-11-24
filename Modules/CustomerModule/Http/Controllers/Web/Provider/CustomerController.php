<?php

namespace Modules\CustomerModule\Http\Controllers\Web\Provider;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Entities\BookingEstimate;
use Modules\CustomerModule\Emails\CustomerRegistrationMail;
use Modules\CustomerModule\Entities\CustomerDocument;
use Modules\CustomerModule\Entities\CustomerDocumentType;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAddress;
use Modules\UserManagement\Entities\UserVerification;

class CustomerController extends Controller
{
    protected User $user;
    protected UserAddress $address;
    protected UserVerification $userVerification;

    public function __construct(User $user, UserAddress $address, UserVerification $userVerification)
    {
        $this->user = $user;
        $this->address = $address;
        $this->userVerification = $userVerification;

        // Security Guard: Only Partners (is_partner = 1) can access customer management
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            $provider = $user?->provider;

            if (!$provider || ($provider->is_partner ?? 0) != 1) {
                Toastr::error(translate('Access denied. This feature is only available for partners.'));
                return redirect()->route('provider.dashboard');
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of customers for partner.
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $provider = $request->user()->provider;
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');

        $queryParam = ['search' => $search, 'status' => $status];

        $customers = $this->user->whereIn('user_type', CUSTOMER_USER_TYPES)
            ->with(['addresses', 'estimates' => function ($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            }])
            ->when(!empty($search), function ($query) use ($search) {
                $keys = explode(' ', $search);
                return $query->where(function ($q) use ($keys) {
                    foreach ($keys as $key) {
                        $q->orWhere('first_name', 'LIKE', '%' . $key . '%')
                          ->orWhere('last_name', 'LIKE', '%' . $key . '%')
                          ->orWhere('phone', 'LIKE', '%' . $key . '%')
                          ->orWhere('email', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('is_active', $status === 'active' ? 1 : 0);
            })
            ->latest()
            ->paginate(pagination_limit())
            ->appends($queryParam);

        return view('customermodule::provider.list', compact('customers', 'search', 'status', 'queryParam'));
    }

    /**
     * Show form to create a new customer.
     * @param Request $request
     * @return Application|Factory|View
     */
    public function create(Request $request): View|Factory|Application
    {
        $documentTypes = CustomerDocumentType::where('is_active', 1)->latest()->get();
        return view('customermodule::provider.create', compact('documentTypes'));
    }

    /**
     * Store a newly created customer.
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:users,phone',
            'password' => 'nullable|min:6',
            'confirm_password' => 'nullable|same:password',
            'gender' => 'nullable|in:male,female,others',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10000',
            'documents.*' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf,doc,docx|max:15000',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'house' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:100',
        ], [
            'email.unique' => translate('Email already taken'),
            'phone.unique' => translate('Phone already taken'),
            'confirm_password.same' => translate('Password confirmation does not match'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            Toastr::error($validator->errors()->first());
            return back()->withErrors($validator)->withInput();
        }

        $password = $request->filled('password') ? $request->password : '12345678';

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->profile_image = $request->hasFile('profile_image')
            ? file_uploader('user/profile_image/', 'png', $request->file('profile_image'))
            : 'default.png';
        $user->date_of_birth = $request->date_of_birth;
        $user->gender = $request->gender ?? 'male';
        $user->password = bcrypt($password);
        $user->user_type = 'customer';
        $user->is_active = 1;
        $user->referred_by = $request->user()->id; // Track that partner created this customer
        $user->save();

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $docTypeId => $file) {
                if ($file && $file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $storedName = file_uploader('customer/document/', $extension, $file);
                    CustomerDocument::create([
                        'user_id' => $user->id,
                        'document_type_id' => $docTypeId,
                        'file_path' => $storedName,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        }

        // Save address if provided
        $savedAddress = null;
        if ($request->filled('address') || $request->filled('city') || $request->filled('street') || $request->filled('zip_code')) {
            $formattedAddress = $request->address;
            if (empty($formattedAddress)) {
                $parts = array_filter([$request->house, $request->floor, $request->street, $request->city, $request->zip_code, $request->country]);
                $formattedAddress = implode(', ', $parts);
            }

            $userAddress = new UserAddress();
            $userAddress->user_id = $user->id;
            $userAddress->address = $formattedAddress;
            $userAddress->city = $request->city;
            $userAddress->street = $request->street;
            $userAddress->zip_code = $request->zip_code;
            $userAddress->country = $request->country ?? 'United Kingdom';
            $userAddress->house = $request->house;
            $userAddress->floor = $request->floor;
            $userAddress->contact_person_name = $user->first_name . ' ' . $user->last_name;
            $userAddress->contact_person_number = $user->phone;
            $userAddress->address_type = 'service';
            $userAddress->zone_id = $request->user()->provider->zone_id ?? null;
            $userAddress->save();

            $savedAddress = $userAddress;
        }

        // Welcome Email & OTP Verification
        try {
            $otp = env('APP_ENV') != 'live' ? '1234' : rand(1000, 9999);
            $webUrl = business_config('web_url', 'landing_button_and_links');
            $token = base64_encode(json_encode(["identity" => $user->email, "identity_type" => "email", "otp" => $otp, "from_url" => 1]));

            $url = ($webUrl && isset($webUrl->live_values))
                ? rtrim($webUrl->live_values, '/') . '/change-password?token=' . urlencode($token)
                : url('/');

            $emailStatus = business_config('email_config_status', 'email_config')?->live_values;
            if ($emailStatus) {
                try {
                    Mail::to($user->email)->send(new CustomerRegistrationMail($user, $password, $otp, $url));
                } catch (\Exception $e) {
                    info('Partner customer creation email sending failed: ' . $e->getMessage());
                }
            }

            $this->userVerification->updateOrCreate([
                'identity' => $user->email,
                'identity_type' => "email"
            ], [
                'identity' => $user->email,
                'identity_type' => 'email',
                'user_id' => null,
                'otp' => $otp,
                'expires_at' => now()->addMinute(60),
            ]);
        } catch (\Exception $e) {
            info('Partner customer verification generation failed: ' . $e->getMessage());
        }

        // Handle AJAX submission (e.g. from modal on estimate/create page)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => translate('Customer created successfully!'),
                'customer' => [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $savedAddress?->address ?? '',
                    'city' => $savedAddress?->city ?? '',
                    'zip_code' => $savedAddress?->zip_code ?? '',
                    'country' => $savedAddress?->country ?? '',
                    'house' => $savedAddress?->house ?? '',
                    'floor' => $savedAddress?->floor ?? '',
                    'street' => $savedAddress?->street ?? '',
                ]
            ], 200);
        }

        Toastr::success(translate('Customer created successfully!'));

        // If 'create_estimate' parameter is present, redirect directly to create estimate for this customer
        if ($request->get('action') === 'create_estimate') {
            return redirect()->route('provider.estimate.create', ['customer_id' => $user->id]);
        }

        return redirect()->route('provider.customer.list');
    }

    /**
     * Display the specified customer details.
     * @param string $id
     * @param Request $request
     * @return Application|Factory|View
     */
    public function show(string $id, Request $request): View|Factory|Application
    {
        $provider = $request->user()->provider;
        $customer = $this->user->where('id', $id)
            ->whereIn('user_type', CUSTOMER_USER_TYPES)
            ->firstOrFail();

        $totalAddressesCount = UserAddress::where('user_id', $id)->count();
        $recentAddresses = UserAddress::where('user_id', $id)->latest()->take(4)->get();
        $allAddresses = UserAddress::where('user_id', $id)->latest()->take(100)->get();

        $estimates = BookingEstimate::where('customer_id', $id)
            ->where('provider_id', $provider->id)
            ->latest()
            ->get();

        $bookings = Booking::where('customer_id', $id)
            ->where('provider_id', $provider->id)
            ->latest()
            ->get();

        return view('customermodule::provider.detail', compact(
            'customer',
            'totalAddressesCount',
            'recentAddresses',
            'allAddresses',
            'estimates',
            'bookings'
        ));
    }

    /**
     * Show the form for editing the customer.
     * @param string $id
     * @param Request $request
     * @return Application|Factory|View
     */
    public function edit(string $id, Request $request): View|Factory|Application
    {
        $customer = $this->user->where('id', $id)
            ->whereIn('user_type', CUSTOMER_USER_TYPES)
            ->with(['addresses', 'documents.documentType'])
            ->firstOrFail();

        $documentTypes = CustomerDocumentType::where('is_active', 1)->latest()->get();

        return view('customermodule::provider.edit', compact('customer', 'documentTypes'));
    }

    /**
     * Update the customer details.
     * @param string $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(string $id, Request $request): RedirectResponse
    {
        $customer = $this->user->where('id', $id)
            ->whereIn('user_type', CUSTOMER_USER_TYPES)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . $customer->id,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:users,phone,' . $customer->id,
            'gender' => 'nullable|in:male,female,others',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10000',
            'documents.*' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf,doc,docx|max:15000',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'house' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:100',
        ], [
            'email.unique' => translate('Email already taken'),
            'phone.unique' => translate('Phone already taken'),
        ]);

        if ($validator->fails()) {
            Toastr::error($validator->errors()->first());
            return back()->withErrors($validator)->withInput();
        }

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        if ($request->hasFile('profile_image')) {
            $customer->profile_image = file_uploader('user/profile_image/', 'png', $request->file('profile_image'));
        }
        $customer->date_of_birth = $request->date_of_birth;
        $customer->gender = $request->gender ?? $customer->gender;
        $customer->save();

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $docTypeId => $file) {
                if ($file && $file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $storedName = file_uploader('customer/document/', $extension, $file);
                    CustomerDocument::updateOrCreate(
                        [
                            'user_id' => $customer->id,
                            'document_type_id' => $docTypeId,
                        ],
                        [
                            'file_path' => $storedName,
                            'file_name' => $file->getClientOriginalName(),
                        ]
                    );
                }
            }
        }

        if ($request->filled('address') || $request->filled('city') || $request->filled('street') || $request->filled('zip_code')) {
            $formattedAddress = $request->address;
            if (empty($formattedAddress)) {
                $parts = array_filter([$request->house, $request->floor, $request->street, $request->city, $request->zip_code, $request->country]);
                $formattedAddress = implode(', ', $parts);
            }

            $userAddress = UserAddress::where('user_id', $customer->id)->first();
            if (!$userAddress) {
                $userAddress = new UserAddress();
                $userAddress->user_id = $customer->id;
            }
            $userAddress->address = $formattedAddress;
            $userAddress->city = $request->city;
            $userAddress->street = $request->street;
            $userAddress->zip_code = $request->zip_code;
            $userAddress->country = $request->country ?? 'UK';
            $userAddress->house = $request->house;
            $userAddress->floor = $request->floor;
            $userAddress->address_label = $request->address_label ?? 'Service Address';
            $userAddress->address_type = 'service';
            $userAddress->contact_person_name = $customer->first_name . ' ' . $customer->last_name;
            $userAddress->contact_person_number = $customer->phone;
            $userAddress->save();
        }

        Toastr::success(translate('Customer details updated successfully'));
        return redirect()->route('provider.customer.detail', [$customer->id]);
    }
}
