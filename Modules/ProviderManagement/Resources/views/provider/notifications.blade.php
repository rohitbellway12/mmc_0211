@extends('providermanagement::layouts.new-master')

@section('title', translate('Notifications'))

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-wrap d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                    <h2 class="page-title d-flex align-items-center gap-2">
                        <span class="material-icons text-primary">notifications</span>
                        {{ translate('All Notifications') }}
                    </h2>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill fz-12 fw-semibold border">
                            {{ translate('Total') }}: {{ $notifications->total() }}
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 btn-mark-all-read">
                            <span class="material-icons fz-16">done_all</span> {{ translate('Mark all as read') }}
                        </button>
                    </div>
                </div>

                {{-- Search Bar --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3">
                        <form action="{{ route('provider.notifications.index') }}" method="GET">
                            <div class="input-group">
                                <input type="search" name="search" class="form-control" placeholder="{{ translate('Search by title or description...') }}" value="{{ $search ?? '' }}">
                                <button type="submit" class="btn btn--primary">
                                    <span class="material-icons fz-16 align-middle">search</span> {{ translate('Search') }}
                                </button>
                                @if(!empty($search))
                                    <a href="{{ route('provider.notifications.index') }}" class="btn btn-outline-secondary">
                                        {{ translate('Reset') }}
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Notification List --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        @if($notifications->count() > 0)
                            <div class="list-group list-group-flush">
                                @php
                                    $readIds = $readIds ?? [];
                                @endphp
                                @foreach($notifications as $notification)
                                    @php
                                        $isUnread = !in_array($notification->id, $readIds);
                                        $isPartner = (int)(auth()->user()?->provider?->is_partner ?? 0);
                                        $action = $notification->on_tap_action ?? 'general';

                                        $targetUrl = route('provider.dashboard');
                                        $actionBadge = null;
                                        $buttonText = translate('Open Dashboard');
                                        $buttonIcon = 'dashboard';

                                        if ($action === 'partner_quotations') {
                                            $targetUrl = route('provider.estimate.index');
                                            $actionBadge = translate('Quotations & Estimates');
                                            $buttonText = translate('View Quotations');
                                            $buttonIcon = 'request_quote';
                                        } elseif ($action === 'partner_new_quotation') {
                                            $targetUrl = route('provider.estimate.create');
                                            $actionBadge = translate('Create Quotation');
                                            $buttonText = translate('Create Quotation');
                                            $buttonIcon = 'add_circle';
                                        } elseif ($action === 'partner_customers') {
                                            $targetUrl = route('provider.customer.list');
                                            $actionBadge = translate('Customer Management');
                                            $buttonText = translate('View Customers');
                                            $buttonIcon = 'people';
                                        } elseif ($action === 'provider_service_requests') {
                                            $targetUrl = route('provider.service.request-list');
                                            $actionBadge = translate('Service Requests');
                                            $buttonText = translate('View Service Requests');
                                            $buttonIcon = 'list';
                                        } elseif ($action === 'provider_bookings') {
                                            $targetUrl = route('provider.booking.list');
                                            $actionBadge = translate('Bookings');
                                            $buttonText = translate('View Bookings');
                                            $buttonIcon = 'calendar_month';
                                        } elseif ($action === 'service_page' || $action === 'provider_services') {
                                            $targetUrl = route('provider.service.available');
                                            $actionBadge = translate('Available Services');
                                            $buttonText = translate('View Services');
                                            $buttonIcon = 'home_repair_service';
                                        } elseif ($action === 'quote_form') {
                                            if ($isPartner == 1) {
                                                $targetUrl = route('provider.estimate.index');
                                                $actionBadge = translate('Quotations & Estimates');
                                                $buttonText = translate('View Quotations');
                                                $buttonIcon = 'request_quote';
                                            } else {
                                                $targetUrl = route('provider.service.request-list');
                                                $actionBadge = translate('Service Requests');
                                                $buttonText = translate('View Service Requests');
                                                $buttonIcon = 'list';
                                            }
                                        } else {
                                            $targetUrl = route('provider.dashboard');
                                            $actionBadge = translate('Dashboard');
                                            $buttonText = translate('Open Dashboard');
                                            $buttonIcon = 'dashboard';
                                        }
                                    @endphp
                                    <div class="list-group-item p-3 p-md-4 border-bottom {{ $loop->last ? 'border-bottom-0' : '' }} notification-row-item {{ $isUnread ? 'bg-light' : '' }}" style="{{ $isUnread ? 'background-color: rgba(13, 110, 253, 0.04);' : '' }}">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0 position-relative">
                                                @if($notification->cover_image)
                                                    <img src="{{ $notification->cover_image_full_path }}" class="rounded object-fit-cover shadow-sm" width="55" height="55" alt="{{ translate('image') }}">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(13, 110, 253, 0.1);">
                                                        <span class="material-icons fz-24 text-primary">notifications</span>
                                                    </div>
                                                @endif
                                                @if($isUnread)
                                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-light rounded-circle unread-dot" style="width: 10px; height: 10px;"></span>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <h5 class="mb-0 text-dark fz-15 {{ $isUnread ? 'fw-bold text-primary' : 'fw-semibold' }}">{{ $notification->title }}</h5>
                                                        @if($isUnread)
                                                            <span class="badge bg-primary text-white fz-10 py-1 px-2 status-pill-unread">{{ translate('New') }}</span>
                                                        @else
                                                            <span class="badge bg-light text-muted border fz-10 py-1 px-2 status-pill-read">{{ translate('Read') }}</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-muted fz-12 d-flex align-items-center gap-1">
                                                        <span class="material-icons fz-14">schedule</span>
                                                        {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('d M Y, h:i A') }})
                                                    </span>
                                                </div>

                                                <p class="text-secondary fz-13 mb-2" style="line-height: 1.5;">
                                                    {{ $notification->description }}
                                                </p>

                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        @if($notification->category)
                                                            <span class="badge bg-light text-dark border fz-11 py-1 px-2 d-inline-flex align-items-center gap-1">
                                                                <span class="material-icons fz-12 text-primary">category</span>
                                                                {{ $notification->category->name }}
                                                            </span>
                                                        @endif
                                                        @if($actionBadge)
                                                            <span class="badge bg-light text-primary border fz-11 py-1 px-2">
                                                                <span class="material-icons fz-12 align-middle">launch</span> {{ $actionBadge }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($isUnread)
                                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-mark-read" data-notif-id="{{ $notification->id }}">
                                                                <span class="material-icons fz-14 align-middle">done</span> {{ translate('Mark as read') }}
                                                            </button>
                                                        @endif

                                                        <a href="{{ $targetUrl }}" class="btn btn-sm btn--primary provider-notif-item" data-notif-id="{{ $notification->id }}">
                                                            <span class="material-icons fz-14 align-middle">{{ $buttonIcon }}</span> {{ $buttonText }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <span class="material-icons fz-48 text-muted mb-2 opacity-50">notifications_off</span>
                                <h5 class="text-muted fw-normal">{{ translate('No Notifications Found') }}</h5>
                                <p class="text-muted fz-13 mb-0">{{ translate('You do not have any notifications matching your search criteria.') }}</p>
                            </div>
                        @endif
                    </div>
                    @if($notifications->hasPages())
                        <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                            {!! $notifications->links() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
