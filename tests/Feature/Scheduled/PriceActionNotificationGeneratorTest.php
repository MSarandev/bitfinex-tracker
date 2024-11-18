<?php

namespace tests\Feature\Scheduled;

use App\Exceptions\MissingLatestPricesException;
use App\Models\PriceAction;
use App\Models\User;
use App\Notifications\PriceActionNotification;
use App\Scheduled\PriceActionNotificationGenerator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PriceActionNotificationGeneratorTest extends TestCase
{
    protected User $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->createOne(['email' => 'test@test.com']);
    }

    /**
     * @return void
     * @throws MissingLatestPricesException
     */
    public function testGenerateEventsNoPrices(): void
    {
        $this->expectException(MissingLatestPricesException::class);

        PriceActionNotificationGenerator::generateEvents();
    }

    /**
     * @return void
     * @throws MissingLatestPricesException
     */
    public function testGenerateEventsNoPriceActions(): void
    {
        Cache::put('price_action_tBTCEUR', json_encode(['bid' => 85300, 'ask' => 85301]));
        Cache::put('price_action_tBTCUSD', json_encode(['bid' => 90237, 'ask' => 90238]));

        PriceActionNotificationGenerator::generateEvents();

        $this->assertEmpty(PriceAction::all());
    }

    /**
     * @return void
     * @throws MissingLatestPricesException
     */
    public function testGenerateEvents(): void
    {
        Notification::fake();

        Cache::put('price_action_tBTCEUR', json_encode(['bid' => 85300, 'ask' => 85301]));
        Cache::put('price_action_tBTCUSD', json_encode(['bid' => 90237, 'ask' => 90238]));

        $this->generateRecords();

        PriceActionNotificationGenerator::generateEvents();

        Notification::assertSentTo(
            $this->user,
            PriceActionNotification::class,
            static function ($notification, $channels) {
                // check channels or properties of $notification here
                return in_array('mail', $channels, true);
            });
    }

    /**
     * @param  int|null  $userId
     * @return void
     */
    private function generateRecords(int $userId = null): void
    {
        PriceAction::factory()->create([
            'active' => 1,
            'user_id' => $userId ?: $this->user->id,
        ]);
        PriceAction::factory()->create([
            'active' => 1,
            'user_id' => $userId ?: $this->user->id,
        ]);
        PriceAction::factory()->create([
            'active' => 1,
            'user_id' => $userId ?: $this->user->id,
        ]);
    }
}
