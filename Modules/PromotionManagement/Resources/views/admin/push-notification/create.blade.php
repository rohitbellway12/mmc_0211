@extends('adminmodule::layouts.new-master')

@section('title',translate('Send Notification'))

@push('css_or_js')
    <style>
        .phone-mockup-wrap {
            position: sticky;
            top: 85px;
            z-index: 10;
        }
        .phone-mockup-frame {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: 10px solid #0f172a;
            border-radius: 40px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-width: 320px;
            margin: 0 auto;
            position: relative;
            min-height: 520px;
            color: #fff;
        }
        .phone-notch {
            width: 110px;
            height: 18px;
            background: #0f172a;
            border-radius: 0 0 12px 12px;
            margin: 0 auto;
        }
        .phone-status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 18px 8px 18px;
            font-size: 11px;
            font-weight: 600;
            color: #f1f5f9;
        }
        .phone-lockscreen-time {
            text-align: center;
            padding: 15px 10px 10px 10px;
        }
        .phone-lockscreen-time .clock-text {
            font-size: 40px;
            font-weight: 300;
            letter-spacing: -1px;
            line-height: 1;
        }
        .phone-lockscreen-time .date-text {
            font-size: 12px;
            font-weight: 400;
            opacity: 0.85;
            margin-top: 4px;
        }
        .phone-notification-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 12px 14px;
            margin: 0 10px 12px 10px;
            color: #0f172a;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease-in-out;
        }
        .phone-notif-header {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 6px;
        }
        .phone-notif-icon {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            object-fit: cover;
        }
        .phone-notif-appname {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .phone-notif-time {
            font-size: 10px;
            color: #64748b;
            margin-left: auto;
        }
        .phone-notif-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            margin-bottom: 4px;
            word-break: break-word;
        }
        .phone-notif-body {
            font-size: 11.5px;
            color: #334155;
            line-height: 1.4;
            word-break: break-word;
            margin-bottom: 4px;
        }
        .phone-notif-image-wrap {
            margin-top: 6px;
            border-radius: 10px;
            overflow: hidden;
            max-height: 130px;
            background: #e2e8f0;
        }
        .phone-notif-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .phone-notif-action-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(13, 110, 253, 0.08);
            color: #0d6efd;
            border: 1px solid rgba(13, 110, 253, 0.2);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 10.5px;
            font-weight: 600;
            margin-top: 6px;
        }
        .phone-bottom-home-bar {
            width: 90px;
            height: 4px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 10px;
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
        }
    </style>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('push_notification')}}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 fs-12 p-12 text-dark rounded mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_9562_195)">
                                <path d="M7 14C8.38447 14 9.73785 13.5895 10.889 12.8203C12.0401 12.0511 12.9373 10.9579 13.4672 9.67879C13.997 8.3997 14.1356 6.99224 13.8655 5.63437C13.5954 4.2765 12.9287 3.02922 11.9497 2.05026C10.9708 1.07129 9.7235 0.404603 8.36563 0.134506C7.00777 -0.13559 5.6003 0.003033 4.32122 0.532846C3.04213 1.06266 1.94888 1.95987 1.17971 3.11101C0.410543 4.26216 0 5.61553 0 7C0.0020073 8.8559 0.74015 10.6352 2.05247 11.9475C3.36479 13.2599 5.1441 13.998 7 14ZM7 2.91667C7.17306 2.91667 7.34223 2.96799 7.48612 3.06413C7.63002 3.16028 7.74217 3.29694 7.8084 3.45682C7.87462 3.61671 7.89195 3.79264 7.85819 3.96237C7.82443 4.13211 7.74109 4.28802 7.61872 4.41039C7.49635 4.53276 7.34044 4.6161 7.1707 4.64986C7.00097 4.68362 6.82504 4.66629 6.66515 4.60006C6.50527 4.53384 6.36861 4.42169 6.27246 4.27779C6.17632 4.1339 6.125 3.96473 6.125 3.79167C6.125 3.55961 6.21719 3.33705 6.38128 3.17295C6.54538 3.00886 6.76794 2.91667 7 2.91667ZM6.41667 5.83334H7C7.30942 5.83334 7.60617 5.95625 7.82496 6.17505C8.04375 6.39384 8.16667 6.69058 8.16667 7V10.5C8.16667 10.6547 8.10521 10.8031 7.99581 10.9125C7.88642 11.0219 7.73804 11.0833 7.58333 11.0833C7.42862 11.0833 7.28025 11.0219 7.17086 10.9125C7.06146 10.8031 7 10.6547 7 10.5V7H6.41667C6.26196 7 6.11358 6.93855 6.00419 6.82915C5.89479 6.71975 5.83333 6.57138 5.83333 6.41667C5.83333 6.26196 5.89479 6.11359 6.00419 6.00419C6.11358 5.8948 6.26196 5.83334 6.41667 5.83334Z" fill="#FFBB38"></path>
                                </g>
                                <defs>
                                <clipPath id="clip0_9562_195">
                                <rect width="14" height="14" fill="white"></rect>
                                </clipPath>
                                </defs>
                            </svg>
                            <p class="fz-12 mb-20">{{ translate('Setup Push Notification Messages for customer. Must setup') }} <a @can('firebase_view') href="{{ route('admin.configuration.third-party','firebase-configuration') }}" @endcan target="_blank" class="text-primary text-decoration-underline fw-medium">{{ translate('Firebase Configuration') }}</a> {{ translate(' page to work notifications.') }}</p>
                        </div>
                    </div>

                    @can('push_notification_add')
                        <div class="card mb-30 shadow-sm border">
                            <div class="card-header bg-white border-bottom pt-20 px-25 pb-15 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h3 class="mb-1 text-dark d-flex align-items-center gap-2">
                                        <span class="material-icons text-primary">notifications_active</span>
                                        {{ translate('Create & Send Push Notification') }}
                                    </h3>
                                    <p class="fz-12 text-muted mb-0">{{ translate('Send immediate announcements or schedule targeted service campaigns directly to customer devices.') }}</p>
                                </div>
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fz-12 fw-medium d-flex align-items-center gap-1">
                                        <span class="material-icons fz-15">schedule</span>
                                        {{ translate('UK Time') }}: {{ \Carbon\Carbon::now()->format('h:i A') }} ({{ date_default_timezone_get() }})
                                    </span>
                                </div>
                            </div>

                            <div class="card-body p-20 p-lg-25 bg-light-subtle">
                                <form action="{{route('admin.push-notification.store')}}" method="POST" enctype="multipart/form-data" id="push_notification_create_form">
                                    @csrf
                                    <input type="hidden" name="user_tz_offset" class="user_tz_offset_input">

                                    <div class="row g-4">
                                        {{-- LEFT COLUMN: All Inputs & Configuration --}}
                                        <div class="col-xl-7 col-lg-7">
                                            {{-- STEP 1: Notification Message --}}
                                            <div class="card border mb-20 shadow-none">
                                                <div class="card-header bg-white py-12 px-20 border-bottom d-flex align-items-center justify-content-between">
                                                    <h4 class="text-dark fz-14 fw-semibold mb-0 d-flex align-items-center gap-2">
                                                        <span class="material-icons fz-18 text-primary">edit_note</span>
                                                        1. {{ translate('Notification Message') }}
                                                    </h4>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fz-10">{{ translate('Required') }}</span>
                                                </div>
                                                <div class="card-body p-20 bg-white">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="form-label text-dark fz-13 fw-semibold mb-0">
                                                                {{translate('Notification Title')}} <span class="text-danger">*</span>
                                                            </label>
                                                            <span class="text-muted letter-count fz-11 title-count">0/100</span>
                                                        </div>
                                                        <input type="text" class="form-control theme-input-style" name="title" id="notif_title_input" placeholder="{{translate('e.g., SMART repair slots available in Bromley')}}" maxlength="100" data-maxlength="100" value="{{ old('title') }}" required>
                                                    </div>

                                                    <div class="mb-0">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="form-label text-dark fz-13 fw-semibold mb-0">
                                                                {{translate('Message / Description')}} <span class="text-danger">*</span>
                                                            </label>
                                                            <span class="text-muted letter-count fz-11 desc-count">0/200</span>
                                                        </div>
                                                        <textarea class="form-control theme-input-style" name="description" id="notif_desc_input" rows="3" placeholder="{{translate('e.g., Got a bumper scuff or scratch? Tap to upload photos and request an instant quote.')}}" maxlength="200" data-maxlength="200" required>{{ old('description') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- STEP 2: Banner Image (Optional) --}}
                                            <div class="card border mb-20 shadow-none">
                                                <div class="card-header bg-white py-12 px-20 border-bottom d-flex align-items-center justify-content-between">
                                                    <h4 class="text-dark fz-14 fw-semibold mb-0 d-flex align-items-center gap-2">
                                                        <span class="material-icons fz-18 text-primary">image</span>
                                                        2. {{ translate('Banner Image') }}
                                                    </h4>
                                                    <span class="badge bg-light text-muted border fz-10">{{ translate('Optional') }}</span>
                                                </div>
                                                <div class="card-body p-20 bg-white">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="custom-upload-wrapper upload-group image-upload-wrap1 m-0">
                                                            <input type="file" id="imageUpload" accept="image/*" name="cover_image">
                                                            <label for="imageUpload" class="upload-box rounded position-relative d-flex align-items-center justify-content-center text-center overflow-hidden bg-light border cursor-pointer m-0" style="width: 120px; height: 80px;">
                                                                <div class="upload-content">
                                                                    <span class="material-symbols-outlined placeholder-icon text-primary fz-24">
                                                                        photo_camera
                                                                    </span>
                                                                    <div class="fz-10 text-dark mt-1">{{ translate('Upload') }}</div>
                                                                </div>
                                                                <img class="image-preview" src="" alt="Preview" style="display:none;" />
                                                            </label>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="fz-12 text-dark fw-medium mb-1">{{ translate('Attach banner image (Optional)') }}</p>
                                                            <p class="fz-11 text-muted mb-0">{{ translate('JPG, PNG up to 2MB (2:1 ratio). Leave empty for clean standard text push.') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- STEP 3: Audience & Area --}}
                                            <div class="card border mb-20 shadow-none">
                                                <div class="card-header bg-white py-12 px-20 border-bottom d-flex align-items-center justify-content-between">
                                                    <h4 class="text-dark fz-14 fw-semibold mb-0 d-flex align-items-center gap-2">
                                                        <span class="material-icons fz-18 text-primary">groups</span>
                                                        3. {{ translate('Audience & Location') }}
                                                    </h4>
                                                    <span class="badge bg-light text-muted border fz-10">{{ translate('Optional Defaults') }}</span>
                                                </div>
                                                <div class="card-body p-20 bg-white">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label text-dark fz-12 fw-semibold mb-1">
                                                                {{translate('Area / Zones')}}
                                                                <span class="badge bg-light text-muted border fz-10 ms-1">{{ translate('Defaults to All UK') }}</span>
                                                            </label>
                                                            <div class="position-relative">
                                                                <select class="select-zone theme-input-style w-100" name="zone_ids[]" id="zone_selector__select4" multiple="multiple">
                                                                    <option value="all" {{ collect(old('zone_ids', ['all']))->contains('all') ? 'selected' : '' }}>{{translate('Select All Zones (UK)')}}</option>
                                                                    @foreach($zones as $zone)
                                                                        <option value="{{$zone->id}}" {{ collect(old('zone_ids'))->contains($zone->id) ? 'selected' : '' }}>{{$zone->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label text-dark fz-12 fw-semibold mb-1">
                                                                {{translate('Target User Group')}}
                                                                <span class="badge bg-light text-muted border fz-10 ms-1">{{ translate('Select Audience') }}</span>
                                                            </label>
                                                            <div class="position-relative">
                                                                <select class="select-user theme-input-style w-100" name="to_users[]" id="user_selector__select" multiple="multiple">
                                                                    <option value="all" {{ collect(old('to_users', ['all']))->contains('all') ? 'selected' : '' }}>{{translate('All Users')}}</option>
                                                                    <option value="customer" {{ collect(old('to_users'))->contains('customer') ? 'selected' : '' }}>{{translate('Customers (App & Web)')}}</option>
                                                                    <option value="partner-admin" {{ collect(old('to_users'))->contains('partner-admin') ? 'selected' : '' }}>{{translate('Partners (Workshops / Estimators)')}}</option>
                                                                    <option value="provider-admin" {{ collect(old('to_users'))->contains('provider-admin') ? 'selected' : '' }}>{{translate('Providers (Garages / Mechanics)')}}</option>
                                                                    <option value="provider-serviceman" {{ collect(old('to_users'))->contains('provider-serviceman') ? 'selected' : '' }}>{{translate('Servicemen')}}</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- Audience Targeting Filter (Shown only when a Service Category is chosen) --}}
                                                        <div class="col-12" id="audience_targeting_filter_wrapper" style="display: none;">
                                                            <div class="p-3 bg-light rounded border">
                                                                <label class="form-label text-dark fz-12 fw-semibold mb-2 d-flex align-items-center gap-1">
                                                                    <span class="material-icons fz-16 text-primary">filter_alt</span>
                                                                    {{translate('Service Campaign Audience')}}
                                                                </label>
                                                                <div class="d-flex flex-column gap-2">
                                                                    <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer">
                                                                        <input class="form-check-input audience-type-radio" type="radio" name="audience_type" id="audience_all" value="all" {{ old('audience_type', 'all') == 'all' ? 'checked' : '' }}>
                                                                        <label class="form-check-label cursor-pointer text-dark fz-12" for="audience_all">
                                                                            <strong>{{translate('All Customers in Selected Area')}}</strong>
                                                                            <span class="text-muted fz-11 d-block">{{translate('Send this service announcement to everyone in the area')}}</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer border-top pt-2">
                                                                        <input class="form-check-input audience-type-radio" type="radio" name="audience_type" id="audience_targeted" value="targeted" {{ old('audience_type') == 'targeted' ? 'checked' : '' }}>
                                                                        <label class="form-check-label cursor-pointer text-dark fz-12" for="audience_targeted">
                                                                            <strong>{{translate('Targeted Customers Only')}}</strong>
                                                                            <span class="text-muted fz-11 d-block" id="targeted_service_label_text">{{translate('Only customers who previously booked or requested quotes for this service')}}</span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Live Reach Status --}}
                                                        <div class="col-12">
                                                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <span class="text-muted fz-11 fw-semibold text-uppercase d-block">{{translate('Ready to Deliver')}}</span>
                                                                    <div class="d-flex align-items-baseline gap-2 mt-1">
                                                                        <h3 class="mb-0 text-primary fw-bold fz-22" id="estimated_reach_display">--</h3>
                                                                        <span class="text-dark fz-13 fw-medium">{{translate('recipients will receive this push')}}</span>
                                                                    </div>
                                                                    <div class="fz-11 text-muted" id="reach_breakdown_display">
                                                                        {{translate('Calculating live reach...')}}
                                                                    </div>
                                                                </div>
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fz-11 d-flex align-items-center gap-1" id="reach_calculation_status">
                                                                    <span class="material-icons fz-14">check_circle</span>
                                                                    {{translate('Active Target')}}
                                                                </span>
                                                                <input type="hidden" name="estimated_reach" id="estimated_reach_input" value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- STEP 4: Destination & Action (Deep Link) --}}
                                            <div class="card border mb-20 shadow-none">
                                                <div class="card-header bg-white py-12 px-20 border-bottom d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h4 class="text-dark fz-14 fw-semibold mb-0 d-flex align-items-center gap-2">
                                                            <span class="material-icons fz-18 text-primary">touch_app</span>
                                                            4. {{ translate('Destination & Action (Deep Link)') }}
                                                        </h4>
                                                    </div>
                                                    <span class="badge bg-light text-muted border fz-10">{{ translate('Optional') }}</span>
                                                </div>
                                                <div class="card-body p-20 bg-white">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label text-dark fz-13 fw-semibold mb-1">
                                                                {{translate('On-Tap Action (Screen to Open)')}}
                                                            </label>
                                                            <select class="form-select theme-input-style w-100" name="on_tap_action" id="on_tap_action__select">
                                                                <option value="general" {{ old('on_tap_action', 'general') == 'general' ? 'selected' : '' }}>{{translate('Open App Home / Dashboard (All Roles)')}}</option>
                                                                
                                                                <optgroup label="📱 {{translate('Customer Actions (App & Web)')}}" id="optgroup_customer">
                                                                    <option value="quote_form" {{ old('on_tap_action') == 'quote_form' ? 'selected' : '' }}>{{translate('Open Quote Request Form')}}</option>
                                                                    <option value="service_page" {{ old('on_tap_action') == 'service_page' ? 'selected' : '' }}>{{translate('Open Service Details Page')}}</option>
                                                                </optgroup>
                                                                
                                                                <optgroup label="🏢 {{translate('Partner Actions')}}" id="optgroup_partner">
                                                                    <option value="partner_quotations" {{ old('on_tap_action') == 'partner_quotations' ? 'selected' : '' }}>{{translate('Quotations & Estimates List')}}</option>
                                                                    <option value="partner_new_quotation" {{ old('on_tap_action') == 'partner_new_quotation' ? 'selected' : '' }}>{{translate('Create New Quotation')}}</option>
                                                                    <option value="partner_customers" {{ old('on_tap_action') == 'partner_customers' ? 'selected' : '' }}>{{translate('Customer Management / Add Customer')}}</option>
                                                                    <option value="provider_bookings" {{ old('on_tap_action') == 'provider_bookings' ? 'selected' : '' }}>{{translate('Bookings List')}}</option>
                                                                </optgroup>
                                                                
                                                                <optgroup label="🔧 {{translate('Provider Actions')}}" id="optgroup_provider">
                                                                    <option value="provider_service_requests" {{ old('on_tap_action') == 'provider_service_requests' ? 'selected' : '' }}>{{translate('Service Requests List')}}</option>
                                                                    <option value="provider_services" {{ old('on_tap_action') == 'provider_services' ? 'selected' : '' }}>{{translate('Available Services & Subscriptions')}}</option>
                                                                    <option value="provider_bookings" {{ old('on_tap_action') == 'provider_bookings' ? 'selected' : '' }}>{{translate('Bookings List')}}</option>
                                                                </optgroup>
                                                            </select>
                                                            <div class="fz-11 text-primary mt-1 fw-medium" id="on_tap_action_hint">
                                                                <span class="material-icons fz-12 align-middle">info</span> {{ translate('Select target screen to open when user clicks notification.') }}
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label text-dark fz-13 fw-semibold mb-1">
                                                                {{translate('Service Category')}}
                                                                <span class="badge bg-light text-muted border fz-10 ms-1">{{ translate('Optional') }}</span>
                                                            </label>
                                                            <select class="form-select theme-input-style w-100" name="category_id" id="category_selector__select">
                                                                <option value="">-- {{translate('No Service (General Broadcast)')}} --</option>
                                                                @foreach($categories as $cat)
                                                                    <option value="{{$cat->id}}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{$cat->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="fz-11 text-muted mt-1">{{ translate('e.g. SMART Repair, Alloy Refurbishment, Car Hire') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- STEP 5: Delivery Timing --}}
                                            <div class="card border mb-20 shadow-none">
                                                <div class="card-header bg-white py-12 px-20 border-bottom d-flex align-items-center justify-content-between">
                                                    <h4 class="text-dark fz-14 fw-semibold mb-0 d-flex align-items-center gap-2">
                                                        <span class="material-icons fz-18 text-primary">schedule</span>
                                                        5. {{ translate('Delivery Timing') }}
                                                    </h4>
                                                    <span class="badge bg-light text-muted border fz-10">{{ translate('Defaults to Immediate') }}</span>
                                                </div>
                                                <div class="card-body p-20 bg-white">
                                                    <div class="d-flex flex-wrap gap-4 align-items-center p-3 bg-light rounded border mb-3">
                                                        <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer">
                                                            <input class="form-check-input send-type-radio" type="radio" name="send_type" id="send_type_immediate" value="immediate" {{ old('send_type', 'immediate') == 'immediate' ? 'checked' : '' }}>
                                                            <label class="form-check-label cursor-pointer text-dark fz-13" for="send_type_immediate">
                                                                <strong>⚡ {{translate('Send Immediately')}}</strong>
                                                                <span class="text-muted fz-11 d-block">{{translate('Broadcast directly right now upon saving')}}</span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer border-start ps-4">
                                                            <input class="form-check-input send-type-radio" type="radio" name="send_type" id="send_type_scheduled" value="scheduled" {{ old('send_type') == 'scheduled' ? 'checked' : '' }}>
                                                            <label class="form-check-label cursor-pointer text-dark fz-13" for="send_type_scheduled">
                                                                <strong>⏰ {{translate('Schedule for Later')}}</strong>
                                                                <span class="text-muted fz-11 d-block">{{translate('Automated delivery at a future date and UK time')}}</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    {{-- Expandable Scheduling Options --}}
                                                    <div id="scheduled_delivery_options" class="p-3 bg-white rounded border {{ old('send_type') == 'scheduled' ? '' : 'd-none' }}">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label text-dark fz-13 fw-semibold mb-1">{{translate('Frequency / Repeat')}}</label>
                                                                <select class="form-select select-repeat-type theme-input-style" name="repeat_type" id="repeat_type">
                                                                    <option value="once" {{ old('repeat_type', 'once') == 'once' ? 'selected' : '' }}>{{translate('One-Time (Specific Date & Time)')}}</option>
                                                                    <option value="daily" {{ old('repeat_type') == 'daily' ? 'selected' : '' }}>{{translate('Daily Repeating (Every Day at Time)')}}</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-8" id="scheduled_at_wrapper">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="form-label text-dark fz-13 fw-semibold mb-0">{{translate('Date & Time Slot(s)')}} <span class="text-danger">*</span></label>
                                                                    <button type="button" class="btn btn-sm btn-outline--primary py-0 px-2 fz-12 d-flex align-items-center gap-1" id="add_more_datetime_btn">
                                                                        <span class="material-icons fz-14">add</span> {{translate('Add More Date')}}
                                                                    </button>
                                                                </div>
                                                                <div id="scheduled_datetime_container" class="d-flex flex-column gap-2">
                                                                    <div class="d-flex align-items-center gap-2 datetime-slot-row">
                                                                        <input type="datetime-local" class="form-control theme-input-style" name="scheduled_at[]" min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" value="{{ \Carbon\Carbon::now()->addMinutes(15)->format('Y-m-d\TH:i') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="fz-11 text-primary mt-2 fw-semibold">
                                                                    <span class="material-icons fz-12 align-middle">schedule</span> {{translate('Company Timezone')}}: {{ date_default_timezone_get() }} ({{translate('Current Time')}}: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }})
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8 d-none" id="scheduled_time_wrapper">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="form-label text-dark fz-13 fw-semibold mb-0">{{translate('Daily Send Time Slot(s)')}} <span class="text-danger">*</span></label>
                                                                    <button type="button" class="btn btn-sm btn-outline--primary py-0 px-2 fz-12 d-flex align-items-center gap-1" id="add_more_time_btn">
                                                                        <span class="material-icons fz-14">add</span> {{translate('Add More Time')}}
                                                                    </button>
                                                                </div>
                                                                <div id="scheduled_time_container" class="d-flex flex-column gap-2">
                                                                    <div class="d-flex align-items-center gap-2 time-slot-row">
                                                                        <input type="time" class="form-control theme-input-style" name="scheduled_time[]" value="{{ \Carbon\Carbon::now()->addHour()->minute(0)->format('H:i') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="fz-11 text-primary mt-2 fw-semibold">
                                                                    <span class="material-icons fz-12 align-middle">schedule</span> {{translate('Company Timezone')}}: {{ date_default_timezone_get() }} ({{translate('Current Time')}}: {{ \Carbon\Carbon::now()->format('h:i A') }})
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- FORM SUBMIT & RESET BUTTONS --}}
                                            <div class="d-flex justify-content-end gap-3 pt-2 mb-4 mb-lg-0">
                                                <button class="btn btn--secondary rounded px-4" type="reset" id="reset_notification_form_btn">{{translate('Reset Form')}}</button>
                                                @can('push_notification_add')
                                                    <button class="btn btn--primary rounded px-4 d-flex align-items-center gap-2" type="submit" id="submit_notification_btn">
                                                        <span class="material-icons fz-18">send</span>
                                                        {{translate('Save & Send Notification')}}
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>

                                        {{-- RIGHT COLUMN: 📱 Real-time Mobile Phone Notification Preview --}}
                                        <div class="col-xl-5 col-lg-5">
                                            <div class="phone-mockup-wrap">
                                                <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                                                    <h4 class="text-dark fz-15 fw-semibold mb-0 d-flex align-items-center gap-2">
                                                        <span class="material-icons fz-18 text-primary">smartphone</span>
                                                        {{ translate('Mobile Device Preview') }}
                                                    </h4>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle fz-11 d-flex align-items-center gap-1">
                                                        <span class="material-icons fz-12">visibility</span>
                                                        {{ translate('Live Customer View') }}
                                                    </span>
                                                </div>

                                                {{-- SMARTPHONE DEVICE FRAME --}}
                                                <div class="phone-mockup-frame">
                                                    {{-- Phone Notch / Speaker --}}
                                                    <div class="phone-notch"></div>

                                                    {{-- Phone Status Bar --}}
                                                    <div class="phone-status-bar">
                                                        <span id="phone_mockup_clock">{{ \Carbon\Carbon::now()->format('h:i') }}</span>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="material-icons fz-12">wifi</span>
                                                            <span class="material-icons fz-12">signal_cellular_alt</span>
                                                            <span class="material-icons fz-14">battery_full</span>
                                                        </div>
                                                    </div>

                                                    {{-- Lockscreen Clock Widget --}}
                                                    <div class="phone-lockscreen-time">
                                                        <div class="clock-text" id="phone_lockscreen_time_display">{{ \Carbon\Carbon::now()->format('h:i') }}</div>
                                                        <div class="date-text">{{ \Carbon\Carbon::now()->format('l, d F') }}</div>
                                                    </div>

                                                    {{-- PUSH NOTIFICATION BANNER CARD --}}
                                                    <div class="phone-notification-card">
                                                        {{-- App Info Header --}}
                                                        <div class="phone-notif-header">
                                                            <img src="{{ asset('public/assets/admin-module/img/mobile-logo.png') }}" alt="MMC" class="phone-notif-icon" onerror="this.src='{{ asset('public/assets/admin-module/img/logo.png') }}'">
                                                            <span class="phone-notif-appname">MMC</span>
                                                            <span class="phone-notif-time">{{ translate('now') }}</span>
                                                        </div>

                                                        {{-- Live Notification Title --}}
                                                        <div class="phone-notif-title" id="preview_notif_title">
                                                            {{ translate('Notification Title') }}
                                                        </div>

                                                        {{-- Live Notification Description --}}
                                                        <div class="phone-notif-body" id="preview_notif_body">
                                                            {{ translate('Your push notification description will appear here on the customer mobile screen.') }}
                                                        </div>

                                                        {{-- Live Image Banner (if uploaded) --}}
                                                        <div class="phone-notif-image-wrap d-none" id="phone_notif_image_container">
                                                            <img src="" id="preview_notif_img" alt="Push Banner">
                                                        </div>

                                                        {{-- On-Tap Action Pill --}}
                                                        <div class="phone-notif-action-tag" id="preview_notif_action_pill">
                                                            <span class="material-icons fz-12">touch_app</span>
                                                            <span id="preview_notif_action_text">{{ translate('Tap to open MMC App') }}</span>
                                                        </div>
                                                    </div>

                                                    {{-- Bottom Hint --}}
                                                    <div class="text-center mt-3" style="opacity: 0.6; font-size: 10px;">
                                                        <span class="material-icons fz-12 align-middle">keyboard_arrow_up</span>
                                                        {{ translate('Swipe up to unlock') }}
                                                    </div>

                                                    {{-- Phone Home Indicator Bar --}}
                                                    <div class="phone-bottom-home-bar"></div>
                                                </div>

                                                {{-- Live Delivery Summary Card below phone --}}
                                                <div class="p-3 bg-white rounded border mt-3 text-center shadow-sm">
                                                    <div class="fz-12 text-muted mb-1">{{ translate('Delivery Target Summary') }}</div>
                                                    <div class="fw-bold fz-14 text-dark d-flex align-items-center justify-content-center gap-1">
                                                        <span class="material-icons fz-16 text-success">verified</span>
                                                        <span id="preview_summary_reach_text">{{ translate('Reaching eligible users in area') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-1 mb-10 gap-3">
                        <ul class="nav nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='all'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=all">
                                    {{translate('all')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='customer'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=customer">
                                    {{translate('customer')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='partner-admin'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=partner-admin">
                                    {{translate('partner')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='provider-admin'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=provider-admin">
                                    {{translate('provider')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='provider-serviceman'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=provider-serviceman">
                                    {{translate('serviceman')}}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-tab-pane">
                            <div class="card">
                                <div class="card-body">
                                    <div class="data-table-top d-flex align-items-center flex-wrap gap-10 justify-content-between">
                                        <h4>{{ translate('Notification History') }}</h4>
                                        <div class="d-flex align-items-center gap-sm-3 gap-2 flex-sm-nowrap flex-wrap">
                                            <form action="{{url()->current()}}?to_user_type={{$toUserType}}" class="d-flex align-items-center gap-0 border rounded" method="POST">
                                                @csrf
                                                <input type="search" class="theme-input-style border-0 rounded block-size-36" value="{{$search}}" name="search" placeholder="{{translate('search_by_title')}}">
                                                <button type="submit" class="bg-light border-0 px-2 block-size-36 rounded-end d-flex align-items-center justify-content-center">
                                                    <span class="material-symbols-outlined fz-20 opacity-75">
                                                        search
                                                    </span>
                                                </button>
                                            </form>
                                            <div class="d-flex flex-wrap align-items-center gap-3 h-full">
                                                @can('push_notification_export')
                                                    <div class="dropdown h-100">
                                                        <button type="button" class="btn btn--secondary block-size-36 rounded text-capitalize dropdown-toggle" data-bs-toggle="dropdown">
                                                            <span class="material-symbols-outlined">cloud_download</span> {{translate('download')}}
                                                        </button>
                                                        @can('push_notification_export')
                                                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                                <a class="dropdown-item"
                                                                   href="{{route('admin.push-notification.download')}}?search={{$search}}&&to_user_type={{$toUserType}}">
                                                                    {{translate('excel')}}
                                                                </a>
                                                            </ul>
                                                        @endcan
                                                    </div>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive table-custom-responsive">
                                        <table id="example" class="table align-middle">
                                            <thead class="text-nowrap">
                                                <tr>
                                                    <th class="text-dark fw-medium bg-light">{{translate('Sl')}}</th>
                                                    <th class="text-dark fw-medium bg-light">{{translate('cover_image')}}</th>
                                                    <th class="text-dark fw-medium bg-light">{{translate('title')}}</th>
                                                    <th class="text-dark fw-medium bg-light">{{translate('description')}}</th>
                                                    <th class="text-dark fw-medium bg-light">{{translate('send_to')}}</th>
                                                    <th class="text-dark fw-medium bg-light">{{translate('zones')}}</th>
                                                    <th class="text-dark fw-medium bg-light">{{translate('Service & Action')}}</th>
                                                    <th class="text-dark fw-medium bg-light">{{translate('Delivery / Timing')}}</th>
                                                    <th class="text-dark fw-medium bg-light">{{translate('Outcome (Sent / Taps)')}}</th>
                                                    @can('push_notification_manage_status')
                                                        <th class="text-dark fw-medium bg-light">{{translate('status')}}</th>
                                                    @endcan
                                                    @canany(['push_notification_delete', 'push_notification_update'])
                                                        <th class="text-dark fw-medium bg-light">{{translate('action')}}</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pushNotification as $key => $item)
                                                <tr>
                                                    <td>{{$key+$pushNotification->firstItem()}}</td>
                                                    <td>
                                                        <img src="{{$item->cover_image_full_path}}" class="table-cover-img" alt="">
                                                    </td>
                                                    <td>
                                                        <div class="line-limit-2 min-w-80" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $item->title }}">
                                                            {{$item->title}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="line-limit-2 min-w-150" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $item->description }}">
                                                            {{$item->description}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="min-w180 position-relative max-w-293">
                                                            @foreach($item->to_users as $key=>$user)
                                                                {{$user}}{{$key+1==count($item->to_users)?'':','}}
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="min-w180">
                                                            @foreach($item->zone_ids as $key=>$zone)
                                                                {{$zone['name']}}{{$key+1==count($item->zone_ids)?'':','}}
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($item->category)
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                                                {{$item->category->name}}
                                                            </span>
                                                        @else
                                                            <span class="text-muted fz-12">{{translate('General')}}</span>
                                                        @endif
                                                        <div class="fz-11 text-muted mt-1 text-capitalize">
                                                            <span class="material-icons fz-12 align-middle text-primary">touch_app</span>
                                                            {{ str_replace('_', ' ', $item->on_tap_action ?? 'general') }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if(($item->send_type ?? 'immediate') == 'immediate')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                                <span class="material-icons fz-12 align-middle">done_all</span> {{translate('Immediate')}}
                                                            </span>
                                                        @elseif($item->repeat_type == 'daily')
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" title="{{translate('Daily at')}} {{ $item->scheduled_time ? \Carbon\Carbon::parse($item->scheduled_time)->format('h:i A') : '' }}">
                                                                <span class="material-icons fz-12 align-middle">repeat</span> {{translate('Daily')}} ({{ $item->scheduled_time ? \Carbon\Carbon::parse($item->scheduled_time)->format('h:i A') : '' }})
                                                            </span>
                                                        @else
                                                            @if($item->is_sent)
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" title="{{translate('Sent at')}} {{ $item->last_sent_at ? $item->last_sent_at->format('d M Y, h:i A') : '' }}">
                                                                    <span class="material-icons fz-12 align-middle">done</span> {{translate('Sent')}}
                                                                </span>
                                                                <div class="fz-11 text-muted mt-1">{{ $item->scheduled_at ? $item->scheduled_at->format('d M Y, h:i A') : '' }}</div>
                                                            @else
                                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1" title="{{translate('Pending delivery')}}">
                                                                    <span class="material-icons fz-12 align-middle">schedule</span> {{translate('Scheduled')}}
                                                                </span>
                                                                <div class="fz-11 text-muted mt-1">{{ $item->scheduled_at ? $item->scheduled_at->format('d M Y, h:i A') : '' }}</div>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-1 min-w-100">
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-start" title="{{translate('Successfully submitted pushes')}}">
                                                                <span class="material-icons fz-12 align-middle">send</span> {{ $item->sent_count ?? 0 }} {{translate('Sent')}}
                                                            </span>
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 text-start" title="{{translate('Clicks / taps recorded from customer app')}}">
                                                                <span class="material-icons fz-12 align-middle">ads_click</span> {{ $item->click_count ?? 0 }} {{translate('Taps')}}
                                                            </span>
                                                            <span class="fz-11 text-muted">{{translate('Est. Reach')}}: {{ $item->estimated_reach ?? 0 }}</span>
                                                        </div>
                                                    </td>
                                                    @can('push_notification_manage_status')
                                                        <td>
                                                            <label class="switcher">
                                                                <input class="switcher_input status-update"
                                                                       data-status="{{$item->id}}"
                                                                       data-id="{{ $item->id }}"
                                                                       type="checkbox" {{$item->is_active?'checked':''}}>
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                    @endcan
                                                    @canany(['push_notification_delete', 'push_notification_update'])
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                    <button class="action-btn icon-hover btn--light-primary show-notification-details"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#notificationDetailsModal"
                                                                            data-id="{{ $item->id }}"
                                                                            data-title="{{ $item->title }}"
                                                                            data-description="{{ $item->description }}"
                                                                            data-image="{{ $item->cover_image_full_path }}"
                                                                            data-zone_ids='@json($item->zone_ids)'
                                                                            data-to_users='@json($item->to_users)'
                                                                            data-category="{{ $item->category ? $item->category->name : translate('General Notification') }}"
                                                                            data-action_type="{{ str_replace('_', ' ', $item->on_tap_action ?? 'general') }}"
                                                                            data-reach="{{ $item->estimated_reach ?? 0 }}"
                                                                            data-sent="{{ $item->sent_count ?? 0 }}"
                                                                            data-clicks="{{ $item->click_count ?? 0 }}"
                                                                            data-resend="{{ route('admin.push-notification.resend', [$item->id]) }}">
                                                                    <span class="material-symbols-outlined">visibility</span>
                                                                    </button>
                                                                @can('push_notification_update')

                                                                    <button class="action-btn btn--light-primary edit-notification-btn"
                                                                            data-bs-target="#edit__NotifiCation"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-id="{{ $item->id }}"
                                                                            data-title="{{ $item->title }}"
                                                                            data-description="{{ $item->description }}"
                                                                            data-zone_ids='@json($item->zone_ids)'
                                                                            data-to_users='@json($item->to_users)'
                                                                            data-image="{{ $item->cover_image_full_path }}"
                                                                            data-send_type="{{ $item->send_type ?? 'immediate' }}"
                                                                            data-repeat_type="{{ $item->repeat_type ?? 'once' }}"
                                                                            data-scheduled_at="{{ $item->scheduled_at ? $item->scheduled_at->format('Y-m-d\TH:i') : '' }}"
                                                                            data-scheduled_time="{{ $item->scheduled_time ?? '' }}"
                                                                            data-category_id="{{ $item->category_id ?? '' }}"
                                                                            data-on_tap_action="{{ $item->on_tap_action ?? 'general' }}"
                                                                            data-audience_type="{{ $item->audience_type ?? 'all' }}"
                                                                            data-estimated_reach="{{ $item->estimated_reach ?? 0 }}"
                                                                            data-action="{{ route('admin.push-notification.update', [$item->id]) }}"
                                                                            data-resend="{{ route('admin.push-notification.resend', [$item->id]) }}">
                                                                    <span class="material-icons">edit</span>
                                                                    </button>
                                                                @endcan
                                                                @can('push_notification_delete')
                                                                    <button type="button"
                                                                            data-id="{{$item->id}}"
                                                                            class="delete-content action-btn btn--danger"
                                                                            data-url="{{ route('admin.push-notification.delete', [$item->id]) }}"
                                                                            data-title="{{ translate('want_to_delete_this') }}?"
                                                                            data-description="{{ translate('You will not be able to revert this!') }}"
                                                                            data-image="{{ asset('public/assets/admin-module/img/modal/delete-icon.svg') }}">
                                                                        <span class="material-icons">delete</span>
                                                                    </button>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    @endcan
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {!! $pushNotification->links() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!--Short View Modal-->
        <div class="modal modal-scrolling-customize fade custom-confirmation-modal" id="notificationDetailsModal" tabindex="-1" aria-labelledby="notificationDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center justify-content-between px-3 pt-3">
                        <h3>{{ translate('Push Notification Short View') }}</h3>
                        <button type="button" class="btn-close bg-light rounded-full" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-30">
                        <div class="">
                            <div class="text-center mx-auto">
                                <img class="mb-20 notification-details-image-view" src="{{asset('public/assets/admin-module')}}/img/short-thumb.png" alt="">
                            </div>
                            <div class="bg-light rounded p-10 mb-15">
                                <div class="bg-white cus-shadow rounded">
                                    <div class="p-12 border-bottom">
                                        <h3 class="mb-2 fz-14 fw-medium">{{ translate('Title') }}</h3>
                                        <p class="fz-12 notif-title">—</p>
                                    </div>
                                    <div class="p-12">
                                        <h3 class="mb-2 fz-14 fw-medium">{{ translate('Description') }}</h3>
                                        <p class="fz-12 notif-description">—</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-light rounded p-10">
                                <div class="bg-white cus-shadow p-12 rounded">
                                    <div class="row g-lg-4 g-3">
                                        <div class="col-lg-6 border-end">
                                            <h3 class="mb-2 fz-14 fw-medium">{{ translate('Zones') }}</h3>
                                            <p class="fz-12 notif-zones">—</p>
                                        </div>
                                        <div class="col-lg-6">
                                            <h3 class="mb-2 fz-14 fw-medium">{{ translate('Targeted user') }}</h3>
                                            <p class="fz-12 notif-users">—</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-light rounded p-10 mb-15 mt-3">
                                <div class="bg-white cus-shadow p-12 rounded">
                                    <div class="row g-lg-4 g-3">
                                        <div class="col-lg-6 border-end">
                                            <h3 class="mb-2 fz-14 fw-medium">{{ translate('Service Category') }}</h3>
                                            <p class="fz-12 notif-category">—</p>
                                        </div>
                                        <div class="col-lg-6">
                                            <h3 class="mb-2 fz-14 fw-medium">{{ translate('On-Tap Action') }}</h3>
                                            <p class="fz-12 notif-action text-capitalize">—</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-light rounded p-10 mt-3">
                                <div class="bg-white cus-shadow p-12 rounded">
                                    <div class="row g-lg-4 g-3">
                                        <div class="col-lg-4 border-end">
                                            <h3 class="mb-2 fz-14 fw-medium">{{ translate('Estimated Reach') }}</h3>
                                            <p class="fz-14 notif-reach text-primary fw-bold">—</p>
                                        </div>
                                        <div class="col-lg-4 border-end">
                                            <h3 class="mb-2 fz-14 fw-medium">{{ translate('Sent Pushes') }}</h3>
                                            <p class="fz-14 notif-sent text-success fw-bold">—</p>
                                        </div>
                                        <div class="col-lg-4">
                                            <h3 class="mb-2 fz-14 fw-medium">{{ translate('Taps Recorded') }}</h3>
                                            <p class="fz-14 notif-taps text-info fw-bold">—</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <div class="choose-option">
                            <div class="d-flex gap-3 justify-content-end flex-wrap">
                                <button type="button" class="btn btn--secondary rounded" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                                <a href="#" class="btn btn--primary rounded px-3 d-flex align-items-center gap-0 resend-action-btn">
                                    <span class="material-symbols-outlined">refresh</span>
                                    {{ translate('Resend') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-end offcanvas-cus-sm" tabindex="-1" id="edit__NotifiCation" aria-labelledby="edit__NotifiCationLabel">
                <div class="offcanvas-header bg-light d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">Edit Push Notification</h2>
                    <div class="d-flex align-items-center gap-2">
                        <a href="#" class="btn btn-outline--primary rounded px-3 d-flex align-items-center gap-0 resend-action-btn">
                            <span class="material-symbols-outlined">refresh</span>
                            Resend
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                </div>

                <form action="#" method="POST" id="update-form-submit" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_tz_offset" class="user_tz_offset_input">
                    <div class="offcanvas-body bg-white">
                        <div class="edit-push-notification">
                            <div class="bg-light rounded p-20 mb-20">
                                <div class="boxe">
                                    <div class="mb-4 text-start">
                                        <h5 class="text-dark fz-14 fw-semibold mb-0">{{ translate('Upload cover image') }} <span class="text-danger">*</span></h5>
                                    </div>
                                    <div class="custom-upload-wrapper upload-group image-upload-wrap1 mb-20 mx-auto">
                                        <input type="file" id="imageUpload02" name="cover_image" accept="image/*">
                                        <label for="imageUpload02" class="upload-box rounded position-relative d-flex align-items-center justify-content-center text-center overflow-hidden bg-white">
                                            <div class="upload-content">
                                            <span class="material-symbols-outlined placeholder-icon mb-2 text-primary">
                                                photo_camera
                                            </span>
                                                <h6 class="fz-10">{{ translate('Add image') }}</h6>
                                            </div>
                                            <img class="image-preview" id="edit_notification_image_preview" src="" alt="Preview" />
                                        </label>
                                    </div>
                                    <p class="opacity-75 fz-12 text-center">{{ translate('JPG, JPEG, PNG Less Than 2MB') }} <span class="text-medium text-dark">(Ratio 2 : 1)</span></p>
                                </div>
                            </div>
                            <div class="bg-light rounded p-20">
                                <div class="message-textarea mb-1">
                                    <div class="mb-2 text-dark d-flex align-items-center gap-1">{{translate('Tittle')}}
                                        <i class="material-icons fz-14 text-light-gray" data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{translate('Write the push notification title')}}"
                                        >info</i>
                                    </div>
                                    <textarea class="form-control block-size-initial" name="title" rows="1" placeholder="Type title" data-maxlength="200"></textarea>
                                    <div class="d-flex justify-content-end mt-1">
                                        <span class="text-light-gray letter-count fz-12">0/100</span>
                                    </div>
                                </div>
                                <div class="message-textarea mb-1">
                                    <div class="mb-2 text-dark d-flex align-items-center gap-1">{{translate('Description')}}
                                        <i class="material-icons fz-14 text-light-gray" data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{translate('Write a description about the push notification')}}"
                                        >info</i>
                                    </div>
                                    <textarea class="form-control block-size-initial" name="description" rows="2" placeholder="Type about the description" data-maxlength="255"></textarea>
                                    <div class="d-flex justify-content-end mt-1">
                                        <span class="text-light-gray letter-count fz-12">0/200</span>
                                    </div>
                                </div>

                                <!-- Zone Selector -->
                                <div class="mb-20">
                                    <div class="mb-2 text-dark d-flex align-items-center gap-1">{{translate('zones')}}
                                        <i class="material-icons fz-14 text-light-gray" data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{translate('Select the zones')}}"
                                        >info</i>
                                    </div>
                                    <div class="position-relative">
                                        <select class="select-zone position-relative theme-input-style w-100" name="zone_ids[]" id="edit_zone_ids" multiple="multiple">
                                            @foreach($zones as $zone)
                                                <option value="{{$zone->id}}">{{$zone->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Targeted Users Selector -->
                                <div class="mb-20">
                                    <div class="mb-2 text-dark d-flex align-items-center gap-1">{{translate('Targeted user')}}
                                        <i class="material-icons fz-14 text-light-gray" data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{translate('Select the targeted user')}}"
                                        >info</i>
                                    </div>
                                    <div class="position-relative">
                                        <select class="select-users position-relative theme-input-style w-100" name="to_users[]" id="edit_to_users" multiple="multiple">
                                            <option value="all">{{translate('all')}}</option>
                                            <option value="customer">{{translate('customer (App & Web)')}}</option>
                                            <option value="partner-admin">{{translate('partner')}}</option>
                                            <option value="provider-admin">{{translate('provider')}}</option>
                                            <option value="provider-serviceman">{{translate('serviceman')}}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Service Category Selector -->
                                <div class="mb-20">
                                    <div class="mb-2 text-dark d-flex align-items-center gap-1">{{translate('Service Category')}}</div>
                                    <select class="form-select theme-input-style w-100" name="category_id" id="edit_category_id">
                                        <option value="">-- {{translate('General Notification')}} --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- On-Tap Action Selector -->
                                <div class="mb-20">
                                    <div class="mb-2 text-dark d-flex align-items-center gap-1">{{translate('On-Tap Action')}}</div>
                                    <select class="form-select theme-input-style w-100" name="on_tap_action" id="edit_on_tap_action">
                                        <option value="general">{{translate('Open App Home / Dashboard (All Roles)')}}</option>
                                        <optgroup label="📱 {{translate('Customer Actions (App & Web)')}}" id="edit_optgroup_customer">
                                            <option value="quote_form">{{translate('Open Quote Request Form')}}</option>
                                            <option value="service_page">{{translate('Open Service Details Page')}}</option>
                                        </optgroup>
                                        <optgroup label="🏢 {{translate('Partner Actions')}}" id="edit_optgroup_partner">
                                            <option value="partner_quotations">{{translate('Quotations & Estimates List')}}</option>
                                            <option value="partner_new_quotation">{{translate('Create New Quotation')}}</option>
                                            <option value="partner_customers">{{translate('Customer Management')}}</option>
                                            <option value="provider_bookings">{{translate('Bookings List')}}</option>
                                        </optgroup>
                                        <optgroup label="🔧 {{translate('Provider Actions')}}" id="edit_optgroup_provider">
                                            <option value="provider_service_requests">{{translate('Service Requests List')}}</option>
                                            <option value="provider_services">{{translate('Available Services & Subscriptions')}}</option>
                                            <option value="provider_bookings">{{translate('Bookings List')}}</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <!-- Audience Type Filter -->
                                <div class="mb-20">
                                    <div class="mb-2 text-dark d-flex align-items-center gap-1">{{translate('Target Audience')}}</div>
                                    <div class="d-flex gap-3 align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="audience_type" id="edit_audience_all" value="all">
                                            <label class="form-check-label fz-12" for="edit_audience_all">{{translate('All Users')}}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="audience_type" id="edit_audience_targeted" value="targeted">
                                            <label class="form-check-label fz-12" for="edit_audience_targeted">{{translate('Targeted Customers')}}</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Edit Scheduling Options --}}
                                <div class="mt-3 pt-3 border-top">
                                    <div class="mb-2 text-dark d-flex align-items-center gap-1 fw-semibold fz-13">
                                        <span class="material-icons fz-16 text-primary">schedule</span>
                                        {{translate('Delivery Timing')}}
                                    </div>
                                    <div class="d-flex flex-wrap gap-3 align-items-center mb-3 p-2 bg-white rounded border">
                                        <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer">
                                            <input class="form-check-input edit-send-type-radio" type="radio" name="send_type" id="edit_send_type_immediate" value="immediate">
                                            <label class="form-check-label cursor-pointer text-dark fz-12" for="edit_send_type_immediate">
                                                <strong>{{translate('Send Immediately')}}</strong>
                                            </label>
                                        </div>
                                        <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer">
                                            <input class="form-check-input edit-send-type-radio" type="radio" name="send_type" id="edit_send_type_scheduled" value="scheduled">
                                            <label class="form-check-label cursor-pointer text-dark fz-12" for="edit_send_type_scheduled">
                                                <strong>{{translate('Schedule for Later')}}</strong>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Edit Scheduled Options Box --}}
                                    <div id="edit_scheduled_delivery_options" class="p-3 bg-white rounded border d-none">
                                        <div class="mb-3">
                                            <label class="form-label text-dark fz-12 fw-semibold mb-1">{{translate('Frequency / Repeat')}}</label>
                                            <select class="form-select select-repeat-type theme-input-style" name="repeat_type" id="edit_repeat_type">
                                                <option value="once">{{translate('One-Time (Specific Date & Time)')}}</option>
                                                <option value="daily">{{translate('Daily Repeating (Every Day at Time)')}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-2" id="edit_scheduled_at_wrapper">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-label text-dark fz-12 fw-semibold mb-0">{{translate('Date & Time Slot(s)')}} <span class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-sm btn-outline--primary py-0 px-2 fz-12 d-flex align-items-center gap-1" id="edit_add_more_datetime_btn">
                                                    <span class="material-icons fz-14">add</span> {{translate('Add More Date')}}
                                                </button>
                                            </div>
                                            <div id="edit_scheduled_datetime_container" class="d-flex flex-column gap-2">
                                                <div class="d-flex align-items-center gap-2 edit-datetime-slot-row">
                                                    <input type="datetime-local" class="form-control theme-input-style" name="scheduled_at[]" id="edit_scheduled_at" min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" value="{{ \Carbon\Carbon::now()->addMinutes(15)->format('Y-m-d\TH:i') }}">
                                                </div>
                                            </div>
                                            <div class="fz-11 text-muted mt-1">{{translate('Select future date and time in company timezone.')}}</div>
                                            <div class="fz-11 text-primary mt-1 fw-semibold"><span class="material-icons fz-12 align-middle">schedule</span> {{translate('Company Timezone')}}: {{ date_default_timezone_get() }} ({{translate('Current Time')}}: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }})</div>
                                        </div>
                                        <div class="mb-2 d-none" id="edit_scheduled_time_wrapper">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-label text-dark fz-12 fw-semibold mb-0">{{translate('Daily Send Time Slot(s)')}} <span class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-sm btn-outline--primary py-0 px-2 fz-12 d-flex align-items-center gap-1" id="edit_add_more_time_btn">
                                                    <span class="material-icons fz-14">add</span> {{translate('Add More Time')}}
                                                </button>
                                            </div>
                                            <div id="edit_scheduled_time_container" class="d-flex flex-column gap-2">
                                                <div class="d-flex align-items-center gap-2 edit-time-slot-row">
                                                    <input type="time" class="form-control theme-input-style" name="scheduled_time[]" id="edit_scheduled_time" value="{{ \Carbon\Carbon::now()->addHour()->minute(0)->format('H:i') }}">
                                                </div>
                                            </div>
                                            <div class="fz-11 text-muted mt-1">{{translate('Notification will automatically fire every day at each configured time slot.')}}</div>
                                            <div class="fz-11 text-primary mt-1 fw-semibold"><span class="material-icons fz-12 align-middle">schedule</span> {{translate('Company Timezone')}}: {{ date_default_timezone_get() }} ({{translate('Current Time')}}: {{ \Carbon\Carbon::now()->format('h:i A') }})</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-footer d-flex gap-3 justify-content-center border-top py-3 px-3">
                        <button type="button" class="btn btn--secondary rounded w-100" data-bs-dismiss="modal">{{ translate('Reset') }}</button>
                        @can('push_notification_update')
                            <button type="submit" class="btn px-xl-4 px-4 btn--primary text-capitalize rounded w-100 demo_check">{{ translate('Update') }}</button>
                        @endcan
                    </div>

                </form>
            </div>
    </div>

@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script>
        "use Strict";

        const COMPANY_TIMEZONE = "{{ date_default_timezone_get() }}";
        const COMPANY_NOW_DATETIME = "{{ \Carbon\Carbon::now()->addMinutes(15)->format('Y-m-d\TH:i') }}";
        const COMPANY_MIN_DATETIME = "{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}";
        const COMPANY_NOW_TIME = "{{ \Carbon\Carbon::now()->addHour()->minute(0)->format('H:i') }}";

        $(document).ready(function () {
            $('.user_tz_offset_input').val(new Date().getTimezoneOffset());

            // Dynamic Reach Calculation Function
            function calculateEstimatedReach() {
                let categoryId = $('#category_selector__select').val();
                let audienceType = $('input[name="audience_type"]:checked').val() || 'all';
                let zoneIds = $('#zone_selector__select4').val() || [];
                let toUsers = $('#user_selector__select').val() || ['all'];

                $('#reach_calculation_status').text('{{ translate("Updating...") }}');

                $.ajax({
                    url: '{{ route("admin.push-notification.estimate-reach") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        category_id: categoryId,
                        audience_type: audienceType,
                        zone_ids: zoneIds,
                        to_users: toUsers
                    },
                    success: function (res) {
                        if (res.success) {
                            $('#estimated_reach_display').text(res.estimated_reach);
                            $('#estimated_reach_input').val(res.estimated_reach);
                            let breakdown = `${res.customer_count} Customers`;
                            if (res.partner_count > 0) breakdown += `, ${res.partner_count} Partners`;
                            if (res.provider_count > 0) breakdown += `, ${res.provider_count} Providers`;
                            if (res.serviceman_count > 0) breakdown += `, ${res.serviceman_count} Servicemen`;
                            $('#reach_breakdown_display').text(breakdown);
                            $('#reach_calculation_status').text('{{ translate("Active Target") }}');
                            $('#preview_summary_reach_text').text(`Reaching ~${res.estimated_reach} recipients (${res.customer_count} Customers)`);
                        }
                    },
                    error: function () {
                        $('#reach_calculation_status').text('{{ translate("Ready") }}');
                    }
                });
            }

            // --- Live Mobile Phone Preview Bindings ---
            $('#notif_title_input').on('input', function() {
                let val = $(this).val();
                $('#preview_notif_title').text(val && val.trim() !== '' ? val : '{{ translate("Notification Title") }}');
            });

            $('#notif_desc_input').on('input', function() {
                let val = $(this).val();
                $('#preview_notif_body').text(val && val.trim() !== '' ? val : '{{ translate("Your push notification description will appear here on the customer mobile screen.") }}');
            });

            $('#imageUpload').on('change', function() {
                if (this.files && this.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview_notif_img').attr('src', e.target.result);
                        $('#phone_notif_image_container').removeClass('d-none');
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    $('#phone_notif_image_container').addClass('d-none');
                    $('#preview_notif_img').attr('src', '');
                }
            });

            function updatePreviewActionTag() {
                let catText = $('#category_selector__select option:selected').text();
                let catVal = $('#category_selector__select').val();
                let actionVal = $('#on_tap_action__select').val();

                let tagText = '{{ translate("Tap to open MMC App") }}';
                if (actionVal === 'quote_form') {
                    tagText = catVal ? '{{ translate("Tap opens") }} ' + catText.trim() + ' {{ translate("Quote Form") }}' : '{{ translate("Tap opens Quote Form") }}';
                } else if (actionVal === 'service_page') {
                    tagText = catVal ? '{{ translate("Tap opens") }} ' + catText.trim() + ' {{ translate("Details") }}' : '{{ translate("Tap opens Service Page") }}';
                } else if (actionVal === 'offer_page') {
                    tagText = '{{ translate("Tap opens Offers & Deals") }}';
                } else if (actionVal === 'partner_quotations') {
                    tagText = '{{ translate("Opens Quotations & Estimates") }}';
                } else if (actionVal === 'partner_new_quotation') {
                    tagText = '{{ translate("Opens Create New Quotation") }}';
                } else if (actionVal === 'partner_customers') {
                    tagText = '{{ translate("Opens Customer Management") }}';
                } else if (actionVal === 'provider_service_requests') {
                    tagText = '{{ translate("Opens Provider Service Requests") }}';
                } else if (actionVal === 'provider_services') {
                    tagText = '{{ translate("Opens Available Services") }}';
                } else if (actionVal === 'provider_bookings') {
                    tagText = '{{ translate("Opens Bookings List") }}';
                }
                $('#preview_notif_action_text').text(tagText);
            }

            function updateActionDropdown() {
                let users = $('#user_selector__select').val() || [];
                let isAll = users.includes('all') || users.length === 0;

                let hasCustomer = users.includes('customer') || isAll;
                let hasPartner = users.includes('partner-admin') || isAll;
                let hasProvider = users.includes('provider-admin') || isAll;

                // Show/hide and enable/disable optgroups according to selected audience
                if (hasCustomer) {
                    $('#optgroup_customer').show().removeAttr('disabled');
                } else {
                    $('#optgroup_customer').hide().attr('disabled', 'disabled');
                }

                if (hasPartner) {
                    $('#optgroup_partner').show().removeAttr('disabled');
                } else {
                    $('#optgroup_partner').hide().attr('disabled', 'disabled');
                }

                if (hasProvider) {
                    $('#optgroup_provider').show().removeAttr('disabled');
                } else {
                    $('#optgroup_provider').hide().attr('disabled', 'disabled');
                }

                // If currently selected option is in a hidden or disabled optgroup, reset to an appropriate option
                let selectedOpt = $('#on_tap_action__select option:selected');
                let parentGroup = selectedOpt.parent('optgroup');
                if (parentGroup.length && (parentGroup.is(':disabled') || parentGroup.css('display') === 'none')) {
                    if (users.length === 1 && users[0] === 'partner-admin') {
                        $('#on_tap_action__select').val('partner_quotations');
                    } else if (users.length === 1 && users[0] === 'provider-admin') {
                        $('#on_tap_action__select').val('provider_service_requests');
                    } else if (users.length === 1 && users[0] === 'customer') {
                        $('#on_tap_action__select').val('quote_form');
                    } else {
                        $('#on_tap_action__select').val('general');
                    }
                }

                updateAudienceHints();
                updatePreviewActionTag();
                toggleAudienceFilterVisibility();
            }

            function updateAudienceHints() {
                let users = $('#user_selector__select').val() || [];
                if (users.length === 1 && users[0] === 'partner-admin') {
                    $('#on_tap_action_hint').html('<span class="material-icons fz-12 align-middle">info</span> {{ translate("Partner will receive notification linking to Quotations, Customers, or Dashboard.") }}');
                } else if (users.length === 1 && users[0] === 'provider-admin') {
                    $('#on_tap_action_hint').html('<span class="material-icons fz-12 align-middle">info</span> {{ translate("Provider will receive notification linking to Service Requests, Services, or Dashboard.") }}');
                } else if (users.length === 1 && users[0] === 'customer') {
                    $('#on_tap_action_hint').html('<span class="material-icons fz-12 align-middle">info</span> {{ translate("Customer taps notification to open MMC App or Website.") }}');
                } else {
                    $('#on_tap_action_hint').html('<span class="material-icons fz-12 align-middle">info</span> {{ translate("Select target screen to open when user clicks notification.") }}');
                }
            }

            function toggleAudienceFilterVisibility() {
                let catVal = $('#category_selector__select').val();
                let catText = $('#category_selector__select option:selected').text();
                let users = $('#user_selector__select').val() || [];
                let hasCustomer = users.includes('customer') || users.includes('all') || users.length === 0;

                if (catVal && catVal.trim() !== '' && hasCustomer) {
                    $('#audience_targeting_filter_wrapper').slideDown(200);
                    $('#targeted_service_label_text').text('{{ translate("Only customers who previously booked or requested quotes for") }} ' + catText.trim());
                } else {
                    $('#audience_targeting_filter_wrapper').slideUp(200);
                    $('#audience_all').prop('checked', true);
                }
            }

            $('#category_selector__select').on('change', function () {
                toggleAudienceFilterVisibility();
                updatePreviewActionTag();
            });

            $('#on_tap_action__select').on('change', updatePreviewActionTag);
            $('#user_selector__select').on('change', updateActionDropdown);

            // Character counters
            $(document).on('input', '#notif_title_input', function () {
                $('.title-count').text($(this).val().length + '/100');
            });
            $(document).on('input', '#notif_desc_input', function () {
                $('.desc-count').text($(this).val().length + '/200');
            });

            // Initial calls
            updateActionDropdown();
            toggleAudienceFilterVisibility();

            $(document).on('change', '#category_selector__select, input[name="audience_type"], #zone_selector__select4, #user_selector__select', function () {
                calculateEstimatedReach();
            });

            // Initial reach calculation on page load
            setTimeout(calculateEstimatedReach, 500);

            // Initialize Select2 with placeholders
            $('.js-select').select2({
                placeholder: "{{ translate('select_items') }}",
                width: '100%'
            });

            $('.select-zone').select2({
                placeholder: "{{ translate('select_zones') }}",
                width: '100%'
            });

            $('.select-user').select2({
                placeholder: "{{ translate('select_users') }}",
                width: '100%'
            });

            // --- Handle Selection in Users ---
            let previousUserValues = $('#user_selector__select').val() || ['all'];
            $('#user_selector__select').on('change', function () {
                var selectedValues = $(this).val();
                if (!selectedValues || selectedValues.length === 0) {
                    previousUserValues = ['all'];
                    $(this).val(['all']).trigger('change');
                    return;
                }
                if (selectedValues.length > 1 && selectedValues.includes('all')) {
                    if (!previousUserValues.includes('all')) {
                        previousUserValues = ['all'];
                        $(this).val(['all']).trigger('change');
                        return;
                    } else {
                        var withoutAll = selectedValues.filter(v => v !== 'all');
                        previousUserValues = withoutAll;
                        $(this).val(withoutAll).trigger('change');
                        return;
                    }
                }
                previousUserValues = selectedValues;
            });

            // --- Handle Selection in Zones ---
            let previousZoneValues = $('#zone_selector__select4').val() || ['all'];
            $('#zone_selector__select4').on('change', function () {
                var selectedValues = $(this).val();
                if (!selectedValues || selectedValues.length === 0) {
                    previousZoneValues = ['all'];
                    $(this).val(['all']).trigger('change');
                    return;
                }
                if (selectedValues.length > 1 && selectedValues.includes('all')) {
                    if (!previousZoneValues.includes('all')) {
                        previousZoneValues = ['all'];
                        $(this).val(['all']).trigger('change');
                        return;
                    } else {
                        var withoutAll = selectedValues.filter(v => v !== 'all');
                        previousZoneValues = withoutAll;
                        $(this).val(withoutAll).trigger('change');
                        return;
                    }
                }
                previousZoneValues = selectedValues;
            });

            $('form#push_notification_create_form').on('reset', function () {
                setTimeout(function () {
                    $('#zone_selector__select4').val(['all']).trigger('change');
                    $('#user_selector__select').val(['all']).trigger('change');
                    $('#category_selector__select').val('').trigger('change');
                    $('#on_tap_action__select').val('general').trigger('change');
                    $('#audience_all').prop('checked', true);
                    $('#send_type_immediate').prop('checked', true).trigger('change');
                    $('#imageUpload').val('');
                    $('.image-preview').attr('src', '').hide();
                    $('#preview_notif_title').text('{{ translate("Notification Title") }}');
                    $('#preview_notif_body').text('{{ translate("Your push notification description will appear here on customer phones.") }}');
                    $('#phone_notif_image_container').addClass('d-none');
                    $('#preview_notif_img').attr('src', '');
                    updatePreviewActionTag();
                    $('.title-count').text('0/100');
                    $('.desc-count').text('0/200');
                    setTimeout(calculateEstimatedReach, 300);
                });
            });
        });


        $(document).on('click', '.show-notification-details', function () {
            const button = $(this);
            const modal = $('#notificationDetailsModal');

            const title = button.data('title');
            const description = button.data('description');
            const zones = button.data('zone_ids');
            const users = button.data('to_users');
            const image = button.data('image');
            const resendUrl = button.data('resend');


            modal.find('.modal-body .notif-title').text(title);
            modal.find('.modal-body .notif-description').text(description);
            modal.find('.notification-details-image-view').attr('src', image).show();

            modal.find('.resend-action-btn').attr('href', resendUrl);

            let zoneText = '';
            try {
                const parsedZones = typeof zones === 'string' ? JSON.parse(zones) : zones;
                zoneText = parsedZones.map(z => z.name).join(', ');
            } catch (e) {
                zoneText = '—';
            }

            // Convert users array to comma-separated list
            let userText = '';
            try {
                const parsedUsers = typeof users === 'string' ? JSON.parse(users) : users;
                userText = parsedUsers.join(', ');
            } catch (e) {
                userText = '—';
            }

            modal.find('.modal-body .notif-zones').text(zoneText);
            modal.find('.modal-body .notif-users').text(userText);
            modal.find('.modal-body .notif-category').text(button.data('category') || '—');
            modal.find('.modal-body .notif-action').text(button.data('action_type') || '—');
            modal.find('.modal-body .notif-reach').text(button.data('reach') || '0');
            modal.find('.modal-body .notif-sent').text(button.data('sent') || '0');
            modal.find('.modal-body .notif-taps').text(button.data('clicks') || '0');
        });

        $(document).on('click', '.edit-notification-btn', function () {
            const button = $(this);
            const title = button.data('title');
            const description = button.data('description');
            const toUsers = button.data('to_users'); // array
            const image = button.data('image');
            const zoneIds = button.data('zone_ids')?.map(z => z.id);
            const actionUrl = button.data('action');
            const resendUrl = button.data('resend');

            const offcanvas = $('#edit__NotifiCation');

            offcanvas.find('form').attr('action', actionUrl);
            offcanvas.find('.resend-action-btn').attr('href', resendUrl);

            // grab references when setting values
            const titleInput = offcanvas.find('textarea[name="title"]').val(title);
            const descInput  = offcanvas.find('textarea[name="description"]').val(description);

            updateLetterCount(titleInput);
            updateLetterCount(descInput);

            $('#edit_zone_ids').val(zoneIds).trigger('change');
            $('#edit_to_users').val(toUsers).trigger('change');
            $('#edit_category_id').val(button.data('category_id') || '');
            $('#edit_on_tap_action').val(button.data('on_tap_action') || 'general');
            let editAudience = button.data('audience_type') || 'all';
            if (editAudience === 'targeted') {
                $('#edit_audience_targeted').prop('checked', true);
            } else {
                $('#edit_audience_all').prop('checked', true);
            }

            if (image) {
                offcanvas.find('#edit_notification_image_preview').attr('src', image).show();
            } else {
                offcanvas.find('#edit_notification_image_preview').hide();
            }

            const sendType = button.data('send_type') || 'immediate';
            const repeatType = button.data('repeat_type') || 'once';
            const scheduledAt = button.data('scheduled_at') || '';
            const scheduledTime = button.data('scheduled_time') || '';

            if (sendType === 'scheduled') {
                $('#edit_send_type_scheduled').prop('checked', true);
            } else {
                $('#edit_send_type_immediate').prop('checked', true);
            }

            $('#edit_scheduled_datetime_container .edit-datetime-slot-row:not(:first)').remove();
            $('#edit_scheduled_time_container .edit-time-slot-row:not(:first)').remove();

            $('#edit_repeat_type').val(repeatType);
            if (!scheduledAt) {
                $('#edit_scheduled_at').val(COMPANY_NOW_DATETIME);
            } else {
                $('#edit_scheduled_at').val(scheduledAt);
            }
            $('#edit_scheduled_at').attr('min', COMPANY_MIN_DATETIME);

            if (!scheduledTime) {
                $('#edit_scheduled_time').val(COMPANY_NOW_TIME);
            } else {
                $('#edit_scheduled_time').val(scheduledTime);
            }

            updateEditScheduledInputsState();
        });

        function updateLetterCount(textarea) {
            const max = textarea.data('maxlength');
            const len = textarea.val().length;
            textarea.closest('.message-textarea')
                .find('.letter-count')
                .text(len + '/' + max);
        }

        // live counter
        $(document).on('input', 'textarea[data-maxlength]', function () {
            updateLetterCount($(this));
        });


        $('#edit_zone_ids, #edit_to_users').select2({
            dropdownParent: $('#edit__NotifiCation')
        });

        function updateEditActionDropdown() {
            let users = $('#edit_to_users').val() || [];
            let isAll = users.includes('all') || users.length === 0;

            let hasCustomer = users.includes('customer') || isAll;
            let hasPartner = users.includes('partner-admin') || isAll;
            let hasProvider = users.includes('provider-admin') || isAll;

            if (hasCustomer) {
                $('#edit_optgroup_customer').show().removeAttr('disabled');
            } else {
                $('#edit_optgroup_customer').hide().attr('disabled', 'disabled');
            }

            if (hasPartner) {
                $('#edit_optgroup_partner').show().removeAttr('disabled');
            } else {
                $('#edit_optgroup_partner').hide().attr('disabled', 'disabled');
            }

            if (hasProvider) {
                $('#edit_optgroup_provider').show().removeAttr('disabled');
            } else {
                $('#edit_optgroup_provider').hide().attr('disabled', 'disabled');
            }
        }

        $('#edit_to_users').on('change', updateEditActionDropdown);

        let selectedNotificationItem;
        let selectedStatusRoute;
        let notificationInitialState;

        $(document).on('change', '.status-update', function (e) {
            e.preventDefault();
            console.log('here')

            selectedNotificationItem = $(this);
            notificationInitialState = selectedNotificationItem.prop('checked');

            // Revert checkbox visual state until confirmation
            selectedNotificationItem.prop('checked', !notificationInitialState);

            let itemId = selectedNotificationItem.data('id');
            selectedStatusRoute = '{{ route('admin.push-notification.status-update', ['id' => ':itemId']) }}'.replace(':itemId', itemId);

            let confirmationTitleText = notificationInitialState
                ? '{{ translate('Are you sure') }}?'
                : '{{ translate('Are you sure') }}?';

            $('.confirmation-title-text').text(confirmationTitleText);

            let confirmationDescriptionText = notificationInitialState
                ? '{{ translate('You want to Turn On the Push Notification Status') }}?'
                : '{{ translate('You want to Turn Off the Push Notification Status') }}?';

            $('.confirmation-description-text').text(confirmationDescriptionText);

            let imgSrc = notificationInitialState
                ? "{{ asset('public/assets/admin-module/img/icons/status-on.png') }}"
                : "{{ asset('public/assets/admin-module/img/icons/status-off.png') }}";

            $('#confirmChangeModal img').attr('src', imgSrc);

            showModal();
        });

        $('#confirmChange').on('click', function () {
            updateStatus(selectedStatusRoute);
        });

        //  Cancel and reset checkbox state
        $('.cancel-change').on('click', function () {
            resetCheckboxState();
            hideModal();
        });

        $('#confirmChangeModal').on('hidden.bs.modal', function () {
            resetCheckboxState();
        });

        //  Show/hide modal functions
        function showModal() {
            $('#confirmChangeModal').modal('show');
        }
        function hideModal() {
            $('#confirmChangeModal').modal('hide');
        }

        //  Reset the checkbox if change canceled
        function resetCheckboxState() {
            if (selectedNotificationItem) {
                selectedNotificationItem.prop('checked', !notificationInitialState);
            }
        }

        // Toggle Scheduled Delivery Options
        function updateScheduledInputsState() {
            let isScheduled = $('#send_type_scheduled').is(':checked');
            if (isScheduled) {
                $('#scheduled_delivery_options').removeClass('d-none');
                $('#scheduled_delivery_options input, #scheduled_delivery_options select').prop('disabled', false);
                toggleRepeatInputs();
            } else {
                $('#scheduled_delivery_options').addClass('d-none');
                $('#scheduled_delivery_options input, #scheduled_delivery_options select').prop('disabled', true);
            }
        }

        $('.send-type-radio').on('change', function () {
            updateScheduledInputsState();
        });

        $('#repeat_type').on('change', function () {
            toggleRepeatInputs();
        });

        function toggleRepeatInputs() {
            let repeat = $('#repeat_type').val();
            if (repeat === 'daily') {
                $('#scheduled_at_wrapper').addClass('d-none');
                $('#scheduled_at_wrapper input').prop('disabled', true);
                $('#scheduled_time_wrapper').removeClass('d-none');
                $('#scheduled_time_wrapper input').prop('disabled', false);
            } else {
                $('#scheduled_at_wrapper').removeClass('d-none');
                $('#scheduled_at_wrapper input').prop('disabled', false);
                $('#scheduled_time_wrapper').addClass('d-none');
                $('#scheduled_time_wrapper input').prop('disabled', true);
            }
        }

        updateScheduledInputsState();

        // Dynamic Add More Datetime Slot (Create)
        $('#add_more_datetime_btn').on('click', function () {
            let rowHtml = `
                <div class="d-flex align-items-center gap-2 datetime-slot-row">
                    <input type="datetime-local" class="form-control theme-input-style" name="scheduled_at[]" min="${COMPANY_MIN_DATETIME}" value="${COMPANY_NOW_DATETIME}">
                    <button type="button" class="btn btn-sm btn--danger p-1 remove-datetime-row" title="{{translate('Remove')}}">
                        <span class="material-icons fz-18">delete</span>
                    </button>
                </div>
            `;
            $('#scheduled_datetime_container').append(rowHtml);
        });

        $(document).on('click', '.remove-datetime-row', function () {
            $(this).closest('.datetime-slot-row').remove();
        });

        // Dynamic Add More Daily Time Slot (Create)
        $('#add_more_time_btn').on('click', function () {
            let rowHtml = `
                <div class="d-flex align-items-center gap-2 time-slot-row">
                    <input type="time" class="form-control theme-input-style" name="scheduled_time[]" value="${COMPANY_NOW_TIME}">
                    <button type="button" class="btn btn-sm btn--danger p-1 remove-time-row" title="{{translate('Remove')}}">
                        <span class="material-icons fz-18">delete</span>
                    </button>
                </div>
            `;
            $('#scheduled_time_container').append(rowHtml);
        });

        $(document).on('click', '.remove-time-row', function () {
            $(this).closest('.time-slot-row').remove();
        });

        // Toggle Edit Scheduled Delivery Options
        function updateEditScheduledInputsState() {
            let isScheduled = $('#edit_send_type_scheduled').is(':checked');
            if (isScheduled) {
                $('#edit_scheduled_delivery_options').removeClass('d-none');
                $('#edit_scheduled_delivery_options input, #edit_scheduled_delivery_options select').prop('disabled', false);
                toggleEditRepeatInputs();
            } else {
                $('#edit_scheduled_delivery_options').addClass('d-none');
                $('#edit_scheduled_delivery_options input, #edit_scheduled_delivery_options select').prop('disabled', true);
            }
        }

        $(document).on('change', '.edit-send-type-radio', function () {
            updateEditScheduledInputsState();
        });

        $(document).on('change', '#edit_repeat_type', function () {
            toggleEditRepeatInputs();
        });

        // Dynamic Add More Datetime Slot (Edit)
        $('#edit_add_more_datetime_btn').on('click', function () {
            let rowHtml = `
                <div class="d-flex align-items-center gap-2 edit-datetime-slot-row">
                    <input type="datetime-local" class="form-control theme-input-style" name="scheduled_at[]" min="${COMPANY_MIN_DATETIME}" value="${COMPANY_NOW_DATETIME}">
                    <button type="button" class="btn btn-sm btn--danger p-1 remove-edit-datetime-row" title="{{translate('Remove')}}">
                        <span class="material-icons fz-18">delete</span>
                    </button>
                </div>
            `;
            $('#edit_scheduled_datetime_container').append(rowHtml);
        });

        $(document).on('click', '.remove-edit-datetime-row', function () {
            $(this).closest('.edit-datetime-slot-row').remove();
        });

        // Dynamic Add More Daily Time Slot (Edit)
        $('#edit_add_more_time_btn').on('click', function () {
            let rowHtml = `
                <div class="d-flex align-items-center gap-2 edit-time-slot-row">
                    <input type="time" class="form-control theme-input-style" name="scheduled_time[]" value="${COMPANY_NOW_TIME}">
                    <button type="button" class="btn btn-sm btn--danger p-1 remove-edit-time-row" title="{{translate('Remove')}}">
                        <span class="material-icons fz-18">delete</span>
                    </button>
                </div>
            `;
            $('#edit_scheduled_time_container').append(rowHtml);
        });

        $(document).on('click', '.remove-edit-time-row', function () {
            $(this).closest('.edit-time-slot-row').remove();
        });

        // Ensure focused empty inputs default to company time so picker opens on company timezone
        $(document).on('focus', 'input[type="datetime-local"]', function () {
            if (!$(this).val()) {
                $(this).val(COMPANY_NOW_DATETIME);
            }
        });
        $(document).on('focus', 'input[type="time"]', function () {
            if (!$(this).val()) {
                $(this).val(COMPANY_NOW_TIME);
            }
        });

        function toggleEditRepeatInputs() {
            let repeat = $('#edit_repeat_type').val();
            if (repeat === 'daily') {
                $('#edit_scheduled_at_wrapper').addClass('d-none');
                $('#edit_scheduled_at_wrapper input').prop('disabled', true);
                $('#edit_scheduled_time_wrapper').removeClass('d-none');
                $('#edit_scheduled_time_wrapper input').prop('disabled', false);
            } else {
                $('#edit_scheduled_at_wrapper').removeClass('d-none');
                $('#edit_scheduled_at_wrapper input').prop('disabled', false);
                $('#edit_scheduled_time_wrapper').addClass('d-none');
                $('#edit_scheduled_time_wrapper input').prop('disabled', true);
            }
        }

        // Validate Create Form Submission
        $('#push_notification_create_form').on('submit', function (e) {
            if ($('#send_type_scheduled').is(':checked')) {
                let repeatType = $('#repeat_type').val();
                if (repeatType === 'once') {
                    let hasValue = false;
                    $('#scheduled_datetime_container input').each(function () {
                        if ($(this).val() && $(this).val().trim() !== '') {
                            hasValue = true;
                        }
                    });
                    if (!hasValue) {
                        e.preventDefault();
                        toastr.error('{{ translate('Please select at least one date and time slot') }}');
                        return false;
                    }
                } else if (repeatType === 'daily') {
                    let hasValue = false;
                    $('#scheduled_time_container input').each(function () {
                        if ($(this).val() && $(this).val().trim() !== '') {
                            hasValue = true;
                        }
                    });
                    if (!hasValue) {
                        e.preventDefault();
                        toastr.error('{{ translate('Please select at least one daily send time') }}');
                        return false;
                    }
                }
            }
        });

        // Validate Edit Drawer Form Submission
        $('#edit__NotifiCation form').on('submit', function (e) {
            if ($('#edit_send_type_scheduled').is(':checked')) {
                let repeatType = $('#edit_repeat_type').val();
                if (repeatType === 'once') {
                    let hasValue = false;
                    $('#edit_scheduled_datetime_container input').each(function () {
                        if ($(this).val() && $(this).val().trim() !== '') {
                            hasValue = true;
                        }
                    });
                    if (!hasValue) {
                        e.preventDefault();
                        toastr.error('{{ translate('Please select at least one date and time slot') }}');
                        return false;
                    }
                } else if (repeatType === 'daily') {
                    let hasValue = false;
                    $('#edit_scheduled_time_container input').each(function () {
                        if ($(this).val() && $(this).val().trim() !== '') {
                            hasValue = true;
                        }
                    });
                    if (!hasValue) {
                        e.preventDefault();
                        toastr.error('{{ translate('Please select at least one daily send time') }}');
                        return false;
                    }
                }
            }
        });


    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>
@endpush

