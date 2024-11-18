<?php

namespace App\Helpers;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use App\Exceptions\ExternalApiNotHealthyException;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ApiWrapper
{
    public Client $client;
    public string $baseUrl;
    public string $singleTickerExtension;
    public string $historicalTickerExtension;
    public string $healthExtension;
    public string $healthOkVerification;

    public int $maxLimit;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('bitfinex.base_url');
        $this->singleTickerExtension = config('bitfinex.single_ticker_ext');
        $this->historicalTickerExtension = config('bitfinex.historical_ticker_ext');
        $this->healthExtension = config('bitfinex.health_ext');
        $this->healthOkVerification = config('bitfinex.health_ok_verification');
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

        try {
            $response = $this->client->request(
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
        } catch (ClientException $e) {
            Log::error('External API call failed: '.$e->getMessage());

            throw new ExternalApiCallNotSuccessfulException();
        }
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

        try {
            $response = $this->client->request(
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
        } catch (ClientException $e) {
            Log::error('External API call failed: '.$e->getMessage());

            throw new ExternalApiCallNotSuccessfulException();
        }
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
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    private function apiHealth(): void
    {
        $url = sprintf("%s%s", $this->baseUrl, $this->healthExtension);

        $response = $this->client->request(
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
