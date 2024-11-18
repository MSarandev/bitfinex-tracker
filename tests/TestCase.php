<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public function getToken(User $user): string
    {
        $tokenRequest = $this->postJson(
            '/api/v1/token',
            ['email' => $user->email, 'password' => 'test']
        )->assertStatus(200);

        return json_decode($tokenRequest->content(), true)['tokenValue'];
    }
}
