<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PersonalAccessToken extends Model
{
    protected $fillable = [
        'user_id', 'token', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public static function generateToken($userId): string
    {
        $plainToken = Str::random(60);
        $hashedToken = hash('sha256', $plainToken);
        $expiresAt = Carbon::now()->addHour();

        self::query()->create([
            'user_id' => $userId,
            'token' => $hashedToken,
            'expires_at' => $expiresAt,
        ]);
        return $plainToken;
    }

    public static function isValidToken($token): bool
    {
        return self::query()->where('token', hash('sha256', $token))
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }
}
