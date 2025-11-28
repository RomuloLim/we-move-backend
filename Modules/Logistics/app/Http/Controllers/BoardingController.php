<?php

namespace Modules\Logistics\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Logistics\Http\Requests\{BoardRequest, UnboardRequest};
use Modules\Logistics\Http\Resources\BoardingResource;
use Modules\Logistics\Services\BoardingServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class BoardingController extends Controller
{
    public function __construct(protected BoardingServiceInterface $service) {}

    /**
     * Autoriza o embarque de um estudante.
     * Apenas o motorista responsável pela trip pode autorizar embarques.
     */
    public function board(BoardRequest $request): JsonResponse
    {
        try {
            $boarding = $this->service->boardStudent($request->toDto());

            return BoardingResource::make($boarding)
                ->response()
                ->setStatusCode(StatusCode::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], StatusCode::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Realiza o desembarque de um estudante.
     * O motorista pode desembarcar qualquer estudante da trip.
     * O estudante pode desembarcar apenas a si mesmo.
     */
    public function unboard(UnboardRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $boarding = $this->service->unboardStudent(
                tripId: $validated['trip_id'],
                studentId: $validated['student_id'],
                requesterId: auth()->id()
            );

            return BoardingResource::make($boarding)->response();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], StatusCode::HTTP_BAD_REQUEST);
        }
    }
}
