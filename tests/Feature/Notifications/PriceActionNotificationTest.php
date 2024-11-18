<?php

namespace Tests\Feature\Notifications;

use App\Notifications\PriceActionNotification;
use Tests\TestCase;

class PriceActionNotificationTest extends TestCase
{
    public function testConstruct(): void
    {
        $userConfig = 'test';
        $priceActionNotif = new PriceActionNotification($userConfig);

        $toMail = $priceActionNotif->toMail($this);

        $this->assertEquals(['mail'], $priceActionNotif->via($this));
        $this->assertEmpty($priceActionNotif->toArray($this));
        $this->assertEquals('Price action alert!', $toMail->introLines[0]);
        $this->assertEquals('Your price action alert has triggered.', $toMail->introLines[1]);
        $this->assertEquals(sprintf('Your config: %s', $userConfig), $toMail->introLines[2]);
        $this->assertEquals('Check the current prices here', $toMail->actionText);
    }
}
