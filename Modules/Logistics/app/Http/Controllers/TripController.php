<?php

namespace Modules\Logistics\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Logistics\Http\Requests\{StartTripRequest};
use Modules\Logistics\Http\Resources\TripResource;
use Modules\Logistics\Services\TripServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class TripController extends Controller
{
    public function __construct(protected TripServiceInterface $service) {}

    /**
     * List active trips (InProgress).
     * Allows filtering by specific user through the 'user_id' query parameter.
     */
    public function activeTrips(Request $request): JsonResponse
    {
        $userId = $request->query('user_id');
        $perPage = $request->query('per_page', 15);

        $trips = $this->service->getActiveTrips($userId, $perPage);

        return TripResource::collection($trips)->response();
    }

    /**
     * Starts a new trip.
     * Only drivers can start trips.
     */
    public function start(StartTripRequest $request): JsonResponse
    {
        try {
            $trip = $this->service->startTrip($request->toDto());

            return TripResource::make($trip)
                ->response()
                ->setStatusCode(StatusCode::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], StatusCode::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Completes a trip in progress.
     * Only the driver of the trip can complete it.
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        try {
            $trip = $this->service->completeTrip($id, auth()->id());

            if (!$trip) {
                return response()->json([
                    'message' => 'Viagem não encontrada.',
                ], StatusCode::HTTP_NOT_FOUND);
            }

            return TripResource::make($trip)->response();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], StatusCode::HTTP_BAD_REQUEST);
        }
    }
}
