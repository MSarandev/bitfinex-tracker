<?php

namespace Tests\Feature\Controllers\API;

use App\Models\PriceAction;
use App\Models\User;
use Tests\TestCase;

class PriceActionControllerTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->createOne(['email' => 'test@test.com']);
    }

    public function testListAllPercentDeltas(): void
    {
        $actions = [
            PriceAction::factory()->create([
                'user_id' => $this->user->id,
                'active' => 1
            ]),
            PriceAction::factory()->create([
                'user_id' => $this->user->id,
                'active' => 1
            ]),
            PriceAction::factory()->create([
                'user_id' => $this->user->id,
                'active' => 1
            ]),
            PriceAction::factory()->create([
                'user_id' => $this->user->id,
                'active' => 1
            ])
        ];

        $token = $this->getToken($this->user);
        $response = $this->getJson(
            '/api/v1/price-action/all',
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(200);
        $this->assertNotNull($response->content());
        $this->assertCount(count($actions), json_decode($response->content(), true));
    }

    public function testGetSingleDelta(): void
    {
        $action = PriceAction::factory()->create([
            'user_id' => $this->user->id,
            'active' => 1
        ]);

        $token = $this->getToken($this->user);
        $response = $this->getJson(
            sprintf('/api/v1/price-action/%d', $action->id),
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(200);
        $this->assertNotNull($response->content());

        $decoded = json_decode($response->content());

        $this->assertEquals($action->id, $decoded->id);
        $this->assertEquals($action->trigger, $decoded->trigger);
        $this->assertEquals($action->price, $decoded->price);
        $this->assertEquals($action->symbol, $decoded->symbol);
    }
}
