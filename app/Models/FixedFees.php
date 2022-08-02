<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedFees extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $hidden = ['created_at', 'updated_at'];

//    protected $casts = [
//        'date' => 'datetime:m-d', // Change your format
//    ];

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class)->select('id', 'slug');
    }

    public static function providerToCompanyFixedCustomByDate($providerId, $company_id, $slug, $monthStarts)
    {
        $query = self::where('service_provider_id', $providerId)->where('company_id', $company_id)->with('feeType')
            ->whereHas('feeType', function ($q) use ($slug) {
                !is_array($slug) ? $q->where('slug', '=', $slug) : $q->whereIn('slug', '=', $slug);
            });

        return is_array($monthStarts) ? $query->whereIn("date", $monthStarts) : $query->where("date", $monthStarts);

    }

    public static function providerToCompaniesFixedCustomByDate($providerId, $company_id, $slug, $monthStarts)
    {
        $query = self::where('service_provider_id', $providerId)->whereIn('company_id', $company_id)->with('feeType')
            ->whereHas('feeType', function ($q) use ($slug) {
                !is_array($slug) ? $q->where('slug', '=', $slug) : $q->whereIn('slug', '=', $slug);
            });

        $query = is_array($monthStarts) ? $query->whereIn("date", $monthStarts) : $query->where("date", $monthStarts);

        return $query->groupBy('company_id')->get()->pluck('fee', 'company_id')->toArray();
    }

    public static function providerFixedCustomByDate($providerId, $slug, $monthStarts)
    {
        $query = self::where('service_provider_id', $providerId)->whereNull('company_id')->with('feeType')
            ->whereHas('feeType', function ($q) use ($slug) {
                !is_array($slug) ? $q->where('slug', '=', $slug) : $q->whereIn('slug', '=', $slug);
            });

        return is_array($monthStarts) ? $query->whereIn("date", $monthStarts) : $query->where("date", $monthStarts);

    }

    public static function providersFixedCustomByDate($providerId, $slug, $monthStarts)
    {
        $query = self::whereIn('service_provider_id', $providerId)->whereNull('company_id')->with('feeType')
            ->whereHas('feeType', function ($q) use ($slug) {
                !is_array($slug) ? $q->where('slug', '=', $slug) : $q->whereIn('slug', '=', $slug);
            });

        $query = is_array($monthStarts) ? $query->whereIn("date", $monthStarts) : $query->where("date", $monthStarts);

        return $query->groupBy('service_provider_id')->get()->pluck('fee', 'service_provider_id')->toArray();
    }

    public static function companiesFixedCustomByDate($companyId, $slug, $monthStarts)
    {
        $query = self::whereIn('company_id', $companyId)->whereNull('service_provider_id')->with('feeType')
            ->whereHas('feeType', function ($query) use ($slug) {
                !is_array($slug) ? $query->where('slug', '=', $slug) : $query->whereIn('slug', '=', $slug);
            })->whereIn("date", $monthStarts);

        return $query->groupBy('company_id')->get()->pluck('fee', 'company_id')->toArray();
    }

    public static function companyFixedCustomByDate($companyId, $slug, $monthStarts)
    {
        return self::where('company_id', $companyId)->whereNull('service_provider_id')->with('feeType')
            ->whereHas('feeType', function ($query) use ($slug) {
                !is_array($slug) ? $query->where('slug', '=', $slug) : $query->whereIn('slug', '=', $slug);
            })->whereIn("date", $monthStarts);
    }

}
