<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->apiKey && $request->apiKey === config('app.api_key')) {
            return $next($request);
        } else {
            return redirect()->route('home');
        }
    }
}
