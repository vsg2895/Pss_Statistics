<?php

namespace App\Services\Insert;

use App\Services\BaseService;

class ChatPricesService extends BaseService
{

    public function getPricesFromDepartment($departmetsWithCompany, $companies, $defaultsWe, $defaultsProviderCompany,
                                            $feeWeCompany, $feeWeProvide, $feeProviderCompany)
    {
        $result = [];
        if (!is_null($departmetsWithCompany)) {
            $company = $companies->where('company_id', $departmetsWithCompany)->first();
            $prices = $this->allTypePricesByCompany($departmetsWithCompany, $company, $defaultsWe, $defaultsProviderCompany, $feeWeCompany, $feeWeProvide, $feeProviderCompany);
            foreach ($prices as $key => $price) {
                $result[$key] = !is_null($price) ? $price['chats_fee'] : null;
            }
        } else {
            $result['we'] = $defaultsWe->pluck('value')->first();
            $result['provider_company'] = null;
        }

        return $result;
    }
}
