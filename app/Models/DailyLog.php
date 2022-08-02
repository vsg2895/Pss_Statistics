<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    public $timestamps = false;

    public function scopeToday($query)
    {
        return $query->where('date', request()->start_date ?? date('Y-m-d'));
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
