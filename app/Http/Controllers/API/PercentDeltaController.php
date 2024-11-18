<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewPercentDeltaRequest;
use App\Http\Requests\SinglePercentDeltaRequest;
use App\Services\PercentDeltaService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;

class PercentDeltaController extends Controller
{
    private const NOT_FOUND_LABEL = 'Not Found';
    private const UNAUTHORISED = 'Unauthorised';

    protected PercentDeltaService $service;

    /**
     * @param  PercentDeltaService  $service
     */
    public function __construct(PercentDeltaService $service)
    {
        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function listAllPercentDeltas(): JsonResponse
    {
        try {
            return response()->json($this->service->getAllPerUser());
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        }
    }

    /**
     * @param  SinglePercentDeltaRequest  $request
     * @return JsonResponse
     */
    public function getSinglePercentDelta(SinglePercentDeltaRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $entry = $this->service->getSinglePercentDelta($validated['entry_id']);

            return response()->json($entry);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        } catch (ModelNotFoundException $me) {
            return response()->json(['error' => self::NOT_FOUND_LABEL], 404);
        }
    }

    /**
     * @param  NewPercentDeltaRequest  $request
     * @return JsonResponse
     */
    public function addNewPercentDelta(NewPercentDeltaRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->addNewPercentDelta($validated);

            return response()->json(null, 204);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        }
    }

    /**
     * @param  SinglePercentDeltaRequest  $request
     * @return JsonResponse|null
     */
    public function activatePercentDelta(SinglePercentDeltaRequest $request): ?JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->activatePercentDelta($validated['entry_id']);

            return response()->json(null, 204);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        } catch (ModelNotFoundException $me) {
            return response()->json(['error' => self::NOT_FOUND_LABEL], 404);
        }
    }

    /**
     * @param  SinglePercentDeltaRequest  $request
     * @return JsonResponse|null
     */
    public function deactivatePercentDelta(SinglePercentDeltaRequest $request): ?JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->deactivatePercentDelta($validated['entry_id']);

            return response()->json(null, 204);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        } catch (ModelNotFoundException $me) {
            return response()->json(['error' => self::NOT_FOUND_LABEL], 404);
        }
    }

    /**
     * @param  SinglePercentDeltaRequest  $request
     * @return JsonResponse|null
     */
    public function deletePercentDelta(SinglePercentDeltaRequest $request): ?JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->service->deletePercentDelta($validated['entry_id']);

            return response()->json(null, 204);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => self::UNAUTHORISED], 401);
        } catch (ModelNotFoundException $me) {
            return response()->json(['error' => self::NOT_FOUND_LABEL], 404);
        }
    }
}
