<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeType extends Model
{

    protected $guarded = [];

    public const FEE_TYPES = [
        'calls_fee' => 'calls_fee',
        'chats_fee' => 'chats_fee',
        'bookings_fee' => 'bookings_fee',
        'above_60_fee' => 'above_60_fee',
        'monthly_fee' => 'monthly_fee',
        'free_calls' => 'free_calls',
        'cold_transferred_calls_fee' => 'cold_transferred_calls_fee',
        'warm_transferred_calls_fee' => 'warm_transferred_calls_fee',
        'time_above_seconds' => 'time_above_seconds',
        'messages_fee' => 'messages_fee',
        'sms_fee' => 'sms_fee',
        'emails_fee' => 'emails_fee',
    ];

    public const FEE_TYPES_UPDATE_DATA_FEES = [
        'calls_fee' => 18,
        'chats_fee' => 30,
        'bookings_fee' => 25,
        'above_60_fee' => 0,
        'free_calls' => 0,
        'cold_transferred_calls_fee' => 3.99,
        'warm_transferred_calls_fee' => 4.99,
        'time_above_seconds' => 500,
        'messages_fee' => 0,
        'sms_fee' => 0,
        'emails_fee' => 0,
    ];

    public const FEE_TYPES_UPDATE_DATA_EXCEL_FEES = [
        'calls_fee' => 18,
        'bookings_fee' => 30,
        'above_60_fee' => 25,
        'cold_transferred_calls_fee' => 0,
        'warm_transferred_calls_fee' => 0,
        'time_above_seconds' => 3.99,
        'messages_fee' => 4.99,
        'sms_fee' => 500,
        'emails_fee' => 0,
        'free_call' => 0,
        'p_calls_fee' => 0,
        'p_bookings_fee' => 0,
        'p_above_60_fee' => 0,
        'p_cold_transferred_calls_fee' => 0,
        'p_warm_transferred_calls_fee' => 0,
        'p_time_above_seconds' => 0,
        'p_messages_fee' => 0,
        'p_sms_fee' => 0,
        'p_emails_fee' => 0,
        'p_free_call' => 0,
    ];

    public const FEE_TYPES_NAMES = [
        'calls_fee' => 'Calls Fee',
        'chats_fee' => 'Chats Fee',
        'bookings_fee' => 'Bookings Fee',
        'above_60_fee' => 'Time Above Fee',
        'monthly_fee' => 'Monthly Fee',
        'free_calls' => 'Free Calls',
        'cold_transferred_calls_fee' => 'Cold Transferred Calls Fee',
        'warm_transferred_calls_fee' => 'Warm Transferred Calls Fee',
        'time_above_seconds' => 'Time Above Seconds',
        'messages_fee' => 'Messages Fee',
        'sms_fee' => 'Sms Fee',
        'emails_fee' => 'Emails Fee',
    ];

    public const FEE_TYPES_VALUES = [
        'calls_fee' => 18,
        'chats_fee' => 30,
        'bookings_fee' => 25,
        'above_60_fee' => 0,
        'monthly_fee' => 250,
        'free_calls' => 0,
        'cold_transferred_calls_fee' => 3.99,
        'warm_transferred_calls_fee' => 4.99,
        'time_above_seconds' => 500,
        'messages_fee' => 0,
        'sms_fee' => 0,
        'emails_fee' => 0,
    ];

    public const FEE_TYPES_DESCRIPTIONS = [
        'calls_fee' => 'Default fee for calls',
        'chats_fee' => 'Default fee for chats',
        'bookings_fee' => 'Default fee for bookings',
        'above_60_fee' => "Default fee for calls with duration more, than the value set for the 'Time Above Seconds'",
        'monthly_fee' => 'Default monthly fee',
        'free_calls' => 'Default free calls count',
        'cold_transferred_calls_fee' => 'Cold Transferred Calls Fee',
        'warm_transferred_calls_fee' => 'Warm Transferred Calls Fee',
        'time_above_seconds' => 'Calls with more duration than this value, will be charged more money for each second',
        'messages_fee' => 'Default Messages Fee',
        'sms_fee' => 'Default Sms Fee',
        'emails_fee' => 'Default Emails Fee',
    ];

    public const FEE_TYPES_IN_INSERT = [
        'call' => 'calls_fee',
        'free' => 'free_calls',
        'above_second' => 'time_above_seconds',
        'above_fee' => 'above_60_fee',
        'message' => 'messages_fee',
        'sms' => 'sms_fee',
        'email' => 'emails_fee',
        'booking' => 'bookings_fee',
        'cold_transfer' => 'cold_transferred_calls_fee',
        'warm_transfer' => 'warm_transferred_calls_fee',
    ];

    public const YOUTUBE_REPLACED_EMBED_URL = [
        'www.youtube.com/' => 'www.youtube.com/embed/',
        'watch?v=' => '',
    ];
    public const FEE_TYPES_IN_SUM_PRICES = [
        'message' => 'messages_fee',
        'sms' => 'sms_fee',
        'email' => 'emails_fee',
        'booking' => 'bookings_fee'
    ];

    public const FEE_TYPES_IN_STATUS_UPDATE_BILLING = [
        'calls_fee' => 'AN',
        'cold_transferred_calls_fee' => 'CT',
        'warm_transferred_calls_fee' => 'WT',
    ];
    public const P_FEE_TYPES_IN_STATUS_UPDATE_BILLING = [
        'calls_fee' => 'AN',
        'cold_transferred_calls_fee' => 'CT',
        'warm_transferred_calls_fee' => 'WT',
        'p_calls_fee' => 'AN',
        'p_cold_transferred_calls_fee' => 'CT',
        'p_warm_transferred_calls_fee' => 'WT',
    ];

    public const FEE_TYPES_NAMES_BILLING_DATA_FEES = [
        'calls_fee' => 'Calls Fee',
        'bookings_fee' => 'Bookings Fee',
        'above_60_fee' => 'Time Above Fee',
        'cold_transferred_calls_fee' => 'Cold Transferred Calls Fee',
        'warm_transferred_calls_fee' => 'Warm Transferred Calls Fee',
        'time_above_seconds' => 'Time Above Seconds',
        'messages_fee' => 'Messages Fee',
        'sms_fee' => 'Sms Fee',
        'emails_fee' => 'Emails Fee',
    ];

    public const CORRESPONDE_NAME_TOTAL_TO_CDRSTATISTICS = [
        'missed' => 'missed_cals.count',
        'calls' => 'calls_fee.count',
        'bookings' => 'bookings_fee.count',
        'timeAbove' => 'time_above_income.count',
        'timeAboveMoney' => 'time_above_income.fee.price',
        'chats' => 'chats_fee.count',
        'warm_transferred' => 'warm_transferred_calls_fee.count',
        'cold_transferred' => 'cold_transferred_calls_fee.count'

    ];
    public const CORRESPONDE_TABLE_NAME_TO_HISTORICAL_FEES = [
        'Calls Fee' => 'Answered Calls',
        'Chats Fee' => 'Chats',
        'Bookings Fee' => 'Bookings',
        'Time Above Fee' => 'Time Above Income',
        'Monthly Fee' => 'Monthly Income',
        'Free Calls' => 'Answered Calls(Complete Month)',
        'Cold Transferred Calls Fee' => 'Cold Transfers',
        'Warm Transferred Calls Fee' => 'Warm Transfers',
        'Time Above Seconds' => 'Time Above Income',
        'Messages Fee' => 'Messages',
        'Sms Fee' => 'Sms',
        'Emails Fee' => 'Emails',

    ];
    public const PROVIDER_CORRESPONDE_NAME_TOTAL_TO_CDRSTATISTICS = [
        'missed' => 'missed_cals.count',
        'calls' => 'calls_fee.count',
        'bookings' => 'bookings_fee.count',
        'timeAbove' => 'time_above_income.count',
        'timeAboveMoney' => 'time_above_income.fee.p_price',
        'chats' => 'chats_fee.count',
        'warm_transferred' => 'warm_transferred_calls_fee.count',
        'cold_transferred' => 'cold_transferred_calls_fee.count'

    ];
    public const PLANNING_TOTAL_FIELDS = [
        'calls' => 0,
        'missedCalls' => 0,
        'bookings' => 0,
        'chats' => 0,
    ];
    public const PLANNING_AVERAGE_FIELDS = [
        'avg_waiting_time' => 0,
        'avg_progress' => 0,
        'progress' => 0,
    ];
    public const PLANNING_AVERAGE_DECIMAL_FIELDS = [
        'agentsCount' => 0,
        'difference_percentage' => 0,
        'teoryEmpCount' => 0,
    ];
    public const GUARD_TYPE_CORRESPOND = [
        'web' => 'Admins',
        'employee' => 'Agents',
    ];

    public static function getFee($slug)
    {
        return self::where('slug', $slug)->pluck('id')->first();
    }

    public static function getFees($ids)
    {
        return self::whereIn('id', $ids)->pluck('slug');
    }

    public static function getFeesBySlug($ids, $slug)
    {
        return self::whereIn('id', $ids)->where('slug', $slug)->pluck('id', 'slug')->first();
    }

}
