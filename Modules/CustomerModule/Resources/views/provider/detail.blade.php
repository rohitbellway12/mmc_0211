@extends('providermanagement::layouts.master')

@section('title', translate('Customer_Details') . ' - ' . $customer->first_name . ' ' . $customer->last_name)

@push('css_or_js')
<style>
    .stat-tab-trigger {
        transition: all 0.2s ease;
        border: 1px solid #edf2f7;
        cursor: pointer;
    }
    .stat-tab-trigger:hover {
        background-color: #f1f5f9 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .sticky-table-head th {
        position: sticky;
        top: 0;
        background-color: #f8fafc;
        z-index: 2;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .nav--tabs .nav-link {
        font-weight: 500;
        padding: 0.65rem 1.25rem;
    }
    .nav--tabs .nav-link.active {
        font-weight: 600;
    }
</style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            {{-- Top Page Title & Actions --}}
            <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-4">
                <div>
                    <h2 class="page-title mb-1">{{ translate('Customer_Profile') }}</h2>
                    <p class="text-muted fz-14 mb-0">{{ translate('Client details, saved addresses and history of bookings & quotations.') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('provider.estimate.create', ['customer_id' => $customer->id]) }}" class="btn btn--primary d-flex align-items-center gap-2">
                        <span class="material-icons">request_quote</span>
                        {{ translate('Create_Quotation') }}
                    </a>
                    <a href="{{ route('provider.customer.list') }}" class="btn btn--secondary d-flex align-items-center gap-2">
                        <span class="material-icons">arrow_back</span>
                        {{ translate('Back_to_List') }}
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Left Column: Customer Profile & Quick Interactive Stat Cards --}}
                <div class="col-lg-4">
                    {{-- Profile Card --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="avatar avatar-xl rounded-circle overflow-hidden mx-auto mb-3 border shadow-xs" style="width: 90px; height: 90px;">
                                <img src="{{ $customer->profile_image_full_path ?? asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                     alt="{{ $customer->first_name }}" class="w-100 h-100 object-fit-cover"
                                     onerror="this.src='{{ asset('public/assets/admin-module/img/media/upload-file.png') }}'">
                            </div>
                            <h4 class="mb-1 text-dark fw-bold">{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                            <p class="text-muted fz-12 mb-3">
                                {{ translate('Member since') }} {{ $customer->created_at ? $customer->created_at->format('d M Y') : 'N/A' }}
                            </p>

                            <div class="d-flex justify-content-center gap-2 mb-4">
                                @if($customer->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fz-12 fw-medium">
                                        <span class="material-icons fz-12 align-middle">check_circle</span> {{ translate('Active Client') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fz-12 fw-medium">
                                        {{ translate('Inactive') }}
                                    </span>
                                @endif
                            </div>

                            <hr class="opacity-10 my-3">

                            <div class="text-start d-flex flex-column gap-3 fz-14">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons text-primary p-2 bg-light rounded-circle fz-18">phone</span>
                                    <div>
                                        <div class="text-muted fz-11 text-uppercase">{{ translate('Phone') }}</div>
                                        <a href="tel:{{ $customer->phone }}" class="fw-semibold text-dark text-decoration-none">{{ $customer->phone }}</a>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons text-primary p-2 bg-light rounded-circle fz-18">mail</span>
                                    <div class="overflow-hidden">
                                        <div class="text-muted fz-11 text-uppercase">{{ translate('Email') }}</div>
                                        <a href="mailto:{{ $customer->email }}" class="fw-semibold text-dark text-break text-decoration-none">{{ $customer->email }}</a>
                                    </div>
                                </div>

                                @if($customer->gender)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-icons text-primary p-2 bg-light rounded-circle fz-18">wc</span>
                                        <div>
                                            <div class="text-muted fz-11 text-uppercase">{{ translate('Gender') }}</div>
                                            <div class="fw-semibold text-dark text-capitalize">{{ $customer->gender }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($customer->date_of_birth)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-icons text-primary p-2 bg-light rounded-circle fz-18">cake</span>
                                        <div>
                                            <div class="text-muted fz-11 text-uppercase">{{ translate('Date_of_Birth') }}</div>
                                            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Quick Interactive Stat Tiles (Clicking switches to the corresponding tab directly) --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <span class="fz-12 fw-semibold text-muted text-uppercase">{{ translate('Quick Navigation') }}</span>
                                <span class="fz-11 text-muted">{{ translate('Click to view') }}</span>
                            </div>
                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light stat-tab-trigger" data-tab-target="#tab-btn-bookings" title="{{ translate('View Bookings') }}">
                                        <div class="fw-bold fz-18 text-success">{{ $bookings->count() }}</div>
                                        <div class="text-muted fz-11">{{ translate('Bookings') }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light stat-tab-trigger" data-tab-target="#tab-btn-quotations" title="{{ translate('View Quotations') }}">
                                        <div class="fw-bold fz-18 text-primary">{{ $estimates->count() }}</div>
                                        <div class="text-muted fz-11">{{ translate('Quotations') }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light stat-tab-trigger" data-tab-target="#tab-btn-addresses" title="{{ translate('View Addresses') }}">
                                        <div class="fw-bold fz-18 text-dark">{{ $totalAddressesCount }}</div>
                                        <div class="text-muted fz-11">{{ translate('Addresses') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Tabbed View (Bookings, Quotations & Addresses) --}}
                <div class="col-lg-8">
                    {{-- Nav Tabs --}}
                    <ul class="nav nav--tabs nav--tabs__style2 mb-3" id="customerDetailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active d-flex align-items-center gap-2" id="tab-btn-bookings"
                                    data-bs-toggle="tab" data-bs-target="#tab-pane-bookings" type="button" role="tab"
                                    aria-controls="tab-pane-bookings" aria-selected="true">
                                <span class="material-icons fz-18">event_note</span>
                                <span>{{ translate('Bookings') }}</span>
                                <span class="badge bg-success text-white rounded-pill px-2 py-1 fz-11">{{ $bookings->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center gap-2" id="tab-btn-quotations"
                                    data-bs-toggle="tab" data-bs-target="#tab-pane-quotations" type="button" role="tab"
                                    aria-controls="tab-pane-quotations" aria-selected="false">
                                <span class="material-icons fz-18">request_quote</span>
                                <span>{{ translate('Quotations') }}</span>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 fz-11">{{ $estimates->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center gap-2" id="tab-btn-addresses"
                                    data-bs-toggle="tab" data-bs-target="#tab-pane-addresses" type="button" role="tab"
                                    aria-controls="tab-pane-addresses" aria-selected="false">
                                <span class="material-icons fz-18">location_on</span>
                                <span>{{ translate('Saved_Addresses') }}</span>
                                <span class="badge bg-secondary text-white rounded-pill px-2 py-1 fz-11">{{ $totalAddressesCount }}</span>
                            </button>
                        </li>
                    </ul>

                    {{-- Tab Contents --}}
                    <div class="tab-content" id="customerDetailTabsContent">

                        {{-- 1. Bookings Tab Pane (Primary default active tab) --}}
                        <div class="tab-pane fade show active" id="tab-pane-bookings" role="tabpanel" aria-labelledby="tab-btn-bookings">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-icons text-success">event_note</span>
                                        <h4 class="card-title mb-0 fz-16">{{ translate('Bookings_History_With_You') }}</h4>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fz-12 fw-medium">
                                            {{ $bookings->count() }} {{ translate('Total') }}
                                        </span>
                                    </div>

                                    @if($bookings->count() > 3)
                                        {{-- Instant search within bookings --}}
                                        <div class="input-group input-group-sm" style="max-width: 220px;">
                                            <span class="input-group-text bg-light border-end-0">
                                                <span class="material-icons text-muted fz-16">search</span>
                                            </span>
                                            <input type="text" id="booking_search_input" class="form-control border-start-0 ps-0"
                                                   placeholder="{{ translate('Search ID / status...') }}">
                                        </div>
                                    @endif
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" id="bookings_table">
                                            <thead class="sticky-table-head">
                                                <tr>
                                                    <th class="ps-3">{{ translate('Booking_ID') }}</th>
                                                    <th>{{ translate('Schedule') }}</th>
                                                    <th>{{ translate('Amount') }}</th>
                                                    <th>{{ translate('Status') }}</th>
                                                    <th class="text-end pe-3">{{ translate('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($bookings as $b)
                                                    <tr class="booking-row" data-search="{{ strtolower(($b->readable_id ?? substr($b->id, 0, 8)) . ' ' . $b->booking_status . ' ' . ($b->total_booking_amount ?? '')) }}">
                                                        <td class="ps-3 fw-bold text-dark">
                                                            <a href="{{ route('provider.booking.details', [$b->id, 'web_page' => 'details']) }}" class="text-dark text-decoration-none hover-primary">
                                                                #{{ $b->readable_id ?? substr($b->id, 0, 8) }}
                                                            </a>
                                                        </td>
                                                        <td class="text-muted fz-12">
                                                            {{ $b->service_schedule ? \Carbon\Carbon::parse($b->service_schedule)->format('d M Y, h:i A') : translate('Flexible') }}
                                                        </td>
                                                        <td class="fw-bold text-dark">
                                                            {{ with_currency_symbol($b->total_booking_amount ?? 0) }}
                                                        </td>
                                                        <td>
                                                            @php($status = strtolower($b->booking_status ?? 'pending'))
                                                            @if($status == 'completed')
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle text-capitalize px-2 py-1">
                                                                    {{ $b->booking_status }}
                                                                </span>
                                                            @elseif($status == 'canceled')
                                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle text-capitalize px-2 py-1">
                                                                    {{ $b->booking_status }}
                                                                </span>
                                                            @elseif($status == 'ongoing')
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle text-capitalize px-2 py-1">
                                                                    {{ $b->booking_status }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-capitalize px-2 py-1">
                                                                    {{ $b->booking_status }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end pe-3">
                                                            <a href="{{ route('provider.booking.details', [$b->id, 'web_page' => 'details']) }}"
                                                               class="btn btn-sm btn-light" title="{{ translate('View Booking') }}">
                                                                <span class="material-icons fz-16">visibility</span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-5 text-muted fz-13">
                                                            <span class="material-icons fz-32 text-muted d-block mb-2">event_busy</span>
                                                            {{ translate('No bookings found for this customer with you yet.') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                <tr id="no_bookings_match" class="d-none">
                                                    <td colspan="5" class="text-center py-4 text-muted fz-13">
                                                        <span class="material-icons fz-24 text-muted d-block mb-1">search_off</span>
                                                        {{ translate('No bookings match your search.') }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Quotations Tab Pane --}}
                        <div class="tab-pane fade" id="tab-pane-quotations" role="tabpanel" aria-labelledby="tab-btn-quotations">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-icons text-primary">request_quote</span>
                                        <h4 class="card-title mb-0 fz-16">{{ translate('Quotations_History_With_You') }}</h4>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fz-12 fw-medium">
                                            {{ $estimates->count() }} {{ translate('Total') }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($estimates->count() > 3)
                                            <div class="input-group input-group-sm" style="max-width: 200px;">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <span class="material-icons text-muted fz-16">search</span>
                                                </span>
                                                <input type="text" id="quote_search_input" class="form-control border-start-0 ps-0"
                                                       placeholder="{{ translate('Search quote...') }}">
                                            </div>
                                        @endif
                                        <a href="{{ route('provider.estimate.create', ['customer_id' => $customer->id]) }}"
                                           class="btn btn-sm btn--primary d-inline-flex align-items-center gap-1">
                                            <span class="material-icons fz-16">add</span>
                                            <span>{{ translate('New_Quote') }}</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" id="quotes_table">
                                            <thead class="sticky-table-head">
                                                <tr>
                                                    <th class="ps-3">{{ translate('Reference') }}</th>
                                                    <th>{{ translate('Service_/_Vehicle') }}</th>
                                                    <th>{{ translate('Total_Amount') }}</th>
                                                    <th>{{ translate('Status') }}</th>
                                                    <th>{{ translate('Date') }}</th>
                                                    <th class="text-end pe-3">{{ translate('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($estimates as $est)
                                                    <tr class="quote-row" data-search="{{ strtolower(($est->estimate_number ?? ('#' . substr($est->id, 0, 8))) . ' ' . ($est->module_type ?? '') . ' ' . ($est->car_model ?? '') . ' ' . ($est->status ?? '')) }}">
                                                        <td class="ps-3 fw-bold text-dark">
                                                            <a href="{{ route('provider.estimate.details', [$est->id]) }}" class="text-dark text-decoration-none hover-primary">
                                                                {{ $est->estimate_number ?? ('#' . substr($est->id, 0, 8)) }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark text-capitalize border">
                                                                {{ str_replace('_', ' ', $est->module_type ?? 'Service') }}
                                                            </span>
                                                            @if($est->car_model)
                                                                <div class="fz-11 text-muted">{{ $est->car_model }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="fw-bold text-dark">
                                                            {{ with_currency_symbol($est->total_amount ?? 0) }}
                                                        </td>
                                                        <td>
                                                            @if($est->status == 'accepted')
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">{{ translate('Accepted') }}</span>
                                                            @elseif($est->status == 'declined')
                                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">{{ translate('Declined') }}</span>
                                                            @else
                                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">{{ translate('Pending') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-muted fz-12">
                                                            {{ $est->created_at ? $est->created_at->format('d M Y') : 'N/A' }}
                                                        </td>
                                                        <td class="text-end pe-3">
                                                            <a href="{{ route('provider.estimate.details', [$est->id]) }}"
                                                               class="btn btn-sm btn-light" title="{{ translate('View Details') }}">
                                                                <span class="material-icons fz-16">visibility</span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-5 text-muted fz-13">
                                                            <span class="material-icons fz-32 text-muted d-block mb-2">post_add</span>
                                                            {{ translate('No quotations sent to this customer yet.') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                <tr id="no_quotes_match" class="d-none">
                                                    <td colspan="6" class="text-center py-4 text-muted fz-13">
                                                        <span class="material-icons fz-24 text-muted d-block mb-1">search_off</span>
                                                        {{ translate('No quotations match your search.') }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Saved Addresses Tab Pane --}}
                        <div class="tab-pane fade" id="tab-pane-addresses" role="tabpanel" aria-labelledby="tab-btn-addresses">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-icons text-primary">location_on</span>
                                        <h4 class="card-title mb-0 fz-16">{{ translate('Saved_Addresses') }}</h4>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fz-12 fw-medium">
                                            {{ $totalAddressesCount }}
                                        </span>
                                    </div>

                                    @if($totalAddressesCount > 1)
                                        <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                                data-bs-toggle="modal" data-bs-target="#allAddressesModal">
                                            <span class="material-icons fz-16">list</span>
                                            <span>{{ translate('Browse_All') }} ({{ $totalAddressesCount }})</span>
                                        </button>
                                    @endif
                                </div>

                                <div class="card-body p-4">
                                    @if($recentAddresses->count() > 0)
                                        {{-- Primary / Latest Address Featured Card --}}
                                        @php($primary = $recentAddresses->first())
                                        <div class="p-3 rounded border bg-light position-relative mb-3" style="border-left: 4px solid var(--primary, #0461A5) !important;">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary text-white fz-11 px-2 py-1">
                                                        {{ translate('Latest_/_Primary') }}
                                                    </span>
                                                    @if($primary->address_label)
                                                        <span class="badge bg-secondary-subtle text-secondary fz-11">
                                                            {{ ucfirst($primary->address_label) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="fw-bold text-dark fz-14 mb-1">{{ $primary->address }}</div>
                                            <div class="text-muted fz-12">
                                                {{ implode(', ', array_filter([$primary->city, $primary->zip_code, $primary->country])) }}
                                            </div>
                                        </div>

                                        {{-- Other recent addresses (up to 3) in compact grid --}}
                                        @if($recentAddresses->count() > 1)
                                            <div class="row g-2 mb-2">
                                                @foreach($recentAddresses->skip(1)->take(3) as $addr)
                                                    <div class="col-sm-6">
                                                        <div class="p-2 rounded border bg-white h-100">
                                                            <div class="d-flex align-items-center gap-1 text-muted fz-11 mb-1">
                                                                <span class="material-icons fz-14 text-muted">place</span>
                                                                <span>{{ ucfirst($addr->address_label ?? 'Address') }}</span>
                                                            </div>
                                                            <div class="text-dark fw-medium fz-13 text-truncate" title="{{ $addr->address }}">
                                                                {{ $addr->address }}
                                                            </div>
                                                            <div class="text-muted fz-11 text-truncate">
                                                                {{ implode(', ', array_filter([$addr->city, $addr->zip_code])) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- More addresses note --}}
                                        @if($totalAddressesCount > 4)
                                            <div class="d-flex justify-content-between align-items-center bg-light rounded p-2 px-3 mt-3">
                                                <span class="text-muted fz-12">
                                                    {{ translate('Showing 4 of') }} <strong>{{ $totalAddressesCount }}</strong> {{ translate('saved addresses.') }}
                                                </span>
                                                <a href="javascript:void(0)" class="text-primary fw-semibold fz-12 text-decoration-none"
                                                   data-bs-toggle="modal" data-bs-target="#allAddressesModal">
                                                    {{ translate('Browse & filter all in popup') }} &rarr;
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4 text-muted fz-13">
                                            <span class="material-icons fz-32 text-muted d-block mb-1">wrong_location</span>
                                            {{ translate('No saved addresses found for this customer.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- All Addresses Popup Modal with Live Search --}}
    @if($totalAddressesCount > 1)
        <div class="modal fade" id="allAddressesModal" tabindex="-1" aria-labelledby="allAddressesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-icons text-primary">location_on</span>
                            <h5 class="modal-title fw-bold" id="allAddressesModalLabel">
                                {{ translate('All_Saved_Addresses') }}
                                <span class="badge bg-primary-subtle text-primary fz-12 ms-1">{{ $totalAddressesCount }}</span>
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- Quick Search Box inside Modal --}}
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light border-end-0">
                                <span class="material-icons text-muted fz-18">search</span>
                            </span>
                            <input type="text" id="address_filter_input" class="form-control border-start-0 ps-0"
                                   placeholder="{{ translate('Type to quickly filter addresses by street, city, postcode...') }}">
                        </div>

                        {{-- Scrollable Container with max height --}}
                        <div class="d-flex flex-column gap-2" id="addresses_list_container" style="max-height: 420px; overflow-y: auto;">
                            @foreach($allAddresses as $index => $addr)
                                <div class="address-item p-3 rounded border bg-light position-relative" data-search="{{ strtolower($addr->address . ' ' . $addr->city . ' ' . $addr->zip_code . ' ' . $addr->country) }}">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary-subtle text-dark fz-11">
                                                #{{ $index + 1 }} {{ ucfirst($addr->address_label ?? 'Address') }}
                                            </span>
                                            @if($index === 0)
                                                <span class="badge bg-success-subtle text-success fz-10">{{ translate('Latest') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="fw-semibold text-dark fz-13 mb-1">{{ $addr->address }}</div>
                                    <div class="text-muted fz-12">
                                        {{ implode(', ', array_filter([$addr->city, $addr->zip_code, $addr->country])) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="no_address_match" class="text-center py-4 text-muted fz-13 d-none">
                            {{ translate('No addresses matched your filter.') }}
                        </div>
                    </div>

                    <div class="modal-footer py-2 px-4 border-top">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ translate('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Interactive Left Stat Tiles -> Switch Right Tabs
            $('.stat-tab-trigger').on('click', function() {
                let targetBtn = $(this).data('tab-target');
                if (targetBtn) {
                    let tabEl = document.querySelector(targetBtn);
                    if (tabEl) {
                        let tabInstance = bootstrap.Tab.getInstance(tabEl) || new bootstrap.Tab(tabEl);
                        tabInstance.show();
                    }
                    if ($(window).width() < 992) {
                        $('html, body').animate({
                            scrollTop: $('#customerDetailTabs').offset().top - 80
                        }, 300);
                    }
                }
            });

            // Live Search inside Bookings Table
            $('#booking_search_input').on('keyup input', function() {
                let query = $(this).val().toLowerCase().trim();
                let $rows = $('#bookings_table tbody tr.booking-row');
                let matches = 0;

                $rows.each(function() {
                    let text = $(this).data('search') || '';
                    if (text.indexOf(query) !== -1) {
                        $(this).removeClass('d-none');
                        matches++;
                    } else {
                        $(this).addClass('d-none');
                    }
                });

                if (matches === 0 && $rows.length > 0) {
                    $('#no_bookings_match').removeClass('d-none');
                } else {
                    $('#no_bookings_match').addClass('d-none');
                }
            });

            // Live Search inside Quotations Table
            $('#quote_search_input').on('keyup input', function() {
                let query = $(this).val().toLowerCase().trim();
                let $rows = $('#quotes_table tbody tr.quote-row');
                let matches = 0;

                $rows.each(function() {
                    let text = $(this).data('search') || '';
                    if (text.indexOf(query) !== -1) {
                        $(this).removeClass('d-none');
                        matches++;
                    } else {
                        $(this).addClass('d-none');
                    }
                });

                if (matches === 0 && $rows.length > 0) {
                    $('#no_quotes_match').removeClass('d-none');
                } else {
                    $('#no_quotes_match').addClass('d-none');
                }
            });

            // Live Search inside Address Modal
            $('#address_filter_input').on('keyup input', function() {
                let query = $(this).val().toLowerCase().trim();
                let $items = $('#addresses_list_container .address-item');
                let matches = 0;

                $items.each(function() {
                    let text = $(this).data('search') || '';
                    if (text.indexOf(query) !== -1) {
                        $(this).removeClass('d-none');
                        matches++;
                    } else {
                        $(this).addClass('d-none');
                    }
                });

                if (matches === 0) {
                    $('#no_address_match').removeClass('d-none');
                } else {
                    $('#no_address_match').addClass('d-none');
                }
            });
        });
    </script>
@endpush
