<?php

namespace App\Http\Controllers\API;

use App\Exceptions\TokenGenerationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\NewTokenRequest;
use App\Services\TokenGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TokenGeneratorController extends Controller
{
    protected TokenGenerationService $tokenGenerationService;

    /**
     * @param  TokenGenerationService  $tokenGenerationService
     */
    public function __construct(TokenGenerationService $tokenGenerationService)
    {
        $this->tokenGenerationService = $tokenGenerationService;
    }

    /**
     * @param  NewTokenRequest  $request
     * @return JsonResponse
     */
    public function generateToken(NewTokenRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $token = $this->tokenGenerationService->newTokenRequest($validated['email']);

            return response()->json($token);
        } catch (TokenGenerationFailedException $e) {
            Log::error(
                "Token generation failed for {email}, {e}",
                ["email" => $validated['email'], 'e' => $e->getMessage()]
            );

            return response()->json(['message' => 'Token generation failed'], 500);
        }
    }
}
