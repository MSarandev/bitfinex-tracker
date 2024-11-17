<?php

namespace App\Scheduled;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use App\Exceptions\ExternalApiNotHealthyException;
use App\Helpers\ApiWrapper;
use App\Models\Ticker;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PriceActionChecker
{
    /**
     * @return void
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function __invoke(): void
    {
        self::getPrices();
    }

    /**
     * @throws ExternalApiCallNotSuccessfulException
     * @throws GuzzleException
     * @throws ExternalApiNotHealthyException
     */
    public static function getPrices(): void
    {
        Log::info('Checking prices at: '.now());

        $symbols = config('bitfinex.symbols');
        $apiWrapper = new ApiWrapper();

        $responses = [];

        foreach ($symbols as $symbol) {
            $responses[$symbol] = self::callApi($apiWrapper, $symbol);
        }

        foreach ($responses as $sym => $data) {
            $processed = self::processResponse($data);
            $cacheKey = sprintf('price_action_%s', $sym);
            $cacheValue = json_encode($processed);

            Cache::put($cacheKey, $cacheValue, now()->addMinutes(1));
        }
    }

    /**
     * @param  string  $symbol
     * @return bool
     */
    private static function checkIsCached(string $symbol): bool
    {
        $cacheKey = sprintf('price_action_%s', $symbol);

        return Cache::has($cacheKey);
    }

    /**
     * @param  string  $content
     * @return Ticker
     */
    private static function processResponse(string $content): Ticker
    {
        $decoded = json_decode($content);

        return new Ticker([
            'bid' => $decoded[Ticker::idFromKey('bid')],
            'ask' => $decoded[Ticker::idFromKey('ask')],
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws ExternalApiNotHealthyException
     * @throws ExternalApiCallNotSuccessfulException
     */
    private static function callApi(ApiWrapper $apiWrapper, string $symbol): string
    {
        return $apiWrapper->getCurrentPrices($symbol);
    }
}
