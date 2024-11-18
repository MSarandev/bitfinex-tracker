<?php

namespace Tests\Feature\Services;

use App\Exceptions\TokenGenerationFailedException;
use App\Models\User;
use App\Services\TokenGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TokenGenerationService $service;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->service = new TokenGenerationService();
    }

    /**
     * @return void
     * @throws TokenGenerationFailedException
     */
    public function testNewTokenRequest(): void
    {
        $email = 'custom_test@test.com';
        $user = User::factory()->createOne(['email' => $email]);

        $this->assertEquals($email, $user->email);

        $token = $this->service->newTokenRequest($email);

        $this->assertNotNull($token);
        $this->assertTrue($token->expiration > now());
        $this->assertNotNull($token->tokenValue);
    }

    /**
     * @return void
     * @throws TokenGenerationFailedException
     */
    public function testNewTokenWithFaultyInfo(): void
    {
        $faultyEmail = "no@mail.com";

        $this->expectException(TokenGenerationFailedException::class);
        $this->service->newTokenRequest($faultyEmail);
    }

    /**
     * @return void
     * @throws TokenGenerationFailedException
     */
    public function testNewTokenWithOldTokens(): void
    {
        $email = 'custom_test@test1.com';
        $user = User::factory()->createOne(['email' => $email]);

        $this->assertEquals($email, $user->email);

        $token = $this->service->newTokenRequest($email);

        $this->assertNotNull($token);
        $this->assertTrue($token->expiration > now());
        $this->assertNotNull($token->tokenValue);

        // required to produce a difference in the expirations
        sleep(2);

        $token2 = $this->service->newTokenRequest($email);

        $this->assertNotNull($token2);
        $this->assertTrue($token2->expiration > now());
        $this->assertNotNull($token2->tokenValue);
        $this->assertNotEquals($token->tokenValue, $token2->tokenValue);
        $this->assertNotEquals($token->expiration, $token2->expiration);
    }
}
