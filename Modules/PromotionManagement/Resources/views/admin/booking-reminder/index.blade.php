@extends('adminmodule::layouts.new-master')

@section('title', translate('Booking Reminder Notifications'))

@push('css_or_js')
    <style>
        .reminder-stage-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease-in-out;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 24px;
        }
        .reminder-stage-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.07);
            border-color: #cbd5e1;
        }
        .reminder-stage-card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 24px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #ffffff;
            color: #4f46e5;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid #c7d2fe;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .tag-pill:hover {
            background: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
        }
        .table-custom-responsive thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
        }
        .table-custom-responsive tbody td {
            padding: 14px 16px;
            vertical-align: middle;
        }
        .reminder-type-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .custom-select.form-select {
            height: 45px;
            border-radius: 6px;
            border: 1px solid #d0d7de;
            background-color: #ffffff;
            font-size: 13px;
            color: #334155;
            padding: 8px 36px 8px 14px;
            cursor: pointer;
            box-shadow: none;
            min-width: 170px;
        }
        .custom-select.form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.15);
        }
        .h-45 {
            height: 45px !important;
        }
        .rule-name-input:focus {
            border-bottom-color: #007bff !important;
            outline: none;
            background-color: #f8fafc !important;
        }
    </style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Title & Header Actions -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="page-title mb-1">
                    <span class="material-icons align-middle text-primary me-1">alarm</span>
                    {{ translate('Automated Booking Reminders') }}
                </h2>
                <p class="text-muted fs-13 mb-0">
                    {{ translate('Configure automatic push notification alerts sent to providers relative to their scheduled service time.') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('admin.booking-reminder.run-scheduler') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn--secondary h-45 d-flex align-items-center gap-2 shadow-sm" title="{{ translate('Trigger the background check right now') }}">
                        <span class="material-icons fs-18">sync</span>
                        {{ translate('Run Scheduler Now') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- System Status Banner -->
        <div class="card mb-30 border-0 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-white rounded-circle shadow-sm">
                            <span class="material-icons text-success fs-28">notifications_active</span>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark fw-bold">{{ translate('Background Automation Status') }}</h6>
                            <p class="mb-0 fs-12 text-muted">
                                {{ translate('Cron scheduler runs every minute in background.') }}
                                <strong>{{ translate('Server Time') }}:</strong> {{ \Carbon\Carbon::now()->format('d M Y, h:i A (e)') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge {{ !empty($settings['is_active']) ? 'bg-success' : 'bg-secondary' }} px-3 py-2 fs-12 rounded-pill">
                            {{ !empty($settings['is_active']) ? translate('Automation Active') : translate('Automation Paused') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs nav--tabs border-bottom mb-4" id="reminderTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ !request()->hasAny(['page', 'search', 'reminder_type', 'target_type']) ? 'active' : '' }} d-flex align-items-center gap-2" id="provider-tab" data-bs-toggle="tab" href="#providerTabPane" role="tab">
                    <span class="material-icons fs-18">tune</span>
                    <span>{{ translate('Provider Reminders Settings') }}</span>
                    @if(!empty($settings['is_active']))
                        <span class="badge bg-success rounded-pill ms-1 fs-10">{{ count($providerRules) }} {{ translate('Rules') }}</span>
                    @else
                        <span class="badge bg-secondary rounded-pill ms-1 fs-10">{{ translate('OFF') }}</span>
                    @endif
                </a>
            </li>
            {{-- Customer Reminders Tab (Temporarily commented out - uncomment when customer reminders are needed)
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" id="customer-tab" data-bs-toggle="tab" href="#customerTabPane" role="tab">
                    <span class="material-icons fs-18">person</span>
                    <span>{{ translate('Customer Reminders') }}</span>
                </a>
            </li>
            --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->hasAny(['page', 'search', 'reminder_type', 'target_type']) ? 'active' : '' }} d-flex align-items-center gap-2" id="logs-tab" data-bs-toggle="tab" href="#logsTabPane" role="tab">
                    <span class="material-icons fs-18">history</span>
                    <span>{{ translate('Sent Reminders History') }}</span>
                </a>
            </li>
        </ul>

        <!-- Tab Panes Container -->
        <div class="tab-content" id="reminderTabsContent">

            <!-- ======================================================== -->
            <!-- TAB 1: PROVIDER REMINDER SETTINGS (DYNAMIC RULES)        -->
            <!-- ======================================================== -->
            <div class="tab-pane fade {{ !request()->hasAny(['page', 'search', 'reminder_type', 'target_type']) ? 'show active' : '' }}" id="providerTabPane" role="tabpanel">
                <form action="{{ route('admin.booking-reminder.update') }}" method="POST" id="reminderSettingsForm">
                    @csrf

                    <!-- Master Control Banner -->
                    <div class="card mb-30 border-0 shadow-sm">
                        <div class="card-body p-20">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <span class="material-icons fs-24">notifications_active</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 text-dark fw-bold">{{ translate('Master Automation Switch') }}</h5>
                                        <p class="text-muted fs-13 mb-0">{{ translate('Enable or pause automatic push notification reminders for assigned provider partners.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 bg-light px-3 py-2 rounded-pill border">
                                    <span class="fs-13 fw-semibold text-dark">{{ translate('Status') }}:</span>
                                    <label class="switcher mb-0">
                                        <input class="switcher_input" type="checkbox" name="is_active" id="masterActiveSwitch" value="1" {{ !empty($settings['is_active']) ? 'checked' : '' }}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <span class="badge {{ !empty($settings['is_active']) ? 'bg-success' : 'bg-secondary' }} rounded-pill px-2 py-1 fs-11">
                                        {{ !empty($settings['is_active']) ? translate('Active') : translate('Paused') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Variables Helper Bar -->
                    <div class="card mb-30 border-0 shadow-sm" style="background: #f8fafc;">
                        <div class="card-body p-20">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons text-primary fs-18">code</span>
                                    <span class="fw-bold fs-13 text-dark">{{ translate('Template Variables') }}</span>
                                    <span class="text-muted fs-12">{{ translate('(Click any tag to copy into title or message body)') }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 pt-1">
                                <span class="tag-pill" onclick="insertTag(this)" title="{{ translate('Booking code/ID') }}"><span class="material-icons fs-12">content_copy</span> {booking_id}</span>
                                <span class="tag-pill" onclick="insertTag(this)" title="{{ translate('Provider company or partner name') }}"><span class="material-icons fs-12">content_copy</span> {provider_name}</span>
                                <span class="tag-pill" onclick="insertTag(this)" title="{{ translate('Customer full name') }}"><span class="material-icons fs-12">content_copy</span> {customer_name}</span>
                                <span class="tag-pill" onclick="insertTag(this)" title="{{ translate('Scheduled booking date and time') }}"><span class="material-icons fs-12">content_copy</span> {service_schedule}</span>
                                <span class="tag-pill" onclick="insertTag(this)" title="{{ translate('Primary service name') }}"><span class="material-icons fs-12">content_copy</span> {service_name}</span>
                                <span class="tag-pill" onclick="insertTag(this)" title="{{ translate('Remaining hours count') }}"><span class="material-icons fs-12">content_copy</span> {hours}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rules List Header & Add Button -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <span class="material-icons text-primary fs-20">format_list_bulleted</span>
                                {{ translate('Configured Reminder Schedules') }}
                            </h5>
                            <small class="text-muted">{{ translate('You can add as many reminder rules as you need (e.g. 48h before, morning, 2h before, 30m before, etc.).') }}</small>
                        </div>
                        <button type="button" class="btn btn--primary d-flex align-items-center gap-1 h-45 px-3 shadow-sm" onclick="addNewReminderRule()">
                            <span class="material-icons fs-18">add_circle</span>
                            {{ translate('Add Reminder Rule') }}
                        </button>
                    </div>

                    <!-- Dynamic Rules Cards Container -->
                    <div id="reminderRulesContainer">
                        @foreach($providerRules as $index => $rule)
                            @php($triggerType = $rule['trigger_type'] ?? 'before_hours')
                            <div class="reminder-stage-card rule-card-item" id="ruleCard_{{ $index }}" data-index="{{ $index }}">
                                <input type="hidden" name="rules[{{ $index }}][id]" value="{{ $rule['id'] ?? ('rule_' . uniqid()) }}">
                                
                                <div class="reminder-stage-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                                        <span class="badge bg-primary text-white px-3 py-2 rounded-pill fs-12 fw-bold d-flex align-items-center gap-1 rule-number-badge">
                                            <span class="material-icons fs-16">schedule</span>
                                            <span class="rule-index-label">{{ translate('Rule') }} #{{ $index + 1 }}</span>
                                        </span>
                                        <div class="flex-grow-1" style="max-width: 480px;">
                                            <input type="text" class="form-control form-control-sm fw-bold fs-14 border-0 border-bottom rounded-0 px-2 bg-transparent rule-name-input" name="rules[{{ $index }}][name]" value="{{ $rule['name'] ?? ('Reminder ' . ($index + 1)) }}" placeholder="{{ translate('Enter a custom name for this reminder...') }}" required>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fs-12 fw-semibold text-muted">{{ translate('Active') }}:</span>
                                            <label class="switcher mb-0">
                                                <input class="switcher_input" type="checkbox" name="rules[{{ $index }}][is_active]" value="1" {{ !empty($rule['is_active']) ? 'checked' : '' }}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle delete-rule-btn" onclick="removeRuleCard(this)" title="{{ translate('Delete this reminder rule') }}">
                                            <span class="material-icons fs-18 align-middle">delete</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body p-4">
                                    <div class="row g-4 align-items-start">
                                        <!-- Trigger Condition Column -->
                                        <div class="col-lg-4">
                                            <div class="bg-light p-3 rounded border">
                                                <label class="form-label fs-13 fw-bold text-dark mb-2">
                                                    <span class="material-icons fs-16 align-middle text-primary me-1">tune</span>
                                                    {{ translate('Trigger Condition') }}
                                                </label>
                                                
                                                <div class="mb-3">
                                                    <select class="custom-select form-select w-100 trigger-type-select" name="rules[{{ $index }}][trigger_type]" onchange="toggleTriggerType(this)">
                                                        <option value="before_hours" {{ $triggerType === 'before_hours' ? 'selected' : '' }}>
                                                            ⏱️ {{ translate('Hours Before Schedule') }}
                                                        </option>
                                                        <option value="day_morning" {{ $triggerType === 'day_morning' ? 'selected' : '' }}>
                                                            🌅 {{ translate('On Service Day at Specific Time') }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <!-- Hours Input (Shown when trigger_type == before_hours) -->
                                                <div class="trigger-hours-box {{ $triggerType === 'day_morning' ? 'd-none' : '' }}">
                                                    <div class="input-group mb-2">
                                                        <input type="number" step="0.5" min="0.5" max="168" class="form-control h-45 fw-semibold fs-14" name="rules[{{ $index }}][trigger_value_hours]" value="{{ $triggerType === 'before_hours' ? $rule['trigger_value'] : 24 }}">
                                                        <span class="input-group-text bg-white text-muted fs-13">{{ translate('Hours Before') }}</span>
                                                    </div>
                                                    <div class="fs-11 text-muted d-flex align-items-start gap-1">
                                                        <span class="material-icons fs-14 text-primary mt-1">info</span>
                                                        <span>{{ translate('Triggers automatically when a booking schedule is within these hours remaining.') }}</span>
                                                    </div>
                                                </div>

                                                <!-- Specific Time Input (Shown when trigger_type == day_morning) -->
                                                <div class="trigger-time-box {{ $triggerType === 'day_morning' ? '' : 'd-none' }}">
                                                    <div class="input-group mb-2">
                                                        <input type="time" class="form-control h-45 fw-semibold fs-14" name="rules[{{ $index }}][trigger_value_time]" value="{{ $triggerType === 'day_morning' ? $rule['trigger_value'] : '07:00' }}">
                                                    </div>
                                                    <div class="fs-11 text-muted d-flex align-items-start gap-1">
                                                        <span class="material-icons fs-14 text-primary mt-1">info</span>
                                                        <span>{{ translate('Dispatched on the booking date at this morning time (Server timezone).') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notification Content Column -->
                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label class="form-label fs-13 fw-semibold text-dark">{{ translate('Notification Title') }}</label>
                                                <input type="text" class="form-control h-45" name="rules[{{ $index }}][title]" value="{{ $rule['title'] ?? 'Booking Reminder: #{booking_id}' }}" placeholder="{{ translate('Enter notification title...') }}" required>
                                            </div>
                                            <div>
                                                <label class="form-label fs-13 fw-semibold text-dark">{{ translate('Message Body') }}</label>
                                                <textarea class="form-control" name="rules[{{ $index }}][message]" rows="3" placeholder="{{ translate('Enter notification message...') }}" required>{{ $rule['message'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Bottom Action Bar -->
                    <div class="card border-0 shadow-sm bg-white p-3 mb-4 rounded-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <span class="text-muted fs-13 d-flex align-items-center gap-1">
                                <span class="material-icons fs-16 text-success">check_circle</span>
                                {{ translate('All reminder rules are evaluated automatically by the background cron scheduler.') }}
                            </span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary h-45 d-flex align-items-center gap-1 px-3" onclick="addNewReminderRule()">
                                    <span class="material-icons fs-18">add</span>
                                    {{ translate('Add Another Rule') }}
                                </button>
                                <button type="submit" class="btn btn--primary px-4 py-2 fs-14 fw-semibold shadow-sm d-flex align-items-center gap-2 h-45">
                                    <span class="material-icons fs-18">save</span>
                                    {{ translate('Save All Reminder Settings') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 
            ====================================================================
            CUSTOMER BOOKING REMINDERS (TEMPORARILY COMMENTED OUT)
            Uncomment this block whenever customer reminders are needed.
            ====================================================================
            <div class="tab-pane fade" id="customerTabPane" role="tabpanel">
                ...
            </div>
            --}}

            <!-- ======================================================== -->
            <!-- TAB 2: SENT REMINDERS HISTORY (REDESIGNED)               -->
            <!-- ======================================================== -->
            <div class="tab-pane fade {{ request()->hasAny(['page', 'search', 'reminder_type', 'target_type']) ? 'show active' : '' }}" id="logsTabPane" role="tabpanel">
                <div class="card mb-30 border-0 shadow-sm">
                    <div class="card-body p-20">
                        <!-- Table Top Controls (Demandium Standard) -->
                        <div class="data-table-top d-flex align-items-center flex-wrap gap-10 justify-content-between mb-20 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="mb-0 fw-bold text-dark">{{ translate('Sent Reminders History') }}</h4>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 fs-12">{{ $logs->total() }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-sm-3 gap-2 flex-sm-nowrap flex-wrap">
                                <!-- Search Form -->
                                <form action="{{ route('admin.booking-reminder.index') }}" class="search-form search-form_style-two" method="GET">
                                    <input type="hidden" name="tab" value="logs">
                                    @if(request('reminder_type'))
                                        <input type="hidden" name="reminder_type" value="{{ request('reminder_type') }}">
                                    @endif
                                    <div class="input-group search-form__input_group">
                                        <span class="search-form__icon">
                                            <span class="material-icons">search</span>
                                        </span>
                                        <input type="search" class="theme-input-style search-form__input"
                                            value="{{ request('search') }}" name="search"
                                            placeholder="{{ translate('Search by booking ID, title...') }}">
                                    </div>
                                    <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                </form>

                                <!-- Reminder Type Filter Dropdown -->
                                <div>
                                    <select class="custom-select form-select" name="reminder_type" onchange="filterByReminderType(this.value)">
                                        <option value="">{{ translate('All Reminder Types') }}</option>
                                        <option value="advance" {{ request('reminder_type') == 'advance' ? 'selected' : '' }}>{{ translate('Advance Reminders') }}</option>
                                        <option value="morning" {{ request('reminder_type') == 'morning' ? 'selected' : '' }}>{{ translate('Morning Alerts') }}</option>
                                        <option value="final" {{ request('reminder_type') == 'final' ? 'selected' : '' }}>{{ translate('Urgent Alerts') }}</option>
                                    </select>
                                </div>

                                @if(request()->hasAny(['reminder_type', 'target_type', 'search']))
                                    <a href="{{ route('admin.booking-reminder.index') }}?tab=logs" class="btn btn--secondary d-flex align-items-center gap-1 h-45">
                                        <span class="material-icons fs-16">refresh</span>
                                        {{ translate('Reset') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive table-custom-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="text-nowrap bg-light">
                                    <tr>
                                        <th class="text-dark fw-semibold bg-light">{{ translate('Sl') }}</th>
                                        <th class="text-dark fw-semibold bg-light">{{ translate('Booking ID') }}</th>
                                        <th class="text-dark fw-semibold bg-light">{{ translate('Recipient') }}</th>
                                        <th class="text-dark fw-semibold bg-light">{{ translate('Reminder Type / ID') }}</th>
                                        <th class="text-dark fw-semibold bg-light">{{ translate('Notification Content') }}</th>
                                        <th class="text-dark fw-semibold bg-light">{{ translate('Dispatched At') }}</th>
                                        <th class="text-dark fw-semibold bg-light text-center">{{ translate('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $key => $log)
                                        <tr>
                                            <!-- Sl -->
                                            <td class="text-muted fs-13">{{ $key + $logs->firstItem() }}</td>

                                            <!-- Booking ID & Service Schedule -->
                                            <td>
                                                <div>
                                                    <a href="{{ route('admin.booking.details', [$log->booking_id, 'web_page' => 'details']) }}" class="fw-bold text-primary text-decoration-none" target="_blank">
                                                        #{{ $log->booking?->readable_id ?? substr($log->booking_id, 0, 8) }}
                                                    </a>
                                                    @if($log->booking?->service_schedule)
                                                        <div class="fs-11 text-muted d-flex align-items-center gap-1 mt-1">
                                                            <span class="material-icons fs-12 text-muted">schedule</span>
                                                            {{ \Carbon\Carbon::parse($log->booking->service_schedule)->format('d M Y, h:i A') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Recipient User -->
                                            <td>
                                                <div>
                                                    <div class="fw-semibold text-dark fs-13">
                                                        {{ $log->user?->first_name ?? 'Provider' }} {{ $log->user?->last_name ?? '' }}
                                                    </div>
                                                    @if($log->user?->phone)
                                                        <div class="fs-11 text-muted d-flex align-items-center gap-1">
                                                            <span class="material-icons fs-12 text-muted">call</span>
                                                            {{ $log->user->phone }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Reminder Stage Badge -->
                                            <td>
                                                <span class="reminder-type-badge bg-info-subtle text-info border border-info-subtle">
                                                    <span class="material-icons fs-14">notifications</span>
                                                    {{ ucfirst(str_replace(['rule_', '_'], ['', ' '], $log->reminder_type)) }}
                                                </span>
                                            </td>

                                            <!-- Notification Title & Body -->
                                            <td style="max-width: 340px;">
                                                <div class="fw-semibold text-dark fs-13 text-truncate" title="{{ $log->title }}">
                                                    {{ $log->title }}
                                                </div>
                                                <div class="text-muted fs-12 text-truncate mt-1" title="{{ $log->body }}">
                                                    {{ $log->body }}
                                                </div>
                                            </td>

                                            <!-- Sent At -->
                                            <td>
                                                <div class="fs-13 text-dark fw-medium">
                                                    {{ $log->sent_at ? $log->sent_at->format('d M Y') : 'N/A' }}
                                                </div>
                                                <div class="fs-11 text-muted">
                                                    {{ $log->sent_at ? $log->sent_at->format('h:i:s A') : '' }}
                                                </div>
                                            </td>

                                            <!-- Delivery Status -->
                                            <td class="text-center">
                                                @if($log->status === 'sent')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-11 d-inline-flex align-items-center gap-1">
                                                        <span class="material-icons fs-14">check_circle</span>
                                                        {{ translate('Delivered') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fs-11 d-inline-flex align-items-center gap-1" title="{{ $log->response_log }}">
                                                        <span class="material-icons fs-14">error_outline</span>
                                                        {{ translate('Failed') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="py-4">
                                                    <span class="material-icons fs-48 text-muted mb-2 d-block">notifications_paused</span>
                                                    <h6 class="text-dark fw-bold mb-1">{{ translate('No reminder history found') }}</h6>
                                                    <p class="text-muted fs-12 mb-0">{{ translate('When reminders are triggered by the scheduler, their delivery logs will appear here.') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        @if($logs->hasPages())
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
                                <div class="fs-12 text-muted">
                                    {{ translate('Showing') }} {{ $logs->firstItem() }} {{ translate('to') }} {{ $logs->lastItem() }} {{ translate('of') }} {{ $logs->total() }} {{ translate('entries') }}
                                </div>
                                <div>
                                    {!! $logs->links() !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    let ruleIndexCounter = {{ count($providerRules) }};

    function addNewReminderRule() {
        ruleIndexCounter++;
        const idx = ruleIndexCounter;
        const currentTotal = document.querySelectorAll('.rule-card-item').length;
        const ruleNum = currentTotal + 1;
        const ruleId = 'rule_' + Date.now();

        const template = `
            <div class="reminder-stage-card rule-card-item" id="ruleCard_${idx}" data-index="${idx}">
                <input type="hidden" name="rules[${idx}][id]" value="${ruleId}">
                
                <div class="reminder-stage-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                        <span class="badge bg-primary text-white px-3 py-2 rounded-pill fs-12 fw-bold d-flex align-items-center gap-1 rule-number-badge">
                            <span class="material-icons fs-16">schedule</span>
                            <span class="rule-index-label">{{ translate('Rule') }} #${ruleNum}</span>
                        </span>
                        <div class="flex-grow-1" style="max-width: 480px;">
                            <input type="text" class="form-control form-control-sm fw-bold fs-14 border-0 border-bottom rounded-0 px-2 bg-transparent rule-name-input" name="rules[${idx}][name]" value="New Reminder Rule #${ruleNum}" placeholder="{{ translate('Enter a custom name for this reminder...') }}" required>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-12 fw-semibold text-muted">{{ translate('Active') }}:</span>
                            <label class="switcher mb-0">
                                <input class="switcher_input" type="checkbox" name="rules[${idx}][is_active]" value="1" checked>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle delete-rule-btn" onclick="removeRuleCard(this)" title="{{ translate('Delete this reminder rule') }}">
                            <span class="material-icons fs-18 align-middle">delete</span>
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-4">
                            <div class="bg-light p-3 rounded border">
                                <label class="form-label fs-13 fw-bold text-dark mb-2">
                                    <span class="material-icons fs-16 align-middle text-primary me-1">tune</span>
                                    {{ translate('Trigger Condition') }}
                                </label>
                                
                                <div class="mb-3">
                                    <select class="custom-select form-select w-100 trigger-type-select" name="rules[${idx}][trigger_type]" onchange="toggleTriggerType(this)">
                                        <option value="before_hours" selected>⏱️ {{ translate('Hours Before Schedule') }}</option>
                                        <option value="day_morning">🌅 {{ translate('On Service Day at Specific Time') }}</option>
                                    </select>
                                </div>

                                <div class="trigger-hours-box">
                                    <div class="input-group mb-2">
                                        <input type="number" step="0.5" min="0.5" max="168" class="form-control h-45 fw-semibold fs-14" name="rules[${idx}][trigger_value_hours]" value="2">
                                        <span class="input-group-text bg-white text-muted fs-13">{{ translate('Hours Before') }}</span>
                                    </div>
                                    <div class="fs-11 text-muted d-flex align-items-start gap-1">
                                        <span class="material-icons fs-14 text-primary mt-1">info</span>
                                        <span>{{ translate('Triggers automatically when a booking schedule is within these hours remaining.') }}</span>
                                    </div>
                                </div>

                                <div class="trigger-time-box d-none">
                                    <div class="input-group mb-2">
                                        <input type="time" class="form-control h-45 fw-semibold fs-14" name="rules[${idx}][trigger_value_time]" value="07:00">
                                    </div>
                                    <div class="fs-11 text-muted d-flex align-items-start gap-1">
                                        <span class="material-icons fs-14 text-primary mt-1">info</span>
                                        <span>{{ translate('Dispatched on the booking date at this morning time.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label class="form-label fs-13 fw-semibold text-dark">{{ translate('Notification Title') }}</label>
                                <input type="text" class="form-control h-45" name="rules[${idx}][title]" value="Booking Reminder: #{booking_id}" placeholder="{{ translate('Enter notification title...') }}" required>
                            </div>
                            <div>
                                <label class="form-label fs-13 fw-semibold text-dark">{{ translate('Message Body') }}</label>
                                <textarea class="form-control" name="rules[${idx}][message]" rows="3" placeholder="{{ translate('Enter notification message...') }}" required>Hello {provider_name}, you have a booking #{booking_id} scheduled for {service_schedule}.</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const container = document.getElementById('reminderRulesContainer');
        container.insertAdjacentHTML('beforeend', template);

        const newCard = document.getElementById(`ruleCard_${idx}`);
        if (newCard) {
            newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function removeRuleCard(btn) {
        const totalCards = document.querySelectorAll('.rule-card-item').length;
        if (totalCards <= 1) {
            alert('{{ translate("At least one reminder rule must remain configured.") }}');
            return;
        }
        if (confirm('{{ translate("Are you sure you want to remove this reminder rule?") }}')) {
            const card = btn.closest('.rule-card-item');
            card.remove();
            renumberRules();
        }
    }

    function renumberRules() {
        document.querySelectorAll('.rule-card-item').forEach((card, i) => {
            const label = card.querySelector('.rule-index-label');
            if (label) {
                label.innerText = '{{ translate("Rule") }} #' + (i + 1);
            }
        });
    }

    function toggleTriggerType(select) {
        const card = select.closest('.rule-card-item');
        const hoursBox = card.querySelector('.trigger-hours-box');
        const timeBox = card.querySelector('.trigger-time-box');
        if (select.value === 'day_morning') {
            hoursBox.classList.add('d-none');
            timeBox.classList.remove('d-none');
        } else {
            hoursBox.classList.remove('d-none');
            timeBox.classList.add('d-none');
        }
    }

    function insertTag(elem) {
        const text = elem.innerText.replace('content_copy', '').trim();
        navigator.clipboard.writeText(text);
        
        const oldBg = elem.style.backgroundColor;
        const oldColor = elem.style.color;
        elem.style.backgroundColor = '#10b981';
        elem.style.color = '#ffffff';
        setTimeout(() => {
            elem.style.backgroundColor = oldBg;
            elem.style.color = oldColor;
        }, 500);
    }

    function filterByReminderType(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', 'logs');
        if (val) {
            url.searchParams.set('reminder_type', val);
        } else {
            url.searchParams.delete('reminder_type');
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }

    // Auto-switch to Logs tab if pagination, filter or search query is present
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'logs' || urlParams.has('page') || urlParams.has('reminder_type') || urlParams.has('search')) {
            const logsTab = document.querySelector('#logs-tab');
            if (logsTab) {
                const tab = new bootstrap.Tab(logsTab);
                tab.show();
            }
        }
    });
</script>
@endsection
