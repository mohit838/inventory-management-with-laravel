<?php

namespace App\Services;

use App\Models\RefreshToken;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;

class JwtService
{
    protected string $secret;
    protected int $ttl;

    public function __construct()
    {
        $this->secret = config('app.key') ?? env('APP_KEY') ?? 'secret';
        $this->ttl = 3600; // 1 hour default
    }

    public function generateAccessToken(int $userId, array $claims = [], int $ttlSeconds = 900): string
    {
        $now = time();
        $payload = array_merge([
            'iss' => url('/'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttlSeconds,
            'sub' => $userId,
        ], $claims);

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function createRefreshToken(int $userId, ?int $ttlDays = 30): RefreshToken
    {
        $plain = Str::random(64);
        $expiresAt = Carbon::now()->addDays($ttlDays ?? 30);

        $rt = RefreshToken::create([
            'user_id' => $userId,
            'token' => hash('sha256', $plain),
            'expires_at' => $expiresAt,
        ]);

        // return model with plain token attached for client
        $rt->plain_token = $plain;

        return $rt;
    }

    public function validateAccessToken(string $token)
    {
        try {
            return JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function revokeRefreshTokenByPlain(string $plain)
    {
        $hash = hash('sha256', $plain);
        $rt = RefreshToken::where('token', $hash)->first();
        if ($rt) {
            $rt->revoked = true;
            $rt->save();
        }
    }
}
