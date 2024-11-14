<?php

namespace App\Helpers;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

class ApiWrapper
{
    private static $client;
    private string $baseUrl;
    private string $singleTickerExtension;
    private string $historicalTickerExtension;
    private string $symbolReplacementFlag;
    private array $acceptedSymbols;

    private array $allowedTimeFrames;


    public function __construct()
    {
        $this->baseUrl = config('bitfinex.base_url');
        $this->singleTickerExtension = config('bitfinex.single_ticker_ext');
        $this->historicalTickerExtension = config('bitfinex.historical_ticker_ext');
        $this->symbolReplacementFlag = config('bitfinex.symbol_replacement_flag');
        $this->acceptedSymbols = config('bitfinex.symbols');
        $this->allowedTimeFrames = config('bitfinex.allowed_period_markers');
    }

    /**
     * @throws ExternalApiCallNotSuccessfulException
     * @throws GuzzleException
     */
    public function getHistoricalTickerData(
        string $symbol,
        string $from = null,
        string $to = null,
    ): string {
        $url = sprintf("%s%s", $this->baseUrl, $this->historicalTickerExtension);

        // else -> set the period
        $limits = $this->buildTimeFrame($from, $to);


        $response = $this->getClient()->request(
            'GET',
            $url,
            [
                'query' => [
                    'symbols' => $symbol,
                    $limits['from'] === null ?: 'start' => $limits['from'],
                    $limits['to'] === null ?: 'end' => $limits['to'],
                ]
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new ExternalApiCallNotSuccessfulException();
        }

        return $response->getBody()->getContents();
    }

    private function buildTimeFrame($from, $to): array
    {
        return [
            'from' => $from !== null ? Carbon::parse($from)->getTimestampMs() : null,
            'to' => $to !== null ? Carbon::parse($to)->getTimestampMs() : null,
        ];
    }

    private
    function getClient(): Client
    {
        if (!self::$client) {
            self::$client = new Client();
        }

        return self::$client;
    }
}
