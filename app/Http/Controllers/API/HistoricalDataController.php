<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiWrapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\GetHistoricalDataRequest;
use App\Models\TickerHistorical;

class HistoricalDataController extends Controller
{
    public function getHistoricalData(GetHistoricalDataRequest $request)
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
