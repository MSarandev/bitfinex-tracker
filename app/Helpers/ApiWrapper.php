<?php

namespace App\Helpers;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiWrapper
{
    private static $client;
    private string $baseUrl;
    private string $singleTickerExtension;
    private string $historicalTickerExtension;
    private string $symbolReplacementFlag;

    public function __construct()
    {
        $this->baseUrl = config('bitfinex.base_url');
        $this->singleTickerExtension = config('bitfinex.single_ticker_ext');
        $this->historicalTickerExtension = config('bitfinex.historical_ticker_ext');
        $this->symbolReplacementFlag = config('bitfinex.symbol_replacement_flag');
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
}
