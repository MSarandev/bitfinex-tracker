<?php

namespace tests\Unit\Models;

use App\Models\PercentDelta;
use Tests\TestCase;

class PercentDeltaTest extends TestCase
{
    /**
     * @return void
     */
    public function testFillable(): void
    {
        $expected = [
            'id',
            'user_id',
            'timeframe_flag',
            'timeframe_value',
            'percent_change',
            'active',
            'symbol'
        ];

        $this->assertEquals($expected, (new PercentDelta())->getFillable());
    }

    /**
     * @return void
     */
    public function testTableVerification(): void
    {
        $expected = 'percent_deltas';

        $this->assertEquals($expected, (new PercentDelta())->getTable());
    }

    /**
     * @return void
     */
    public function testVerifyProps(): void
    {
        $percentDelta = new PercentDelta();
        $this->assertNull($percentDelta->id);
        $this->assertNull($percentDelta->user_id);
        $this->assertNull($percentDelta->timeframe_flag);
        $this->assertNull($percentDelta->timeframe_value);
        $this->assertNull($percentDelta->percent_change);
        $this->assertNull($percentDelta->active);
        $this->assertNull($percentDelta->symbol);
    }
}
