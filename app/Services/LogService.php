<?php


namespace App\Services;

use App\Contracts\LogInterface;
use Illuminate\Support\Facades\Log;

class LogService extends BaseService implements LogInterface
{
    public function actionArrayInfo(string $key, array $data)
    {
        Log::info($key, $data);
    }

    public function actionStringInfo(string $key, string $data)
    {
        Log::info($key, $data);
    }


}
