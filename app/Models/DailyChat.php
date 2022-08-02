<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DailyChat extends Model
{
    protected $guarded = [];

    //---------------- - - relationships - - ---------------
    public function user()
    {
        return $this->belongsTo(ImportedUser::class, 'user_id');
    }

    //---------------- - - scopes - - ---------------
    public function scopeToday($query)
    {
        return $query->where('date', date('Y-m-d'));
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where('date', '>=', $startDate)->where('date', '<=', $endDate);
    }

    public function scopeLastWeek($query)
    {
        return $query->where('date', Carbon::now()->subDays(7)->format('Y-m-d'));
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
}
