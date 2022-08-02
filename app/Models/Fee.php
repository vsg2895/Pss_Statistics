<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fee extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class)->select('id', 'slug');
    }

    // delete custom values by company
    public static function DeleteCompanyCustom($companyId)
    {
        return self::where('company_id', $companyId)->delete();
    }

    //PSS charge from company
    public static function getCompanyCustom($companyId)
    {
        return self::where('company_id', $companyId)->where('service_provider_id', null)
            ->with('feeType')->get()->pluck('fee', 'feeType.slug');
    }

    //PSS charge from provider
    public static function getProviderCustom($providerId)
    {
        return self::where('company_id', null)->where('service_provider_id', $providerId)
            ->with('feeType')->get()->pluck('fee', 'feeType.slug');
    }

    public static function feeByFeeType($chatFeeId, $company)
    {
        return self::where(['fee_type_id' => $chatFeeId, 'company_id' => $company->id])
            ->whereNull('service_provider_id')->pluck('fee')->first();
    }

    public static function feeByFeeTypeCompanyProvider($chatFeeId, $provider)
    {
        return self::where(['fee_type_id' => $chatFeeId, 'service_provider_id' => $provider->id])
            ->whereNull('company_id')->pluck('fee')->first();

    }

    public static function feeByFeeTypeProvider($chatFeeId, $provider, $company)
    {
        return self::where(['fee_type_id' => $chatFeeId, 'company_id' => $company->id, 'service_provider_id' => $provider->id])
            ->pluck('fee')->first();
    }


    //provider charge from company
    public static function getProviderCompanyCustom($companyId, $providerId)
    {
        return self::where('company_id', $companyId)->where('service_provider_id', $providerId)
            ->with('feeType')->get()->pluck('fee', 'feeType.slug');
    }

    public static function getProviderCompaniesCustom($companyId, $providerId)
    {
        return self::whereIn('company_id', $companyId)->where('service_provider_id', $providerId)
            ->with('feeType', function ($q) {
                return $q->where('slug', 'monthly_fee');
            })->get()->pluck('fee', 'company_id')->toArray();
    }

    //provider charge from all company
    public static function getProviderCompanyCustomAll()
    {
        return self::whereNotNull('company_id')->whereNotNull('service_provider_id')->with('feeType')->get();
    }

    // charge from all provider
    public static function getProviderCustomAll()
    {
        return self::whereNull('company_id')->whereNotNull('service_provider_id')->with('feeType')->get();
    }

    // charge from all provider
    public static function getCompanyCustomAll()
    {
        return self::whereNotNull('company_id')->whereNull('service_provider_id')->with('feeType')->get();
    }
}
