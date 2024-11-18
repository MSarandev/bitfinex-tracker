<?php

namespace Tests\Feature\Helpers;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use App\Exceptions\ExternalApiNotHealthyException;
use App\Helpers\ApiWrapper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class ApiWrapperTest extends TestCase
{
    protected ApiWrapper $wrapper;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->wrapper = new ApiWrapper();
    }

    /**
     * @return void
     */
    public function testConstruct(): void
    {
        $baseUrl = config('bitfinex.base_url');
        $singleTickerExtension = config('bitfinex.single_ticker_ext');
        $historicalTickerExtension = config('bitfinex.historical_ticker_ext');
        $healthExtension = config('bitfinex.health_ext');
        $healthOkVerification = config('bitfinex.health_ok_verification');
        $maxLimit = config('bitfinex.max_limit');

        $this->assertEquals($baseUrl, $this->wrapper->baseUrl);
        $this->assertEquals($singleTickerExtension, $this->wrapper->singleTickerExtension);
        $this->assertEquals($historicalTickerExtension, $this->wrapper->historicalTickerExtension);
        $this->assertEquals($healthExtension, $this->wrapper->healthExtension);
        $this->assertEquals($healthOkVerification, $this->wrapper->healthOkVerification);
        $this->assertEquals($maxLimit, $this->wrapper->maxLimit);
    }

    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function testGetHistoricalTickerData(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"data": "test"}'
            ),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        $response = $this->wrapper->getHistoricalTickerData(
            'tBTCUSD',
            now()->subDay(),
            now()
        );

        $this->assertEquals('{"data": "test"}', $response);
    }

    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function testGetHistoricalTickerDataPreParsedTimeFrame(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"data": "test"}'
            ),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        $response = $this->wrapper->getHistoricalTickerData(
            'tBTCUSD',
            now()->subDay()->getTimestampMs(),
            now()->getTimestampMs(),
            true
        );

        $this->assertEquals('{"data": "test"}', $response);
    }

    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function testGetHistoricalTickerDataApiMoved(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(302),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        $this->expectException(ExternalApiCallNotSuccessfulException::class);
        $this->wrapper->getHistoricalTickerData(
            'tBTCUSD',
            now()->subDay(),
            now()
        );
    }

    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function testGetHistoricalTickerDataApiFailure(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(400),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        $this->expectException(ExternalApiCallNotSuccessfulException::class);
        $this->wrapper->getHistoricalTickerData(
            'tBTCUSD',
            now()->subDay(),
            now()
        );
    }

    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function testGetCurrentPrices(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"data": "test"}'
            ),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        $response = $this->wrapper->getCurrentPrices('tBTCUSD');

        $this->assertEquals('{"data": "test"}', $response);
    }

    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function testGetCurrentPricesMoved(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(302),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        $this->expectException(ExternalApiCallNotSuccessfulException::class);
        $this->wrapper->getCurrentPrices('tBTCUSD');
    }

    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function testGetCurrentPricesApiFailure(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[1]'), // healthcheck
            new Response(400),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        $this->expectException(ExternalApiCallNotSuccessfulException::class);
        $this->wrapper->getCurrentPrices('tBTCUSD');
    }

    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function testApiNotHealthy(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '[0]'), // healthcheck
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $this->wrapper->client = new Client(['handler' => $handlerStack]);

        $this->expectException(ExternalApiNotHealthyException::class);
        $this->wrapper->getHistoricalTickerData(
            'tBTCUSD',
            now()->subDay(),
            now()
        );
    }
}
