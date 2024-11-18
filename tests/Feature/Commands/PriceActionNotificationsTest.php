<?php

namespace Tests\Feature\Commands;

use Mockery;
use Tests\TestCase;

class PriceActionNotificationsTest extends TestCase
{
    public function testHandle(): void
    {
        Mockery::mock('alias:App\Scheduled\PriceActionNotificationGenerator')
            ->shouldReceive('generateEvents')
            ->once();

        $this->artisan('app:price-action-notifications')
            ->assertExitCode(0);
    }
}
