<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\NewTokenRequest;
use App\Models\User;
use DateInterval;
use DateMalformedIntervalStringException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TokenGeneratorController extends Controller
{
    private const API_TOKEN_PREFIX = "a_tok_";
    private const API_TTL_MINUTES = 30;

    /**
     * @param  NewTokenRequest  $request
     * @return JsonResponse
     */
    public function generateToken(NewTokenRequest $request): JsonResponse
    {
        try {
            $authUser = $this->checkAuth($request->email);

            $expiration = now()->add(new DateInterval(sprintf("PT%dM", self::API_TTL_MINUTES)));

            $token = $authUser->createToken(
                sprintf("%s%d", self::API_TOKEN_PREFIX, time()),
                ['*'], // TODO: restrict here
                $expiration
            );

            return response()->json(['token' => $token->plainTextToken]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (DateMalformedIntervalStringException $e) {
            Log::error("Failed to generate token: {error}", ["error" => $e->getMessage()]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * @throws ValidationException
     */
    private function checkAuth(string $email): User|null
    {
        $user = User::where('email', $email)->first();

        if ($user !== null) {
            return $user;
        }

        Log::warning("API Token - Failed login: {email}", ["email" => $email]);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
}
