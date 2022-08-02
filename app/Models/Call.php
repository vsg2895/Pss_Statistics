<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Call extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope('open', function (Builder $builder) {
            $builder->where('calid', 'OPEN');
        });
    }

    public function scopeToday($query)
    {
        return $query->whereRaw('DATE(started_at) = ?', request()->start_date ? [request()->start_date] : [date('Y-m-d')]);
    }

    public function scopeLastWeek($query)
    {
        return $query->whereRaw('DATE(started_at) = ?', request()->compare_date ? [request()->compare_date] : [Carbon::now()->subDays(7)->format('Y-m-d')]);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereRaw('DATE(`started_at`) >= ?', [$startDate])->whereRaw('DATE(`started_at`) <= ?', [$endDate]);
    }

    public function scopeMissed($query)
    {
        $missedSeconds = Setting::where('slug', 'missed_call_seconds')->first()->value;
        return $query->whereNull('connected_at')
            ->where(DB::raw( "TIMESTAMPDIFF(second,started_at,hang_up_at)"), '>=', $missedSeconds);
    }

    public function scopeAnswered($query)
    {
        return $query->whereNotNull('connected_at');
    }
}
