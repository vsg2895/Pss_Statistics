<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'slug', 'slug');
    }

    public static function getSettings()
    {
        return Setting::pluck('value', 'slug')->toArray();
    }

    //cost to charge providers from PSS side
    public static function getProviderDefaults()
    {
        return Setting::whereIn('slug', FeeType::FEE_TYPES)->select('id', 'name', 'slug', 'value')
            ->with(['feeType' => function ($q) {
                $q->select('id', 'slug');
            }])->orderBy('id')->get();
    }
    //get default fees according billing row
    public static function getDefaultsBillingRow($insertType)
    {
        return Setting::whereIn('slug', $insertType)->select('id', 'name', 'slug', 'value')
            ->with(['feeType' => function ($q) {
                $q->select('id', 'slug');
            }])->orderBy('id')->get();
    }

    //get setting by slug
    public static function getValueBySlug($slug)
    {
        return (float)Setting::where('slug', $slug)->pluck('value')->first();

    }

}
