<?php

namespace Tests\Unit\Models\Dto;

use App\Models\Dtos\ApiTokenDto;
use Tests\TestCase;

class ApiTokenDtoTest extends TestCase
{
    /**
     * @return void
     */
    public function testFillable(): void
    {
        $expected = [
            'tokenValue',
            'expiration',
        ];

        $this->assertEquals($expected, (new ApiTokenDto())->getFillable());
    }

    /**
     * @return void
     */
    public function testVerifyProps(): void
    {
        $apiTokenDto = new ApiTokenDto();
        $this->assertNull($apiTokenDto->id);
        $this->assertNull($apiTokenDto->tokenValue);
        $this->assertNull($apiTokenDto->expiration);
    }
}
