<?php

namespace Tests\Feature\Notifications;

use App\Notifications\PercentDeltaNotification;
use Tests\TestCase;

class PercentDeltaNotificationTest extends TestCase
{
    public function testConstruct(): void
    {
        $userConfig = 'test';
        $percentDeltaNotification = new PercentDeltaNotification($userConfig);

        $toMail = $percentDeltaNotification->toMail($this);

        $this->assertEquals(['mail'], $percentDeltaNotification->via($this));
        $this->assertEmpty($percentDeltaNotification->toArray($this));
        $this->assertEquals('Percent change alert!', $toMail->introLines[0]);
        $this->assertEquals('Your percent change alert has triggered.', $toMail->introLines[1]);
        $this->assertEquals(sprintf('Your config: %s', $userConfig), $toMail->introLines[2]);
        $this->assertEquals('Check the current prices here', $toMail->actionText);
    }
}
