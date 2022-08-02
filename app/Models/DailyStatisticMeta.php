<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DailyStatisticMeta extends Model
{
    protected $table = 'daily_statistic_meta';

    protected $guarded = [];

    public function dailyStatistic()
    {
        return $this->belongsTo(DailyStatistic::class);
    }
}
