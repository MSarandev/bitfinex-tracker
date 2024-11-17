<?php

namespace App\Helpers;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use App\Exceptions\ExternalApiNotHealthyException;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ApiWrapper
{
    private static $client;
    private string $baseUrl;
    private string $singleTickerExtension;
    private string $historicalTickerExtension;
    private string $healthExtension;
    private string $healthOkVerification;
    private string $symbolReplacementFlag;

    private int $maxLimit;

    public function __construct()
    {
        $this->baseUrl = config('bitfinex.base_url');
        $this->singleTickerExtension = config('bitfinex.single_ticker_ext');
        $this->historicalTickerExtension = config('bitfinex.historical_ticker_ext');
        $this->healthExtension = config('bitfinex.health_ext');
        $this->healthOkVerification = config('bitfinex.health_ok_verification');
        $this->symbolReplacementFlag = config('bitfinex.symbol_replacement_flag');
        $this->maxLimit = config('bitfinex.max_limit');
    }

    /**
     * @throws ExternalApiCallNotSuccessfulException
     * @throws GuzzleException|ExternalApiNotHealthyException
     */
    public function getHistoricalTickerData(
        string $symbol,
        string $from = null,
        string $to = null,
        bool $preParsedTimeFrame = false
    ): string {
        $this->apiHealth();

        $url = sprintf("%s%s", $this->baseUrl, $this->historicalTickerExtension);

        if (!$preParsedTimeFrame) {
            $limits = $this->buildTimeFrame($from, $to);
        } else {
            $limits = [
                'from' => $from,
                'to' => $to,
            ];
        }

        $response = $this->getClient()->request(
            'GET',
            $url,
            [
                'query' => [
                    'symbols' => $symbol,
                    $limits['from'] === null ?: 'start' => $limits['from'],
                    $limits['to'] === null ?: 'end' => $limits['to'],
                    'limit' => $this->maxLimit,
                ]
            ]
        );


        if ($response->getStatusCode() !== 200) {
            throw new ExternalApiCallNotSuccessfulException();
        }

        return $response->getBody()->getContents();
    }

    /**
     * @param  string  $symbol
     * @return string
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function getCurrentPrices(string $symbol)
    {
        $this->apiHealth();

        $url = sprintf("%s%s", $this->baseUrl, $this->singleTickerExtension);

        $response = $this->getClient()->request(
            'GET',
            $url,
            [
                'query' => [
                    'symbol' => $symbol,
                ]
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new ExternalApiCallNotSuccessfulException();
        }

        return $response->getBody()->getContents();
    }

    /**
     * @param $from
     * @param $to
     * @return null[]
     */
    private function buildTimeFrame($from, $to): array
    {
        return [
            'from' => $from !== null ? Carbon::parse($from)->getTimestampMs() : null,
            'to' => $to !== null ? Carbon::parse($to)->getTimestampMs() : null,
        ];
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        if (!self::$client) {
            self::$client = new Client();
        }

        return self::$client;
    }

    /**
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    private function apiHealth(): void
    {
        $url = sprintf("%s%s", $this->baseUrl, $this->healthExtension);

        $response = $this->getClient()->request(
            'GET',
            $url,
        );

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        if ($statusCode !== 200 || $body !== $this->healthOkVerification) {
            Log::warning('External API reporting not healthy at: '.now());

            throw new ExternalApiNotHealthyException();
        }
    }
}
