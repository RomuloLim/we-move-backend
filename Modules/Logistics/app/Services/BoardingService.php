<?php

namespace Modules\Logistics\Services;

use Illuminate\Support\Str;
use Modules\Logistics\DTOs\BoardingDto;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\{Boarding, Trip};
use Modules\Logistics\Repositories\Boarding\BoardingRepositoryInterface;
use Modules\Operation\Models\Student;

class BoardingService implements BoardingServiceInterface
{
    public function __construct(
        protected BoardingRepositoryInterface $repository
    ) {}

    public function boardStudent(BoardingDto $data): Boarding
    {
        // Verifica se a trip existe e está ativa
        $trip = Trip::find($data->tripId);

        if (!$trip) {
            throw new \Exception('Viagem não encontrada.');
        }

        if ($trip->status !== TripStatus::InProgress) {
            throw new \Exception('A viagem não está em progresso.');
        }

        // Verifica se o motorista da requisição é o responsável pela trip
        if ($trip->driver_id !== $data->driverId) {
            throw new \Exception('Apenas o motorista responsável pela viagem pode autorizar embarques.');
        }

        // Busca o estudante
        $student = Student::find($data->studentId);

        if (!$student) {
            throw new \Exception('Estudante não encontrado.');
        }

        // Valida o token do QR Code
        if ($student->qrcode_token !== $data->qrcodeToken) {
            throw new \Exception('Token de QR Code inválido.');
        }

        // Verifica se o estudante já está embarcado (não desembarcou da última viagem)
        $activeBoarding = $this->repository->findActiveBoarding($data->studentId);

        if ($activeBoarding) {
            throw new \Exception('O estudante ainda não desembarcou da viagem anterior.');
        }

        // Cria o embarque
        $boarding = $this->repository->create($data);

        // Gera um novo token de QR Code para o estudante
        $student->update([
            'qrcode_token' => Str::uuid()->toString(),
        ]);

        return $boarding;
    }

    public function unboardStudent(int $tripId, int $studentId, int $requesterId): Boarding
    {
        // Verifica se a trip existe
        $trip = Trip::find($tripId);

        if (!$trip) {
            throw new \Exception('Viagem não encontrada.');
        }

        // Busca o estudante
        $student = Student::find($studentId);

        if (!$student) {
            throw new \Exception('Estudante não encontrado.');
        }

        // Verifica permissões: motorista pode desembarcar qualquer um, estudante só a si mesmo
        if ($trip->driver_id === $requesterId) {
            // Motorista pode desembarcar qualquer estudante
        } elseif ($student->user_id === $requesterId) {
            // Estudante pode desembarcar apenas a si mesmo
        } else {
            throw new \Exception('Você não tem permissão para realizar este desembarque.');
        }

        // Busca o embarque ativo do estudante nesta trip
        $boarding = Boarding::query()
            ->where('trip_id', $tripId)
            ->where('student_id', $studentId)
            ->whereNull('landed_at')
            ->first();

        if (!$boarding) {
            throw new \Exception('Estudante não está embarcado nesta viagem.');
        }

        return $this->repository->unboard($boarding);
    }

    public function unboardAllStudents(int $tripId): int
    {
        return $this->repository->unboardAllByTripId($tripId);
    }

    public function getPassengers(int $tripId, ?bool $onlyBoarded = null): \Illuminate\Database\Eloquent\Collection
    {
        $trip = Trip::find($tripId);

        if (!$trip) {
            throw new \Exception('Viagem não encontrada.');
        }

        return $this->repository->getPassengersByTripId($tripId, $onlyBoarded);
    }
}
