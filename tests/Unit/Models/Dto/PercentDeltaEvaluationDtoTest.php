<?php

namespace Tests\Unit\Models\Dto;

use App\Models\Dtos\PercentDeltaEvaluationDto;
use Tests\TestCase;

class PercentDeltaEvaluationDtoTest extends TestCase
{
    /**
     * @return void
     */
    public function testFillable(): void
    {
        $expected = [
            'timeframe_flag',
            'timeframe_value',
        ];

        $this->assertEquals($expected, (new PercentDeltaEvaluationDto())->getFillable());
    }

    /**
     * @return void
     */
    public function testVerifyProps(): void
    {
        $apiTokenDto = new PercentDeltaEvaluationDto();
        $this->assertNull($apiTokenDto->timeframe_flag);
        $this->assertNull($apiTokenDto->timeframe_value);
    }
}
