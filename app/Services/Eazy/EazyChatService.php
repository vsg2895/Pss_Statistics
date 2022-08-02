<?php

namespace App\Services\Eazy;

use App\Models\EazyChat;
use App\Models\ImportedUser;
use App\Services\BaseService;

class EazyChatService extends BaseService
{
    public function saveChats($data)
    {
        try {
            $agentId = $this->getAgentId($data['agent']);
            $time = $this->getFormattedTime($data['time']);
            EazyChat::create([
                'imported_user_id' => $agentId,
                'conversation_id' => $data['conversationId'],
                'agent_email' => $data['agent'],
                'company_id' => $data['companyId'],
                'date' => $time,
            ]);
        } catch (\Exception $exception) {
            throw new $exception;
        }
    }

    private function getAgentId($agentEmail)
    {
        $agentIds = ImportedUser::pluck('id', 'email')->toArray();
        $agentId = 0;
        if (in_array($agentEmail, array_keys($agentIds))) {
            $agentId = $agentIds[$agentEmail];
        }

        return $agentId;
    }

    private function getFormattedTime($theirTime)
    {
        //2022-05-04T08:17:44.151265Z
        $usIt = explode('T', $theirTime);

        $date = $usIt[0];
        $time = explode('.', $usIt[1])[0];

        return $date . ' ' . $time;
    }
}
