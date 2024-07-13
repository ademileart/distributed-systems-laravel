<?php

namespace App\Http\Middleware;

use App\Services\AuthenticationMicroServiceConnection;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class UserAuthenticationInAuthenticationMicroService
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws ConnectionException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = AuthenticationMicroServiceConnection::getInstance()->validateUser($request->bearerToken(), $request->ip());

        if ($response->status() != 200)
            abort(400, $response->body());

        return $next($request);
    }
}
