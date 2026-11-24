@extends('adminmodule::layouts.master')

@section('title',translate('push_notification'))

@push('css_or_js')
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
                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.push-notification.update',[$pushNotification->id])}}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="user_tz_offset" class="user_tz_offset_input">
                                <div class="row">
                                    <div class="col-lg-6 mb-4 mb-lg-0">
                                        <div class="form-floating form-floating__icon mb-30">
                                            <input type="text" class="form-control" id="floatingInput" name="title"
                                                   placeholder="{{translate('Title')}}" required="" maxlength="191" value="{{$pushNotification->title}}">
                                            <label for="floatingInput">{{translate('title')}}</label>
                                            <span class="material-icons">title</span>
                                        </div>
                                        <div class="form-floating mb-30">
                                            <textarea class="form-control resize-none" id="floatingInput2"
                                                      placeholder="{{translate('description')}}"
                                                      name="description">{{$pushNotification->description}}</textarea>
                                            <label for="floatingInput2">{{translate('description')}}</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-30">
                                                    <select class="select-zone theme-input-style w-100"
                                                            name="zone_ids[]" id="zone_selector__select" multiple="multiple">
                                                        <option value="all">{{translate('Select All')}}</option>
                                                        @foreach($zones as $zone)
                                                            <option value="{{$zone->id}}" {{in_array($zone->id,collect($pushNotification['zone_ids'])->pluck('id')->toArray())?'selected':''}}>{{$zone->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-30">
                                                    <select class="select-user theme-input-style w-100"
                                                            name="to_users[]" id="user_selector__select" multiple="multiple">
                                                        <option value="all">{{translate('all')}}</option>
                                                        <option value="customer" {{in_array('customer',$pushNotification->to_users)?'selected':''}}>
                                                            {{translate('customer')}}
                                                        </option>
                                                        <option value="provider-admin" {{in_array('provider-admin',$pushNotification->to_users)?'selected':''}}>
                                                            {{translate('provider')}}
                                                        </option>
                                                        <option value="provider-serviceman" {{in_array('provider-serviceman',$pushNotification->to_users)?'selected':''}}>
                                                            {{translate('serviceman')}}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Service Category & Deep Link Options --}}
                                        <div class="row mb-30">
                                            <div class="col-lg-6">
                                                <label class="form-label text-dark fz-12 fw-semibold mb-1">{{translate('Service Category')}}</label>
                                                <select class="form-select theme-input-style w-100" name="category_id" id="category_selector__select">
                                                    <option value="">-- {{translate('General Notification (No Category)')}} --</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{$cat->id}}" {{ old('category_id', $pushNotification->category_id) == $cat->id ? 'selected' : '' }}>{{$cat->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label text-dark fz-12 fw-semibold mb-1">{{translate('On-Tap Action (Deep Link)')}}</label>
                                                <select class="form-select theme-input-style w-100" name="on_tap_action" id="on_tap_action__select">
                                                    <option value="general" {{ old('on_tap_action', $pushNotification->on_tap_action ?? 'general') == 'general' ? 'selected' : '' }}>{{translate('Open App Home / General')}}</option>
                                                    <option value="quote_form" {{ old('on_tap_action', $pushNotification->on_tap_action) == 'quote_form' ? 'selected' : '' }}>{{translate('Open Quote Request Form')}}</option>
                                                    <option value="service_page" {{ old('on_tap_action', $pushNotification->on_tap_action) == 'service_page' ? 'selected' : '' }}>{{translate('Open Service Details Page')}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Target Audience Filter --}}
                                        <div class="mb-30">
                                            <label class="form-label text-dark fz-12 fw-semibold mb-1">{{translate('Target Audience')}}</label>
                                            <div class="d-flex gap-4 align-items-center p-3 bg-white rounded border">
                                                <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer">
                                                    <input class="form-check-input" type="radio" name="audience_type" id="audience_all" value="all" {{ old('audience_type', $pushNotification->audience_type ?? 'all') == 'all' ? 'checked' : '' }}>
                                                    <label class="form-check-label cursor-pointer text-dark fz-12" for="audience_all">
                                                        <strong>{{translate('All Eligible Users in Area')}}</strong>
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer">
                                                    <input class="form-check-input" type="radio" name="audience_type" id="audience_targeted" value="targeted" {{ old('audience_type', $pushNotification->audience_type) == 'targeted' ? 'checked' : '' }}>
                                                    <label class="form-check-label cursor-pointer text-dark fz-12" for="audience_targeted">
                                                        <strong>{{translate('Targeted Customers Only')}}</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Edit Scheduling Options --}}
                                        <div class="mt-3 pt-3 border-top">
                                            <div class="mb-2 text-dark d-flex align-items-center gap-1 fw-semibold fz-13">
                                                <span class="material-icons fz-16 text-primary">schedule</span>
                                                {{translate('Delivery Timing')}}
                                            </div>
                                            <div class="d-flex flex-wrap gap-4 align-items-center mb-3 p-3 bg-white rounded border">
                                                <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer">
                                                    <input class="form-check-input send-type-radio" type="radio" name="send_type" id="send_type_immediate" value="immediate" {{ old('send_type', $pushNotification->send_type ?? 'immediate') == 'immediate' ? 'checked' : '' }}>
                                                    <label class="form-check-label cursor-pointer text-dark fz-13" for="send_type_immediate">
                                                        <strong>{{translate('Send Immediately')}}</strong>
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2 m-0 cursor-pointer">
                                                    <input class="form-check-input send-type-radio" type="radio" name="send_type" id="send_type_scheduled" value="scheduled" {{ old('send_type', $pushNotification->send_type ?? '') == 'scheduled' ? 'checked' : '' }}>
                                                    <label class="form-check-label cursor-pointer text-dark fz-13" for="send_type_scheduled">
                                                        <strong>{{translate('Schedule for Later')}}</strong>
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Scheduled Options Box --}}
                                            <div id="scheduled_delivery_options" class="p-3 bg-white rounded border {{ old('send_type', $pushNotification->send_type ?? '') == 'scheduled' ? '' : 'd-none' }}">
                                                <div class="mb-3">
                                                    <label class="form-label text-dark fz-12 fw-semibold mb-1">{{translate('Frequency / Repeat')}}</label>
                                                    <select class="form-select select-repeat-type theme-input-style" name="repeat_type" id="repeat_type">
                                                        <option value="once" {{ old('repeat_type', $pushNotification->repeat_type ?? 'once') == 'once' ? 'selected' : '' }}>{{translate('One-Time (Specific Date & Time)')}}</option>
                                                        <option value="daily" {{ old('repeat_type', $pushNotification->repeat_type ?? '') == 'daily' ? 'selected' : '' }}>{{translate('Daily Repeating (Every Day at Time)')}}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2" id="scheduled_at_wrapper">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label text-dark fz-12 fw-semibold mb-0">{{translate('Date & Time Slot(s)')}} <span class="text-danger">*</span></label>
                                                        <button type="button" class="btn btn-sm btn-outline--primary py-0 px-2 fz-12 d-flex align-items-center gap-1" id="add_more_datetime_btn">
                                                            <span class="material-icons fz-14">add</span> {{translate('Add More Date')}}
                                                        </button>
                                                    </div>
                                                    <div id="scheduled_datetime_container" class="d-flex flex-column gap-2">
                                                        <div class="d-flex align-items-center gap-2 datetime-slot-row">
                                                            <input type="datetime-local" class="form-control theme-input-style" name="scheduled_at[]" id="scheduled_at" min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" value="{{ old('scheduled_at', $pushNotification->scheduled_at ? $pushNotification->scheduled_at->format('Y-m-d\TH:i') : \Carbon\Carbon::now()->addMinutes(15)->format('Y-m-d\TH:i')) }}">
                                                        </div>
                                                    </div>
                                                    <div class="fz-11 text-muted mt-1">{{translate('You can add multiple dates and times. Click "+ Add More Date" to schedule more.')}}</div>
                                                    <div class="fz-11 text-primary mt-1 fw-semibold"><span class="material-icons fz-12 align-middle">schedule</span> {{translate('Company Timezone')}}: {{ date_default_timezone_get() }} ({{translate('Current Time')}}: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }})</div>
                                                </div>
                                                <div class="mb-2 d-none" id="scheduled_time_wrapper">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label text-dark fz-12 fw-semibold mb-0">{{translate('Daily Send Time Slot(s)')}} <span class="text-danger">*</span></label>
                                                        <button type="button" class="btn btn-sm btn-outline--primary py-0 px-2 fz-12 d-flex align-items-center gap-1" id="add_more_time_btn">
                                                            <span class="material-icons fz-14">add</span> {{translate('Add More Time')}}
                                                        </button>
                                                    </div>
                                                    <div id="scheduled_time_container" class="d-flex flex-column gap-2">
                                                        <div class="d-flex align-items-center gap-2 time-slot-row">
                                                            <input type="time" class="form-control theme-input-style" name="scheduled_time[]" id="scheduled_time" value="{{ old('scheduled_time', $pushNotification->scheduled_time ?? \Carbon\Carbon::now()->addHour()->minute(0)->format('H:i')) }}">
                                                        </div>
                                                    </div>
                                                    <div class="fz-11 text-muted mt-1">{{translate('Notification will automatically fire every day at each configured time slot.')}}</div>
                                                    <div class="fz-11 text-primary mt-1 fw-semibold"><span class="material-icons fz-12 align-middle">schedule</span> {{translate('Company Timezone')}}: {{ date_default_timezone_get() }} ({{translate('Current Time')}}: {{ \Carbon\Carbon::now()->format('h:i A') }})</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="d-flex flex-column align-items-center gap-3">
                                            <p class="title-color mb-0">{{translate('upload_cover_image')}}</p>

                                            <div class="upload-file">
                                                <input type="file" class="upload-file__input" name="cover_image" accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*">
                                                <div class="upload-file__img upload-file__img_banner">
                                                    <img
                                                        src="{{$pushNotification->cover_image_full_path}}">
                                                </div>
                                                <span class="upload-file__edit">
                                                    <span class="material-icons">edit</span>
                                                </span>
                                            </div>

                                            <p class="opacity-75 max-w220 mx-auto">{{translate('Image format - jpg,
                                                png, jpeg, gif Image Size - maximum size 2 MB Image
                                                Ratio - 2:1')}}</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-20 mt-30">
                                            <button class="btn btn--secondary"
                                                    type="reset">{{translate('reset')}}</button>
                                            <button class="btn btn--primary demo_check"
                                                    type="submit">{{translate('update')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>

    <script>
        "use Strict";

        $('#user_selector__select').on('change', function() {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

        $('#zone_selector__select').on('change', function() {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

        const COMPANY_TIMEZONE = "{{ date_default_timezone_get() }}";
        const COMPANY_NOW_DATETIME = "{{ \Carbon\Carbon::now()->addMinutes(15)->format('Y-m-d\TH:i') }}";
        const COMPANY_MIN_DATETIME = "{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}";
        const COMPANY_NOW_TIME = "{{ \Carbon\Carbon::now()->addHour()->minute(0)->format('H:i') }}";

        $(document).ready(function () {
            $('.user_tz_offset_input').val(new Date().getTimezoneOffset());
            $('.js-select').select2({
                placeholder: "{{translate('select_items')}}",
            });
            $('.select-zone').select2({
                placeholder: "{{translate('select_zones')}}",
            });
            $('.select-user').select2({
                placeholder: "{{translate('select_users')}}",
            });

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

            // Dynamic Add More Datetime Slot (Edit Standalone)
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

            // Dynamic Add More Daily Time Slot (Edit Standalone)
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

            // Form Submit Validation
            $('form').on('submit', function (e) {
                if ($('#send_type_scheduled').is(':checked')) {
                    let repeatType = $('#repeat_type').val();
                    if (repeatType === 'once') {
                        let hasVal = false;
                        $('#scheduled_datetime_container input').each(function () {
                            if ($(this).val() && $(this).val().trim() !== '') {
                                hasVal = true;
                            }
                        });
                        if (!hasVal) {
                            e.preventDefault();
                            toastr.error('{{ translate('Please select at least one date and time slot') }}');
                            return false;
                        }
                    } else if (repeatType === 'daily') {
                        let hasVal = false;
                        $('#scheduled_time_container input').each(function () {
                            if ($(this).val() && $(this).val().trim() !== '') {
                                hasVal = true;
                            }
                        });
                        if (!hasVal) {
                            e.preventDefault();
                            toastr.error('{{ translate('Please select at least one daily send time') }}');
                            return false;
                        }
                    }
                }
            });
        });

    </script>
@endpush
