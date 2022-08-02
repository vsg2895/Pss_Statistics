<?php

namespace App\Services;

use App\Models\AgentLog;

class AgentLogService extends BaseService
{
    public function store($agentId, $ip)
    {
        AgentLog::create([
            'agent_id' => $agentId,
            'ip' => $ip,
            'route_name' => request()->route()->getName()
        ]);
    }
}
