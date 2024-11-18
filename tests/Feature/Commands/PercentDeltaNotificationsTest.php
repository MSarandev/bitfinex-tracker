<?php

namespace Tests\Feature\Commands;

use Mockery;
use Tests\TestCase;

class PercentDeltaNotificationsTest extends TestCase
{
    public function testHandle(): void
    {
        Mockery::mock('alias:App\Scheduled\PercentDeltaNotificationGenerator')
            ->shouldReceive('generateEvents')
            ->once();

        $this->artisan('app:percent-change-notifications')
            ->assertExitCode(0);
    }
}
