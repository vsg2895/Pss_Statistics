<?php

namespace App\Services\LiveagentApi;

use App\Contracts\LiveagentApiInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\Http;

class LiveagentService extends BaseService implements LiveagentApiInterface
{
    /**
     * Liveagent api use header to get data for your preferred timezone
     *
     * @var string
     */
    private string $timeZoneOffset = '+28800';//60*60*8

    public function getData(string $endpoint, string $filters = '')
    {
        try {
            $response = Http::withHeaders([
                'apikey' => config('apiKeys.liveagent_api_key'),
                'Timezone-Offset' => $this->timeZoneOffset,
            ])->get(config('apiKeys.liveagent_url') . "/$endpoint" . $filters);

            return $response;
        } catch (\Exception $exception) {
            throw new $exception;
        }
    }

    private function getUrl()
    {
        return config('apiKeys.servit_url');
    }
}
