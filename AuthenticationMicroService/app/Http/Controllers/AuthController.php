<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Services\RedisService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        User::query()->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json('User created, proceed to login!', 201);
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::query()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            abort(400, !$user ? 'User not valid!' : 'Password mismatch');
        }

        DB::beginTransaction();
        try {
            $token = PersonalAccessToken::generateToken($user->id);
            RedisService::getInstance()->putInCache($token, $request->ip(), 55 * 60);
            DB::commit();
        } catch
        (\Throwable $exception) {
            abort(400, $exception->getMessage());
        }
        return response()->json(['token' => $token]);
    }

    public function refreshToken(Request $request)
    {
        if (!$request->bearerToken()) {
            abort(400, 'No token provided! You cannot refresh the token');
        }

        $activeToken = PersonalAccessToken::query()
            ->where('token', hash('sha256', $request->bearerToken()))
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('expires_at', 'desc')
            ->first();

        if ($activeToken) {
            $activeToken->update([
                'expires_at' => Carbon::now()->addHour()
            ]);
            $token = $request->bearerToken();
            RedisService::getInstance()->deleteFromCache($token);
            RedisService::getInstance()->putInCache($token, $request->ip(), 55 * 60);
            return response()->json(['token' => $token, 'active_until:' => $activeToken->expires_at]);
        } else {
            abort(400, 'Token is not valid');
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        try {
            PersonalAccessToken::query()->where('token', hash('sha256', $token))->delete();
        } catch
        (\Throwable $exception) {
            abort(400, $exception->getMessage());
        }


        return response()->json(['message' => 'Logged out successfully']);
    }

    public function validateUserByToken(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'user_ip' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $token = $request->bearerToken();

        $storedIp = RedisService::getInstance()->getFromCache($token);
        $currentIp = $request->user_ip;

        if ($storedIp != $currentIp) {
            return response()->json(['message' => 'IP address mismatch'], 401);
        }

        if (!PersonalAccessToken::isValidToken($token)) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $accessToken = PersonalAccessToken::query()->where('token', hash('sha256', $token))->first();
        $user = User::query()->where('id', $accessToken->user_id)->exists();

        if ($user)
            return response()->json('User is valid', 200);
        return response()->json('User is invalid', 404);
    }

    public function getUserByToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (!PersonalAccessToken::isValidToken($token)) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $accessToken = PersonalAccessToken::query()->where('token', hash('sha256', $token))->first();
        $user = User::query()->where('id', $accessToken->user_id)->firstOrFail();

        return response()->json($user, 200);
    }


}
