<?php

namespace Tests\Feature\Controllers\API;

use App\Helpers\ApiWrapper;
use App\Models\TickerHistorical;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Tests\TestCase;

class HistoricalDataControllerTest extends TestCase
{
    protected ApiWrapper $wrapper;
    protected User $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->wrapper = new ApiWrapper();
        $this->user = User::factory()->createOne(['email' => 'test@test.com', 'password' => 'test']);
    }

    /**
     * @return void
     */
    public function testGetHistoricalData(): void
    {
        $expectedEntries = $this->generateEntries(2);
        $expectedObjects = $this->generateObjects($expectedEntries);

        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedEntries)
            ),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        Mockery::mock(ApiWrapper::class, [$this->wrapper->client]);
        $this->app->instance(ApiWrapper::class, $this->wrapper);

        $token = $this->getToken($this->user);

        $response = $this->getJson(
            '/api/v1/historical?symbol=tBTCUSD&from=2024-01-01&to=2024-01-02',
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(200);

        foreach (json_decode($response->content()) as $responseEntry) {
            foreach ($responseEntry as $re) {
                $this->assertContains($re->mts, array_column($expectedObjects, 'mts'));
            }
        }
    }

    /**
     * @return void
     */
    public function testGetHistoricalDataFaulty(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(400),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        Mockery::mock(ApiWrapper::class, [$this->wrapper->client]);
        $this->app->instance(ApiWrapper::class, $this->wrapper);

        $token = $this->getToken($this->user);

        $response = $this->getJson(
            '/api/v1/historical?symbol=tBTCUSD&from=2024-01-01&to=2024-01-02',
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(500);
    }

    /**
     * @return void
     */
    public function testLoadDashboardData(): void
    {
        $expectedEntries = $this->generateEntries(2);
        $expectedObjects = $this->generateObjects($expectedEntries);

        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedEntries)
            ),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        Mockery::mock(ApiWrapper::class, [$this->wrapper->client]);
        $this->app->instance(ApiWrapper::class, $this->wrapper);

        $token = $this->getToken($this->user);

        $response = $this->getJson(
            '/api/v1/historical/dashboard?symbol=tBTCUSD',
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(200);

        foreach (json_decode($response->content()) as $responseEntry) {
            foreach ($responseEntry as $re) {
                $this->assertContains($re->mts, array_column($expectedObjects, 'mts'));
            }
        }
    }

    /**
     * @return void
     */
    public function testLoadDashboardDataFaulty(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(400),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        Mockery::mock(ApiWrapper::class, [$this->wrapper->client]);
        $this->app->instance(ApiWrapper::class, $this->wrapper);

        $token = $this->getToken($this->user);

        $response = $this->getJson(
            '/api/v1/historical/dashboard?symbol=tBTCUSD',
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(500);
    }

    /**
     * @param  int  $count
     * @return array
     */
    private function generateEntries(int $count): array
    {
        $entries = [];

        for ($i = 0; $i < $count; $i++) {
            $entries[] = [
                "tBTCUSD",
                fake()->numberBetween(2, 1000),
                null,
                fake()->numberBetween(2, 1000),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                fake()->dateTimeThisMonth->getTimestamp()
            ];
        }

        return $entries;
    }

    /**
     * @param  array  $response
     * @return array
     */
    private function generateObjects(array $response): array
    {
        $responseStack = [];

        foreach ($response as $data) {
            $entry = new TickerHistorical([
                'bid' => $data[TickerHistorical::idFromKey('bid')],
                'ask' => $data[TickerHistorical::idFromKey('ask')],
                'mts' => $data[TickerHistorical::idFromKey('mts')],
            ]);

            $responseStack[] = $entry;
        }

        return $responseStack;
    }
}
