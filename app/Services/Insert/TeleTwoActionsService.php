<?php

namespace App\Services\Insert;

use App\Contracts\TeleTwoApiInterface;
use App\Mail\DeletedTeleTwoUsers;
use App\Models\TeleTwoUser;
use App\Services\BaseService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class TeleTwoActionsService extends BaseService
{
    private TeleTwoApiInterface $teleTwoApi;

    public function __construct(TeleTwoApiInterface $teleTwoApi)
    {
        $this->teleTwoApi = $teleTwoApi;
    }

    public function insertAll(): void
    {
        $apiIds = [];
        foreach (range('a', 'z') as $word) {
            $insertData = [];
            $queryParams = "&maxResults=1000&query=$word";
            $middlepart = "/contacts/list/soderbergsbil.se/svarsservice";
            $response = $this->teleTwoApi->getApiData('', $middlepart, $queryParams);
            $users = simplexml_load_string($response->body()) ?: [];
            if ($users) {
                foreach ($users as $user) {
                    $userId = $user->attributes()['id'];
                    $apiIds[] = $userId;
                    $insertData[] = [
                        'id' => $userId,
                        'first_name' => $user->firstname,
                        'last_name' => $user->lastname,
                        'phone_number' => $user->preferredNumber,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                TeleTwoUser::upsert($insertData, ['id']);
            }
        }
        $dbIds = TeleTwoUser::all()->pluck('id')->toArray();
        $diff = array_diff($dbIds, $apiIds);
        if (count($diff)) {
            TeleTwoUser::whereIn('id', $diff)->delete();
//            change email address
            Mail::to('stepanyan281995@gmail.com')->send(new DeletedTeleTwoUsers($diff));
        }
    }

    public function getMore($id)
    {
        $params = "/$id/available";
        $middlepart = config('apiKeys.tele_two_api_middle_part');
        $response = $this->teleTwoApi->getApiData($params, $middlepart, '');

        return $response;
    }
}
