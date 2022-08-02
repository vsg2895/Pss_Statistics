<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebhookCheckKey
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
        $token = "zT7JYfRsF1J4RWsJCXiuCMjTQIktdlIgDuoFmxP9kNOcyvp6exAWc0e4Mo33";

        if ($request->header('token') && $request->header('token') == $token) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => "Wrong token",
        ], 403);
    }
}
