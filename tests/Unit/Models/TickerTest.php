<?php

namespace tests\Unit\Models;

use App\Models\Ticker;
use Tests\TestCase;

class TickerTest extends TestCase
{
    /**
     * @return void
     */
    public function testFillable(): void
    {
        $expected = [
            'bid',
            'ask',
        ];

        $this->assertEquals($expected, (new Ticker())->getFillable());
    }

    /**
     * @return void
     */
    public function testApiMarshaller(): void
    {
        $expected = [
            0 => 'bid',
            1 => 'bidSize',
            2 => 'ask',
            3 => 'askSize',
            4 => 'dailyChange',
            5 => 'dailyChangeRelative',
            6 => 'lastPrice',
            7 => 'volume',
            8 => 'high',
            9 => 'low',
        ];

        $this->assertEquals($expected, Ticker::$apiMarshaller);
    }

    /**
     * @return void
     */
    public function testApiUnMarshaller(): void
    {
        $expected = [
            'bid' => 0,
            'bidSize' => 1,
            'ask' => 2,
            'askSize' => 3,
            'dailyChange' => 4,
            'dailyChangeRelative' => 5,
            'lastPrice' => 6,
            'volume' => 7,
            'high' => 8,
            'low' => 9,
        ];

        $this->assertEquals($expected, Ticker::$apiUnMarshaller);
    }

    /**
     * @return void
     */
    public function testIdFromKey(): void
    {
        $this->assertEquals(0, Ticker::idFromKey('bid'));
        $this->assertEquals(1, Ticker::idFromKey('bidSize'));
        $this->assertEquals(2, Ticker::idFromKey('ask'));
        $this->assertEquals(7, Ticker::idFromKey('volume'));
    }

    /**
     * @return void
     */
    public function testKeyFromId(): void
    {
        $this->assertEquals('bid', Ticker::keyFromId(0));
        $this->assertEquals('bidSize', Ticker::keyFromId(1));
        $this->assertEquals('ask', Ticker::keyFromId(2));
        $this->assertEquals('volume', Ticker::keyFromId(7));
    }

    public function testVerifyProps(): void
    {
        $ticker = new Ticker();
        $this->assertNull($ticker->bid);
        $this->assertNull($ticker->ask);
    }
}
