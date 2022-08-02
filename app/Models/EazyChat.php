<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EazyChat extends Model
{
    protected $guarded = [];

    public function scopeToday($query)
    {
        return $query->whereRaw('DATE(date) = ?', [date('Y-m-d')]);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereRaw('DATE(date) >= ?', [$startDate])->whereRaw('DATE(date) <= ?', [$endDate]);
    }
}
