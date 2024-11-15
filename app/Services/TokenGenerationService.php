<?php

namespace App\Services;

use App\Exceptions\TokenGenerationFailedException;
use App\Models\Dtos\ApiTokenDto;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TokenGenerationService
{
    private const API_TOKEN_PREFIX = "a_tok_";
    private const API_TTL_MINUTES = 30;

    /**
     * Handle an incoming token generation request
     * @throws TokenGenerationFailedException
     */
    public function newTokenRequest(string $email): ApiTokenDto
    {
        try {
            $authUser = $this->checkAuth($email);

            return $this->generateToken($authUser);
        } catch (ValidationException $e) {
            throw new TokenGenerationFailedException($e);
        }
    }

    /**
     * @param  User  $user
     * @return bool
     */
    private function hasActiveTokens(User $user): bool
    {
        return $user->tokens()->count() > 0;
    }

    /**
     * Generate a new token for the user, deleting any existing tokens
     * @param  User  $user
     * @return ApiTokenDto
     */
    private function generateToken(User $user): ApiTokenDto
    {
        $expiration = now()->addMinutes(self::API_TTL_MINUTES);

        // Check if the user has an existing token
        if ($this->hasActiveTokens($user)) {
            $user->tokens()->delete();
        }

        $tokenValues = $user->createToken(
            sprintf("%s%d", self::API_TOKEN_PREFIX, time()),
            ['*'],
            $expiration
        );

        return new ApiTokenDto(
            [
                'tokenValue' => $tokenValues->plainTextToken,
                'expiration' => $expiration->toDateTimeString(),
            ]
        );
    }

    /**
     * Check if the user is authenticated
     * @throws ValidationException
     */
    private function checkAuth(string $email): User|null
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($user !== null) {
            return $user;
        }

        Log::warning("API Token - Failed login: {email}", ["email" => $email]);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
}
