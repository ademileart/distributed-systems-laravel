<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AuthenticationMicroServiceConnection
{
    private static self|null $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null)
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * @throws ConnectionException
     */
    public function getUser(string $bearerToken)
    {
        $url = 'auth_service:80/auth/api/get-user';
        $response = Http::withToken($bearerToken)->get($url);
        return json_decode($response);
    }

    public function validateUser(string $bearerToken, string $userIp)
    {
        $url = 'auth_service:80/auth/api/validate-user';
        return Http::withToken($bearerToken)->get($url, ['user_ip' => $userIp]);

    }

}
