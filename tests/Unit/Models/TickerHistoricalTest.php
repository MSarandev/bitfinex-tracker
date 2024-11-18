<?php

namespace tests\Unit\Models;

use App\Models\TickerHistorical;
use Tests\TestCase;

class TickerHistoricalTest extends TestCase
{
    /**
     * @return void
     */
    public function testFillable(): void
    {
        $expected = ['bid', 'ask', 'mts'];

        $this->assertEquals($expected, (new TickerHistorical())->getFillable());
    }

    /**
     * @return void
     */
    public function testApiMarshaller(): void
    {
        $expected = [
            1 => 'bid',
            3 => 'ask',
            12 => 'mts',
        ];

        $this->assertEquals($expected, TickerHistorical::$apiMarshaller);
    }

    /**
     * @return void
     */
    public function testApiUnMarshaller(): void
    {
        $expected = [
            'bid' => 1,
            'ask' => 3,
            'mts' => 12,
        ];

        $this->assertEquals($expected, TickerHistorical::$apiUnMarshaller);
    }

    /**
     * @return void
     */
    public function testIdFromKey(): void
    {
        $this->assertEquals(1, TickerHistorical::idFromKey('bid'));
        $this->assertEquals(3, TickerHistorical::idFromKey('ask'));
        $this->assertEquals(12, TickerHistorical::idFromKey('mts'));
    }

    /**
     * @return void
     */
    public function testKeyFromId(): void
    {
        $this->assertEquals('bid', TickerHistorical::keyFromId(1));
        $this->assertEquals('ask', TickerHistorical::keyFromId(3));
        $this->assertEquals('mts', TickerHistorical::keyFromId(12));
    }

    /**
     * @return void
     */
    public function testVerifyProps(): void
    {
        $tickerHistorical = new TickerHistorical();
        $this->assertNull($tickerHistorical->id);
        $this->assertNull($tickerHistorical->bid);
        $this->assertNull($tickerHistorical->ask);
        $this->assertNull($tickerHistorical->mts);
        $this->assertNull($tickerHistorical->created_at);
        $this->assertNull($tickerHistorical->updated_at);
    }
}
