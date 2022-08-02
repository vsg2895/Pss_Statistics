<?php

namespace App\Services\Insert;

use App\Contracts\TeleTwoApiInterface;
use Illuminate\Support\Facades\Http;

class TeleTwoApiService implements TeleTwoApiInterface
{
    public function getApiData($params = '', $middlePart = '', $queryParams = '')
    {
        $baseUrl = config('apiKeys.tele_two_api');
        $baseToken = config('apiKeys.tele_two_api_token');
        $url = $baseUrl . $middlePart . $params . '?' . $baseToken . $queryParams;

        return Http::get($url);
    }

}
