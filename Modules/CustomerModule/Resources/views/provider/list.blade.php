@extends('providermanagement::layouts.master')

@section('title', translate('Partner_Customers'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-4">
                <div>
                    <h2 class="page-title mb-1">{{ translate('Customers_Directory') }}</h2>
                    <p class="text-muted fz-14 mb-0">{{ translate('View, onboard and manage clients to send quotations and track bookings.') }}</p>
                </div>
                <div>
                    <a href="{{ route('provider.customer.create') }}" class="btn btn--primary d-flex align-items-center gap-2">
                        <span class="material-icons">person_add</span>
                        {{ translate('Add_New_Customer') }}
                    </a>
                </div>
            </div>

            {{-- Search & Filter Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <span class="material-icons text-muted">search</span>
                                </span>
                                <input type="search" class="form-control border-start-0 ps-0" name="search"
                                       placeholder="{{ translate('Search by customer name, email or phone...') }}"
                                       value="{{ $search }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ translate('All Statuses') }}</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>{{ translate('Active Only') }}</option>
                                <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>{{ translate('Inactive Only') }}</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn--primary flex-grow-1">
                                {{ translate('Filter') }}
                            </button>
                            <a href="{{ route('provider.customer.list') }}" class="btn btn--secondary">
                                {{ translate('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Customers Table Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">{{ translate('SL') }}</th>
                                    <th>{{ translate('Customer_Info') }}</th>
                                    <th>{{ translate('Contact_Details') }}</th>
                                    <th>{{ translate('Address') }}</th>
                                    <th>{{ translate('Quotations') }}</th>
                                    <th>{{ translate('Status') }}</th>
                                    <th class="text-end pe-4">{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $key => $customer)
                                    <tr>
                                        <td class="ps-4 text-muted fz-13">
                                            {{ ($customers->currentPage() - 1) * $customers->perPage() + $key + 1 }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar avatar-md rounded-circle overflow-hidden bg-light border flex-shrink-0" style="width: 44px; height: 44px;">
                                                    <img src="{{ $customer->profile_image_full_path ?? asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                         alt="{{ $customer->first_name }}" class="w-100 h-100 object-fit-cover"
                                                         onerror="this.src='{{ asset('public/assets/admin-module/img/media/upload-file.png') }}'">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fz-14">
                                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                                    </div>
                                                    <div class="text-muted fz-12">
                                                        {{ translate('Joined') }}: {{ $customer->created_at ? $customer->created_at->format('d M Y') : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1 fz-13">
                                                <div class="d-flex align-items-center gap-1 text-dark">
                                                    <span class="material-icons text-muted fz-16">phone</span>
                                                    <span>{{ $customer->phone }}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-1 text-muted">
                                                    <span class="material-icons text-muted fz-16">mail</span>
                                                    <span>{{ $customer->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php($latestAddress = $customer->addresses->first())
                                            @if($latestAddress)
                                                <div class="fz-13 text-dark text-truncate" style="max-width: 220px;" title="{{ $latestAddress->address }}">
                                                    {{ $latestAddress->address }}
                                                </div>
                                                @if($latestAddress->city || $latestAddress->zip_code)
                                                    <div class="fz-12 text-muted">
                                                        {{ implode(', ', array_filter([$latestAddress->city, $latestAddress->zip_code])) }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="badge bg-light text-muted fz-11">{{ translate('No address added') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1 fz-12">
                                                    {{ $customer->estimates ? $customer->estimates->count() : 0 }} {{ translate('Quotes') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($customer->is_active)
                                                <span class="badge bg-success text-white px-2 py-1 fz-11">{{ translate('Active') }}</span>
                                            @else
                                                <span class="badge bg-danger text-white px-2 py-1 fz-11">{{ translate('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <a href="{{ route('provider.estimate.create', ['customer_id' => $customer->id]) }}"
                                                   class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                                   title="{{ translate('Create Quotation for this client') }}">
                                                    <span class="material-icons fz-16">request_quote</span>
                                                    <span>{{ translate('Quote') }}</span>
                                                </a>
                                                <a href="{{ route('provider.customer.detail', [$customer->id]) }}"
                                                   class="btn btn-sm btn-light d-flex align-items-center text-muted"
                                                   title="{{ translate('View Client Details') }}">
                                                    <span class="material-icons fz-18">visibility</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <span class="material-icons text-muted" style="font-size: 48px;">person_off</span>
                                                <h5 class="mt-2 text-dark">{{ translate('No Customers Found') }}</h5>
                                                <p class="text-muted fz-13 mb-3">{{ translate('You have not added any customers yet, or no customers matched your search.') }}</p>
                                                <a href="{{ route('provider.customer.create') }}" class="btn btn--primary">
                                                    <span class="material-icons">person_add</span>
                                                    {{ translate('Add First Customer') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($customers->hasPages())
                    <div class="card-footer bg-transparent border-top py-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted fz-13">
                            {{ translate('Showing') }} {{ $customers->firstItem() ?? 0 }} {{ translate('to') }} {{ $customers->lastItem() ?? 0 }} {{ translate('of') }} {{ $customers->total() }} {{ translate('entries') }}
                        </div>
                        <div>
                            {!! $customers->links() !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
