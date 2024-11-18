<?php

namespace Tests\Feature\Repos;

use App\Models\PercentDelta;
use App\Models\User;
use App\Repositories\PercentDeltaRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PercentDeltaRepoTest extends TestCase
{
    use RefreshDatabase;

    protected PercentDeltaRepo $repo;
    protected User $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repo = new PercentDeltaRepo(new PercentDelta());
        $this->user = User::factory()->createOne();
    }

    /**
     * @return void
     */
    public function testGetAllEntriesPerUser(): void
    {
        $users = User::factory()->createMany(2)->all();

        $priceAction = PercentDelta::factory()->createOne(['user_id' => $users[0]->id]);
        PercentDelta::factory()->createOne(['user_id' => $users[1]->id]);

        $this->assertEquals($users[0]->id, $priceAction->user_id);

        $entries = $this->repo->getAllEntriesPerUser($users[0]->id);

        $this->assertNotNull($entries);
        $this->assertCount(1, $entries);

        foreach ($entries as $entry) {
            $this->assertEquals($priceAction->id, $entry->id);
            $this->assertEquals($users[0]->id, $entry->user_id);
        }
    }

    /**
     * @return void
     */
    public function testGetSingleEntry(): void
    {
        $priceAction = PercentDelta::factory()->createOne(['user_id' => $this->user->id]);

        $this->assertEquals($this->user->id, $priceAction->user_id);

        $entry = $this->repo->getSingleEntry($priceAction->id, $this->user->id);

        $this->assertNotNull($entry);
        $this->assertEquals($priceAction->id, $entry->id);
        $this->assertEquals($this->user->id, $entry->user_id);
    }

    /**
     * @return void
     */
    public function testCreateNewEntry(): void
    {
        $timeframeValue = fake()->numberBetween(1,23);
        $percentChange = fake()->randomFloat(2, 1, 1000);
        $symbol = 'tBTCUSD';

        $validated = [
            'timeframe_flag' => 'H',
            'timeframe_value' => $timeframeValue,
            'percent_change' => $percentChange,
            'symbol' => $symbol,
            'user_id' => $this->user->id
        ];

        $this->repo->createNewEntry($validated);

        $entry = $this->repo->getAllEntriesPerUser($this->user->id)->first();

        $this->assertNotNull($entry);
        $this->assertEquals($timeframeValue, $entry->timeframe_value);
        $this->assertEquals($percentChange, $entry->percent_change);
        $this->assertEquals($symbol, $entry->symbol);
    }

    /**
     * @return void
     */
    public function testActivateAnEntry(): void
    {
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);

        $entry = $this->repo->getAllEntriesPerUser($this->user->id)->first();

        $this->assertTrue((bool) $entry->active);

        // deactivate
        $this->repo->deactivateAnEntry($entry->id, $this->user->id);
        $entry = $this->repo->getAllEntriesPerUser($this->user->id)->first();

        $this->assertFalse((bool) $entry->active);

        // activate and assert
        $this->repo->activateAnEntry($entry->id, $this->user->id);
        $entry = $this->repo->getAllEntriesPerUser($this->user->id)->first();

        $this->assertTrue((bool) $entry->active);
    }

    /**
     * @return void
     */
    public function testDeactivateAnEntry(): void
    {
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);

        $entry = $this->repo->getAllEntriesPerUser($this->user->id)->first();

        $this->assertTrue((bool) $entry->active);

        // deactivate
        $this->repo->deactivateAnEntry($entry->id, $this->user->id);
        $entry = $this->repo->getAllEntriesPerUser($this->user->id)->first();

        $this->assertFalse((bool) $entry->active);
    }

    /**
     * @return void
     */
    public function testDeleteAnEntry(): void
    {
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);

        $entries = $this->repo->getAllEntriesPerUser($this->user->id);

        $this->assertNotNull($entries);
        $this->assertCount(1, $entries);

        $this->repo->deleteEntry($entries->first()->id, $this->user->id);

        $shouldBeNull = $this->repo->getAllEntriesPerUser($this->user->id)->first();

        $this->assertNull($shouldBeNull);
    }

    /**
     * @return void
     */
    public function testGetAllActive(): void
    {
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => false]);
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => false]);

        $entries = $this->repo->getAllActive();

        $this->assertNotNull($entries);
        $this->assertCount(1, $entries);

        foreach ($entries as $entry) {
            $this->assertEquals($this->user->id, $entry->user_id);
            $this->assertTrue((bool) $entry->active);
        }
    }

    /**
     * @return void
     */
    public function testGetAllStatic(): void
    {
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => false]);
        PercentDelta::factory()->createOne(['user_id' => $this->user->id, 'active' => false]);

        $entries = PercentDeltaRepo::getAllStatic();

        $this->assertNotNull($entries);
        $this->assertCount(1, $entries);

        foreach ($entries as $entry) {
            $this->assertEquals($this->user->id, $entry->user_id);
            $this->assertTrue((bool) $entry->active);
        }
    }
}
