<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetHistoricalDataRequest;
use App\Services\HistoricalDataService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class HistoricalDataController extends Controller
{
    private HistoricalDataService $historicalDataService;

    public function __construct(HistoricalDataService $historicalDataService)
    {
        $this->historicalDataService = $historicalDataService;
    }

    /**
     * @param  GetHistoricalDataRequest  $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getHistoricalData(GetHistoricalDataRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $data = $this->historicalDataService->getHistoricalData(
                $validated['symbol'],
                $validated['from'],
                $validated['to']
            );

            return response()->json(["data" => $data]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
