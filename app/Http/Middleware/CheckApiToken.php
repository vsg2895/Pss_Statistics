<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckApiToken
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
        if ($request->header('apiKey') && $request->header('apiKey') === config('app.api_token')) {
            return $next($request);
        } else {
            return response()->json([
                'success' => false,
                'error' => "Wrong token"
            ], 403);
        }
    }
}
