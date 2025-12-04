<?php

namespace Modules\Logistics\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Logistics\Http\Requests\{StartTripRequest};
use Modules\Logistics\Http\Resources\TripResource;
use Modules\Logistics\Services\{BoardingServiceInterface, TripServiceInterface};
use Symfony\Component\HttpFoundation\Response as StatusCode;

class TripController extends Controller
{
    public function __construct(
        protected TripServiceInterface $service,
        protected BoardingServiceInterface $boardingService
    ) {}

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
     * Automatically unboards all students before completing.
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        try {
            $this->boardingService->unboardAllStudents($id);

            $trip = $this->service->completeTrip($id, $request->user()->id);

            if (!$trip) {
                return response()->json([
                    'message' => 'Viagem não encontrada.',
                ], StatusCode::HTTP_NOT_FOUND);
            }

            $trip->load(['route', 'boardings']);

            $tripDuration = $trip->created_at->diffForHumans($trip->updated_at, [
                'parts' => 2,
                'short' => true,
                'syntax' => Carbon::DIFF_ABSOLUTE,
            ]);

            return response()->json([
                'message' => 'Viagem finalizada com sucesso.',
                'data' => [
                    'trip' => TripResource::make($trip),
                    'summary' => [
                        'route_name' => $trip->route->route_name,
                        'total_boardings' => $trip->boardings->count(),
                        'duration' => $tripDuration,
                    ],
                ],
            ], StatusCode::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], StatusCode::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get the active trip for the authenticated driver.
     */
    public function myActiveTrip(Request $request): JsonResponse
    {
        $trip = $this->service->getActiveTripForDriver($request->user()->id);

        if (!$trip) {
            return response()->json([
                'message' => 'Nenhuma viagem ativa encontrada.',
            ], StatusCode::HTTP_NOT_FOUND);
        }

        return TripResource::make($trip)->response();
    }

    /**
     * Get the active trip for the authenticated student.
     * Returns the trip where the student is currently boarded (not landed yet).
     */
    public function myActiveTripAsStudent(Request $request): JsonResponse
    {
        $trip = $this->service->getActiveTripForStudent($request->user()->id);

        if (!$trip) {
            return response()->json([
                'message' => 'Nenhuma viagem ativa encontrada.',
            ], StatusCode::HTTP_NOT_FOUND);
        }

        return TripResource::make($trip)->response();
    }
}
