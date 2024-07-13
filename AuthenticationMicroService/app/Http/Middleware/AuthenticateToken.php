<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Services\RedisService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthenticateToken
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('login') || $request->is('register')) {
            return $next($request);
        }

        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        if (!PersonalAccessToken::isValidToken($token)) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $storedIp = RedisService::getInstance()->getFromCache($token);
        $currentIp = $request->ip();

        if ($storedIp != $currentIp) {
            return response()->json(['message' => 'IP address mismatch'], 401);
        }

        return $next($request);
    }
}
