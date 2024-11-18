<?php

namespace tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @return void
     */
    public function testFillable(): void
    {
        $this->assertEquals([
            'name',
            'email',
            'password',
        ], (new User())->getFillable());
    }

    /**
     * @return void
     */
    public function testVerifyProps(): void
    {
        $user = new User();
        $this->assertNull($user->id);
        $this->assertNull($user->name);
        $this->assertNull($user->email);
        $this->assertNull($user->password);
        $this->assertNull($user->created_at);
        $this->assertNull($user->updated_at);
    }
}
