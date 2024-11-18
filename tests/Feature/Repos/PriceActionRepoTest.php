<?php

namespace Tests\Feature\Repos;

use App\Models\PriceAction;
use App\Models\User;
use App\Repositories\PriceActionsRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceActionRepoTest extends TestCase
{
    use RefreshDatabase;

    protected PriceActionsRepo $repo;
    protected User $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repo = new PriceActionsRepo(new PriceAction());
        $this->user = User::factory()->createOne();
    }

    /**
     * @return void
     */
    public function testGetAllEntriesPerUser(): void
    {
        $users = User::factory()->createMany(2)->all();

        $priceAction = PriceAction::factory()->createOne(['user_id' => $users[0]->id]);
        PriceAction::factory()->createOne(['user_id' => $users[1]->id]);

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
        $priceAction = PriceAction::factory()->createOne(['user_id' => $this->user->id]);

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
        $trigger = 'above';
        $price = fake()->randomFloat(2, 1, 1000);;
        $symbol = 'tBTCUSD';

        $validated = [
            'trigger' => $trigger,
            'price' => $price,
            'symbol' => $symbol,
            'user_id' => $this->user->id
        ];

        $this->repo->createNewEntry($validated);

        $entry = $this->repo->getAllEntriesPerUser($this->user->id)->first();

        $this->assertNotNull($entry);
        $this->assertEquals($trigger, $entry->trigger);
        $this->assertEquals($price, $entry->price);
        $this->assertEquals($symbol, $entry->symbol);
    }

    /**
     * @return void
     */
    public function testActivateAnEntry(): void
    {
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);

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
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);

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
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);

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
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => false]);
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => false]);

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
    public function testGetAllActiveStatic(): void
    {
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => true]);
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => false]);
        PriceAction::factory()->createOne(['user_id' => $this->user->id, 'active' => false]);

        $entries = PriceActionsRepo::getAllActiveStatic();

        $this->assertNotNull($entries);
        $this->assertCount(1, $entries);

        foreach ($entries as $entry) {
            $this->assertEquals($this->user->id, $entry->user_id);
            $this->assertTrue((bool) $entry->active);
        }
    }
}
