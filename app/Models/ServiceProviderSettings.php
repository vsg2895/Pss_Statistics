<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceProviderSettings extends Model
{
    protected $table = 'service_provider_settings';

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'slug', 'slug');
    }

    //cost to charge companies from Service Provider Side
    public static function getCompanyDefaults($providerId)
    {
        return ServiceProviderSettings::where('service_provider_id', $providerId)
            ->whereIn('slug', FeeType::FEE_TYPES)->select('id', 'name', 'slug', 'value')
            ->with('feeType')->orderBy('id')->get();
    }

    public static function getProviderDefaultBySlug($providerId, $slug)
    {
        return self::where('slug', $slug)->where('service_provider_id', $providerId)->pluck('value')->first();
    }

    //cost to charge companies from Service Provider Side
    public static function getProviderToCompanyDefaults($insertType)
    {
        return ServiceProviderSettings::whereIn('slug', $insertType)->select('id', 'service_provider_id', 'name', 'slug', 'value')
            ->with('feeType')->orderBy('id')->get();
    }

    public static function storeDefaultSettings($providerId)
    {
        $data = [];
        $feeTypes = FeeType::FEE_TYPES;
        $feeTypeNames = FeeType::FEE_TYPES_NAMES;
        $feeTypeValues = FeeType::FEE_TYPES_VALUES;
        $feeTypeDescs = FeeType::FEE_TYPES_DESCRIPTIONS;

        foreach ($feeTypes as $feeType) {
            $data[] = [
                'service_provider_id' => $providerId,
                'name' => $feeTypeNames[$feeType],
                'slug' => $feeType,
                'value' => $feeTypeValues[$feeType],
                'description' => $feeTypeDescs[$feeType],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('service_provider_settings')->insert($data);
    }
}
