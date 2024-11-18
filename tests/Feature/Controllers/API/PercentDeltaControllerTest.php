<?php

namespace Tests\Feature\Controllers\API;

use App\Models\PercentDelta;
use App\Models\User;
use Tests\TestCase;

class PercentDeltaControllerTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->createOne(['email' => 'test@test.com']);
    }

    public function testListAllPercentDeltas(): void
    {
        $deltas = [
            PercentDelta::factory()->create([
                'user_id' => $this->user->id,
                'active' => 1
            ]),
            PercentDelta::factory()->create([
                'user_id' => $this->user->id,
                'active' => 1
            ]),
            PercentDelta::factory()->create([
                'user_id' => $this->user->id,
                'active' => 1
            ]),
            PercentDelta::factory()->create([
                'user_id' => $this->user->id,
                'active' => 1
            ])
        ];

        $token = $this->getToken($this->user);
        $response = $this->getJson(
            '/api/v1/percent-delta/all',
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(200);
        $this->assertNotNull($response->content());
        $this->assertCount(count($deltas), json_decode($response->content(), true));
    }

    public function testGetSingleDelta(): void
    {
        $delta = PercentDelta::factory()->create([
            'user_id' => $this->user->id,
            'active' => 1
        ]);

        $token = $this->getToken($this->user);
        $response = $this->getJson(
            sprintf('/api/v1/percent-delta/%d', $delta->id),
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(200);
        $this->assertNotNull($response->content());

        $decoded = json_decode($response->content());

        $this->assertEquals($delta->id, $decoded->id);
        $this->assertEquals($delta->symbol, $decoded->symbol);
        $this->assertEquals($delta->timeframe_flag, $decoded->timeframe_flag);
        $this->assertEquals($delta->timeframe_value, $decoded->timeframe_value);
        $this->assertEquals($delta->percent_change, $decoded->percent_change);
    }
}
