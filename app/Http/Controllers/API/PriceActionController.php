<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewPriceActionRequest;
use App\Http\Requests\SinglePriceActionRequest;
use App\Services\PriceActionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;

class PriceActionController extends Controller
{
    private const NOT_FOUND_LABEL = 'Not Found';
    private const UNAUTHORISED = 'Unauthorised';

    protected PriceActionService $service;

    /**
     * @param  PriceActionService  $service
     */
    public function __construct(PriceActionService $service)
    {
        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function listAllPriceActions(): JsonResponse
    {
        try {
            return response()->json($this->service->getAllPerUser());
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        }
    }

    /**
     * @param  SinglePriceActionRequest  $request
     * @return JsonResponse
     */
    public function getSinglePriceAction(SinglePriceActionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $entry = $this->service->getSinglePriceAction($validated['entry_id']);

            return response()->json($entry);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        } catch (ModelNotFoundException $me) {
            return response()->json(['error' => self::NOT_FOUND_LABEL], 404);
        }
    }

    /**
     * @param  NewPriceActionRequest  $request
     * @return JsonResponse
     */
    public function addNewPriceAction(NewPriceActionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->addNewPriceAction($validated);

            return response()->json(null, 204);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        }
    }

    /**
     * @param  SinglePriceActionRequest  $request
     * @return JsonResponse|null
     */
    public function activatePriceAction(SinglePriceActionRequest $request): ?JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->activatePriceAction($validated['entry_id']);

            return response()->json(null, 204);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        } catch (ModelNotFoundException $me) {
            return response()->json(['error' => self::NOT_FOUND_LABEL], 404);
        }
    }

    /**
     * @param  SinglePriceActionRequest  $request
     * @return JsonResponse|null
     */
    public function deactivatePriceAction(SinglePriceActionRequest $request): ?JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->deactivatePriceAction($validated['entry_id']);

            return response()->json(null, 204);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        } catch (ModelNotFoundException $me) {
            return response()->json(['error' => self::NOT_FOUND_LABEL], 404);
        }
    }

    /**
     * @param  SinglePriceActionRequest  $request
     * @return JsonResponse|null
     */
    public function deletePriceAction(SinglePriceActionRequest $request): ?JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->deletePriceAction($validated['entry_id']);

            return response()->json(null, 204);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        } catch (ModelNotFoundException $me) {
            return response()->json(['error' => self::NOT_FOUND_LABEL], 404);
        }
    }
}
