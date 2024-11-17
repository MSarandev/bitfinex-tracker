<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ExternalApiCallNotSuccessfulException;
use App\Exceptions\ExternalApiNotHealthyException;
use App\Helpers\ApiWrapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetHistoricalDataRequest;
use App\Models\TickerHistorical;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class HistoricalDataController extends Controller
{
    /**
     * @param  GetHistoricalDataRequest  $request
     * @return JsonResponse
     * @throws ExternalApiCallNotSuccessfulException
     * @throws GuzzleException|ExternalApiNotHealthyException
     */
    public function getHistoricalData(GetHistoricalDataRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $apiWrapper = new ApiWrapper();

        $response = $apiWrapper->getHistoricalTickerData(
            $validated['symbol'],
            $validated['from'] ?? null,
            $validated['to'] ?? null,
        );

        $responseStack = [];


        foreach (json_decode($response) as $data) {
            $entry = new TickerHistorical([
                'bid' => $data[TickerHistorical::idFromKey('bid')],
                'ask' => $data[TickerHistorical::idFromKey('ask')],
                'mts' => $data[TickerHistorical::idFromKey('mts')],
            ]);

            $responseStack[] = $entry;
        }

        return response()->json($responseStack);
    }
}
