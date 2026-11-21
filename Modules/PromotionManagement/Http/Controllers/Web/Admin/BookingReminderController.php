<?php

namespace Modules\PromotionManagement\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Modules\BookingModule\Entities\Booking;
use Modules\BusinessSettingsModule\Entities\BusinessSettings;
use Modules\PromotionManagement\Entities\BookingReminderLog;
use Modules\PromotionManagement\Entities\PushNotification;

class BookingReminderController extends Controller
{
    protected BusinessSettings $businessSettings;

    public function __construct(BusinessSettings $businessSettings)
    {
        $this->businessSettings = $businessSettings;
    }

    /**
     * Default reminder rules (initial starter templates)
     */
    public static function getDefaultProviderRules(): array
    {
        return [
            [
                'id' => 'rule_advance_24h',
                'name' => '24 Hours Advance Reminder',
                'trigger_type' => 'before_hours',
                'trigger_value' => 24,
                'title' => 'Booking Reminder: #{booking_id}',
                'message' => 'Hello {provider_name}, you have an upcoming booking #{booking_id} scheduled for {service_schedule}. Please ensure you are prepared.',
                'is_active' => 1,
            ],
            [
                'id' => 'rule_morning_7am',
                'name' => 'Booking Day Morning Alert',
                'trigger_type' => 'day_morning',
                'trigger_value' => '07:00',
                'title' => "Today's Booking Alert: #{booking_id}",
                'message' => 'Good morning {provider_name}! You have booking #{booking_id} scheduled today at {service_schedule}.',
                'is_active' => 1,
            ],
            [
                'id' => 'rule_final_2h',
                'name' => '2 Hours Urgent Alert',
                'trigger_type' => 'before_hours',
                'trigger_value' => 2,
                'title' => 'Upcoming Service Alert: #{booking_id}',
                'message' => 'Urgent Reminder: Booking #{booking_id} is scheduled in {hours} hours at {service_schedule}.',
                'is_active' => 1,
            ],
        ];
    }

    /**
     * Default reminder settings structure
     */
    public static function getDefaultSettings(): array
    {
        return [
            'is_active' => 1,
            'provider' => [
                'is_active' => 1,
            ],
            'provider_rules' => self::getDefaultProviderRules(),
            'customer' => [
                'is_active' => 1,
                'advance' => [
                    'is_active' => 1,
                    'hours' => 24,
                    'title' => 'Service Reminder: #{booking_id}',
                    'message' => 'Hello {customer_name}, your booking #{booking_id} is scheduled for tomorrow at {service_schedule}.',
                ],
                'morning' => [
                    'is_active' => 1,
                    'time' => '08:00',
                    'title' => 'Your Service Today: #{booking_id}',
                    'message' => 'Good morning {customer_name}! Your booking #{booking_id} is scheduled for today at {service_schedule}.',
                ],
                'final' => [
                    'is_active' => 1,
                    'hours' => 2,
                    'title' => 'Provider Arriving Soon: #{booking_id}',
                    'message' => 'Hello {customer_name}, your service provider is scheduled to arrive at {service_schedule} for booking #{booking_id}.',
                ],
            ],
        ];
    }

    /**
     * Normalize provider rules array (handles dynamic rules + backward compatibility)
     */
    public static function normalizeProviderRules(array $settings): array
    {
        if (!empty($settings['provider_rules']) && is_array($settings['provider_rules'])) {
            return $settings['provider_rules'];
        }

        // Convert legacy 3 rules if available
        $rules = [];
        if (isset($settings['provider']['advance'])) {
            $rules[] = [
                'id' => 'rule_advance_24h',
                'name' => '24 Hours Advance Reminder',
                'trigger_type' => 'before_hours',
                'trigger_value' => $settings['provider']['advance']['hours'] ?? 24,
                'title' => $settings['provider']['advance']['title'] ?? 'Booking Reminder: #{booking_id}',
                'message' => $settings['provider']['advance']['message'] ?? '',
                'is_active' => !empty($settings['provider']['advance']['is_active']) ? 1 : 0,
            ];
        }
        if (isset($settings['provider']['morning'])) {
            $rules[] = [
                'id' => 'rule_morning_7am',
                'name' => 'Booking Day Morning Alert',
                'trigger_type' => 'day_morning',
                'trigger_value' => $settings['provider']['morning']['time'] ?? '07:00',
                'title' => $settings['provider']['morning']['title'] ?? "Today's Booking Alert: #{booking_id}",
                'message' => $settings['provider']['morning']['message'] ?? '',
                'is_active' => !empty($settings['provider']['morning']['is_active']) ? 1 : 0,
            ];
        }
        if (isset($settings['provider']['final'])) {
            $rules[] = [
                'id' => 'rule_final_2h',
                'name' => '2 Hours Urgent Alert',
                'trigger_type' => 'before_hours',
                'trigger_value' => $settings['provider']['final']['hours'] ?? 2,
                'title' => $settings['provider']['final']['title'] ?? 'Upcoming Service Alert: #{booking_id}',
                'message' => $settings['provider']['final']['message'] ?? '',
                'is_active' => !empty($settings['provider']['final']['is_active']) ? 1 : 0,
            ];
        }

        return !empty($rules) ? $rules : self::getDefaultProviderRules();
    }

    /**
     * Display reminder settings & log
     */
    public function index(Request $request): Renderable
    {
        $rawSettings = $this->businessSettings->where('key_name', 'booking_reminder_settings')->first();
        $defaults = self::getDefaultSettings();

        if ($rawSettings && !empty($rawSettings->live_values)) {
            $saved = is_array($rawSettings->live_values) ? $rawSettings->live_values : json_decode($rawSettings->live_values, true);
            $settings = array_replace_recursive($defaults, $saved ?: []);
        } else {
            $settings = $defaults;
        }

        $providerRules = self::normalizeProviderRules($settings);

        // Fetch logs
        $query = BookingReminderLog::with(['booking', 'user'])->latest('sent_at');

        if ($request->filled('target_type')) {
            $query->where('target_type', $request->get('target_type'));
        }
        if ($request->filled('reminder_type')) {
            $query->where('reminder_type', 'like', '%' . $request->get('reminder_type') . '%');
        }
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhere('booking_id', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(pagination_limit())->appends($request->all());

        return view('promotionmanagement::admin.booking-reminder.index', compact('settings', 'providerRules', 'logs'));
    }

    /**
     * Save dynamic reminder settings
     */
    public function update(Request $request): RedirectResponse
    {
        $rawSettings = $this->businessSettings->where('key_name', 'booking_reminder_settings')->first();
        $defaults = self::getDefaultSettings();
        $saved = ($rawSettings && !empty($rawSettings->live_values))
            ? (is_array($rawSettings->live_values) ? $rawSettings->live_values : json_decode($rawSettings->live_values, true))
            : [];
        $existingCustomer = $saved['customer'] ?? $defaults['customer'];

        // Process dynamic rules
        $rawRules = $request->input('rules', []);
        $sanitizedRules = [];
        $idx = 0;

        foreach ($rawRules as $r) {
            $idx++;
            $ruleId = !empty($r['id']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $r['id']) : ('rule_' . uniqid());
            $triggerType = ($r['trigger_type'] ?? 'before_hours') === 'day_morning' ? 'day_morning' : 'before_hours';

            $sanitizedRules[] = [
                'id' => $ruleId,
                'name' => trim($r['name'] ?? ('Reminder Rule ' . $idx)),
                'trigger_type' => $triggerType,
                'trigger_value' => $triggerType === 'day_morning' ? ($r['trigger_value_time'] ?? '07:00') : (float) ($r['trigger_value_hours'] ?? 24),
                'title' => trim($r['title'] ?? 'Booking Reminder: #{booking_id}'),
                'message' => trim($r['message'] ?? ''),
                'is_active' => isset($r['is_active']) && ($r['is_active'] == '1' || $r['is_active'] == 'on') ? 1 : 0,
            ];
        }

        // If no rules were submitted, retain default
        if (empty($sanitizedRules)) {
            $sanitizedRules = self::getDefaultProviderRules();
        }

        $payload = [
            'is_active' => $request->has('is_active') ? 1 : 0,
            'provider' => [
                'is_active' => 1,
            ],
            'provider_rules' => $sanitizedRules,
            'customer' => $existingCustomer,
        ];

        $setting = $this->businessSettings->firstOrNew(['key_name' => 'booking_reminder_settings']);
        $setting->live_values = json_encode($payload);
        $setting->test_values = json_encode($payload);
        $setting->settings_type = 'booking_reminder';
        $setting->mode = 'live';
        $setting->is_active = $payload['is_active'];
        $setting->save();

        Toastr::success(translate('Booking reminder settings updated successfully!'));
        return redirect()->back();
    }

    /**
     * Trigger manual background scheduler run
     */
    public function runSchedulerNow(): RedirectResponse
    {
        Artisan::call('booking:send-reminders');
        $output = Artisan::output();
        Toastr::success(translate('Reminder scheduler executed: ') . strip_tags($output));
        return redirect()->back();
    }
}
