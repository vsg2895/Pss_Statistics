<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DailyStatistic extends Model
{
    protected $guarded = [];

    public function scopeToday($query)
    {
        return $query->where('date', date('Y-m-d'));
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where('date', '>=', $startDate)->where('date', '<=', $endDate);
    }

    public function scopeYesterday($query)
    {
        return $query->where('date', Carbon::yesterday()->format('Y-m-d'));
    }

    public function scopeLastWeek($query)
    {
        return $query->where('date', Carbon::now()->subDays(7)->format('Y-m-d'));
    }

    public function dailyStatisticMeta()
    {
        return $this->hasOne(DailyStatisticMeta::class);
    }

    public function agent()
    {
        return $this->belongsTo(ImportedUser::class, 'servit_user_id', 'servit_id');
    }
}
