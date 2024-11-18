<?php

namespace App\Services;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use App\Exceptions\ExternalApiNotHealthyException;
use App\Helpers\ApiWrapper;
use App\Models\TickerHistorical;
use GuzzleHttp\Exception\GuzzleException;

class HistoricalDataService
{
    private ApiWrapper $apiWrapper;

    public function __construct(ApiWrapper $apiWrapper)
    {
        $this->apiWrapper = $apiWrapper;
    }

    /**
     * @param  string  $symbol
     * @param  string|null  $from
     * @param  string|null  $to
     * @return array
     * @throws ExternalApiCallNotSuccessfulException
     * @throws ExternalApiNotHealthyException
     * @throws GuzzleException
     */
    public function getHistoricalData(string $symbol, string $from = null, string $to = null): array
    {
        $response = $this->apiWrapper->getHistoricalTickerData($symbol, $from ?? null, $to ?? null);

        $responseStack = [];

        foreach (json_decode($response) as $data) {
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
