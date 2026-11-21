<?php

namespace Modules\PromotionManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\BookingModule\Entities\Booking;
use Modules\UserManagement\Entities\User;

class BookingReminderLog extends Model
{
    protected $table = 'booking_reminders_log';

    protected $fillable = [
        'booking_id',
        'target_type',
        'reminder_type',
        'user_id',
        'fcm_token',
        'title',
        'body',
        'sent_at',
        'status',
        'response_log',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
