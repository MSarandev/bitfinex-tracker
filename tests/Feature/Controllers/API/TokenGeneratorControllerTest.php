<?php

namespace Tests\Feature\Controllers\API;

use App\Models\User;
use Tests\TestCase;

class TokenGeneratorControllerTest extends TestCase
{
    protected User $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->createOne([
            'email' => 'test@test.com',
            'password' => 'password',
        ]);
    }

    /**
     * @return void
     */
    public function testGenerateToken(): void
    {
        $response = $this->postJson(
            '/api/v1/token',
            [
                'email' => $this->user->email,
                'password' => 'password',
            ]
        );

        $response->assertStatus(200);
        $this->assertNotNull(json_decode($response->content())->tokenValue);
        $this->assertNotNull(json_decode($response->content())->expiration);
    }

    /**
     * @return void
     */
    public function testGenerateTokenFaulty(): void
    {
        $response = $this->postJson(
            '/api/v1/token',
            [
                'email' => 'someFaulty@mail.com',
                'password' => 'fault',
            ]
        );

        $response->assertStatus(500);
    }
}
