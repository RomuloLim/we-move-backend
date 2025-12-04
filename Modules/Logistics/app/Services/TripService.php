<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\TripDto;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\Trip;
use Modules\Logistics\Repositories\Trip\TripRepositoryInterface;

class TripService implements TripServiceInterface
{
    public function __construct(
        protected TripRepositoryInterface $repository
    ) {}

    public function startTrip(TripDto $data): Trip
    {
        // Check if driver has an active trip
        if ($this->repository->hasActiveTrip($data->driverId)) {
            throw new \Exception('Você já possui uma viagem em progresso. Finalize-a antes de iniciar outra.');
        }

        // Check if vehicle is already in use
        if ($this->repository->hasActiveVehicle($data->vehicleId)) {
            throw new \Exception('Este veículo já está sendo utilizado em outra viagem em andamento.');
        }

        // Check for existing IN PROGRESS trip on the same route and date
        $existingTrip = Trip::where('route_id', $data->routeId)
            ->where('trip_date', $data->tripDate)
            ->where('status', TripStatus::InProgress)
            ->first();

        if ($existingTrip) {
            // If the existing trip is assigned to the same driver, return it
            if ($existingTrip->driver_id === $data->driverId) {
                return $existingTrip;
            }

            throw new \Exception('Já existe uma viagem em progresso para esta rota na data selecionada.');
        }

        // Check for scheduled trip that can be started
        $scheduledTrip = Trip::where('route_id', $data->routeId)
            ->where('trip_date', $data->tripDate)
            ->where('status', TripStatus::Scheduled)
            ->where('driver_id', $data->driverId)
            ->first();

        if ($scheduledTrip) {
            return $this->repository->update($scheduledTrip->id, [
                'status' => TripStatus::InProgress,
                'vehicle_id' => $data->vehicleId,
            ]);
        }

        // Create new trip with status InProgress
        $tripData = new TripDto(
            routeId: $data->routeId,
            driverId: $data->driverId,
            vehicleId: $data->vehicleId,
            tripDate: $data->tripDate,
            status: TripStatus::InProgress
        );

        return $this->repository->create($tripData);
    }

    public function completeTrip(int $tripId, int $driverId): ?Trip
    {
        $trip = $this->repository->find($tripId);

        if (!$trip) {
            throw new \Exception('Viagem não encontrada.');
        }

        if ($trip->driver_id !== $driverId) {
            throw new \Exception('Você não tem permissão para finalizar esta viagem.');
        }

        if ($trip->status !== TripStatus::InProgress) {
            throw new \Exception('Apenas viagens em progresso podem ser finalizadas.');
        }

        return $this->repository->update($tripId, [
            'status' => TripStatus::Completed,
        ]);
    }

    public function getActiveTrips(?int $userId = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getActiveTrips($userId, $perPage);
    }

    public function find(int $id): ?Trip
    {
        return $this->repository->find($id);
    }

    public function getActiveTripForDriver(int $driverId): ?Trip
    {
        return $this->repository->findByDriverAndStatus($driverId, TripStatus::InProgress);
    }

    public function getActiveTripForStudent(int $studentId): ?Trip
    {
        // First, find the student record by user_id
        $student = \Modules\Operation\Models\Student::where('user_id', $studentId)->first();

        if (!$student) {
            return null;
        }

        return $this->repository->findActiveTripForStudent($student->id);
    }
}
