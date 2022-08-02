<?php

namespace App\Http\Middleware;

use App\Services\AgentLogService;
use Closure;
use Illuminate\Http\Request;

class AddAgentLogs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        (new AgentLogService())->store(auth()->user()->servit_id, $request->ip());
        return $next($request);
    }
}
