@php
    $readIds = $readIds ?? [];
@endphp
@forelse($notifications as $notification)
    @php
        $isUnread = !in_array($notification->id, $readIds);
        $isPartner = (int)(auth()->user()?->provider?->is_partner ?? 0);
        $action = $notification->on_tap_action ?? 'general';

        $targetUrl = route('provider.dashboard');
        $badgeText = null;

        if ($action === 'partner_quotations') {
            $targetUrl = route('provider.estimate.index');
            $badgeText = translate('Quotations & Estimates');
        } elseif ($action === 'partner_new_quotation') {
            $targetUrl = route('provider.estimate.create');
            $badgeText = translate('Create Quotation');
        } elseif ($action === 'partner_customers') {
            $targetUrl = route('provider.customer.list');
            $badgeText = translate('Customer Management');
        } elseif ($action === 'provider_service_requests') {
            $targetUrl = route('provider.service.request-list');
            $badgeText = translate('Service Requests');
        } elseif ($action === 'provider_bookings') {
            $targetUrl = route('provider.booking.list');
            $badgeText = translate('Bookings');
        } elseif ($action === 'service_page' || $action === 'provider_services') {
            $targetUrl = route('provider.service.available');
            $badgeText = translate('Available Services');
        } elseif ($action === 'quote_form') {
            if ($isPartner == 1) {
                $targetUrl = route('provider.estimate.index');
                $badgeText = translate('Quotations & Estimates');
            } else {
                $targetUrl = route('provider.service.request-list');
                $badgeText = translate('Service Requests');
            }
        } else {
            $targetUrl = route('provider.dashboard');
            $badgeText = translate('Dashboard');
        }
    @endphp
    <div class="p-3 border-bottom position-relative notification-row-item {{ $isUnread ? 'bg-light-blue' : '' }}" style="{{ $isUnread ? 'background-color: rgba(13, 110, 253, 0.04);' : '' }}">
        <div class="d-flex align-items-start gap-2">
            <a href="{{ $targetUrl }}" class="flex-grow-1 text-decoration-none d-flex align-items-start gap-3 provider-notif-item" data-notif-id="{{ $notification->id }}">
                <div class="avatar title-color hover-color-c2 flex-shrink-0 mt-1 position-relative">
                    @if($notification->cover_image)
                        <img src="{{$notification->cover_image_full_path}}" class="avatar rounded-circle object-fit-cover" width="38" height="38" alt="{{translate('image')}}">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(13,110,253,0.12);">
                            <span class="material-icons fz-20 text-primary">notifications</span>
                        </div>
                    @endif
                    @if($isUnread)
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-light rounded-circle unread-dot" style="width: 8px; height: 8px;"></span>
                    @endif
                </div>
                <div class="media-body flex-grow-1" style="min-width: 0;">
                    <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                        <h6 class="card-title text-dark fz-13 mb-0 text-truncate {{ $isUnread ? 'fw-bold text-primary' : 'fw-semibold' }}">{{$notification->title}}</h6>
                    </div>
                    <p class="card-text text-muted fz-12 mb-1" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $notification->description }}
                    </p>
                    @php
                        $to_time = strtotime($notification->created_at);
                        $from_time = strtotime(now());
                        $diff = round(abs($to_time - $from_time) / 60, 2);
                        $time = $diff .' '.translate('min');
                        if ($diff > 60){
                            $diff = round($diff / 60);
                            $time = $diff.' '.translate('hr');
                            if ($diff > 24){
                                $diff = round($diff / 24);
                                $time = $diff.' '.translate('day');
                            }
                        }
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="card-text fz-11 text-muted">{{$time}} {{translate('ago')}}</span>
                        @if($badgeText)
                            <span class="badge bg-light text-primary border fz-10 py-1 px-2">{{ $badgeText }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @if($isUnread)
                <button type="button" class="btn btn-sm btn-icon p-1 text-muted hover-primary btn-mark-read flex-shrink-0" data-notif-id="{{ $notification->id }}" title="{{ translate('Mark as read') }}" style="line-height: 1;">
                    <span class="material-icons fz-16">done</span>
                </button>
            @endif
        </div>
    </div>
@empty
    <div class="text-center py-4 px-3">
        <span class="material-icons fz-32 text-muted mb-2 opacity-50">notifications_none</span>
        <p class="fz-13 text-muted mb-0">{{ translate('No notifications found') }}</p>
    </div>
@endforelse
