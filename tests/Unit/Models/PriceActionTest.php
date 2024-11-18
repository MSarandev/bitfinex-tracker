<?php

namespace tests\Unit\Models;

use App\Models\PriceAction;
use Tests\TestCase;

class PriceActionTest extends TestCase
{
    /**
     * @return void
     */
    public function testFillable(): void
    {
        $expected = [
            'id',
            'user_id',
            'trigger',
            'price',
            'active',
            'symbol'
        ];

        $this->assertEquals($expected, (new PriceAction())->getFillable());
    }

    /**
     * @return void
     */
    public function testTableVerification(): void
    {
        $expected = 'price_actions';

        $this->assertEquals($expected, (new PriceAction())->getTable());
    }

    /**
     * @return void
     */
    public function testVerifyProps(): void
    {
        $priceAction = new PriceAction();
        $this->assertNull($priceAction->id);
        $this->assertNull($priceAction->user_id);
        $this->assertNull($priceAction->trigger);
        $this->assertNull($priceAction->price);
        $this->assertNull($priceAction->active);
        $this->assertNull($priceAction->symbol);
    }

    /**
     * @return void
     */
    public function testIsHit(): void
    {
        $priceAction = new PriceAction();
        $priceAction->price = 100;
        $priceAction->trigger = 'above';

        $this->assertTrue($priceAction->isHit(101));
        $this->assertFalse($priceAction->isHit(99));

        $priceAction->trigger = 'below';

        $this->assertTrue($priceAction->isHit(99));
        $this->assertFalse($priceAction->isHit(101));
    }
}
