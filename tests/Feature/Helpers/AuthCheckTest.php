<?php

namespace Tests\Feature\Helpers;

use App\Helpers\AuthCheck;
use Illuminate\Validation\UnauthorizedException;
use Tests\TestCase;

class AuthCheckTest extends TestCase
{
    public function testCheckUserNoAuth(): void
    {
        $this->expectException(UnauthorizedException::class);
        AuthCheck::checkUser();
    }
}
