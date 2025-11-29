<?php

namespace Modules\Operation\Services;

use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\Trip;
use Modules\Operation\Models\Student;
use Modules\Operation\Repositories\Student\StudentRepositoryInterface;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;

class StudentService implements StudentServiceInterface
{
    public function __construct(
        protected StudentRepositoryInterface $studentRepository
    ) {}

    public function getStudentFullData(int $studentId, ?User $authenticatedUser = null): Student
    {
        $student = $this->studentRepository->findByIdWithFullData($studentId);

        if (!$student) {
            throw new \Exception('Estudante não encontrado.');
        }

        // Verificar autorização: estudantes só podem ver seus próprios dados
        if ($authenticatedUser && $authenticatedUser->user_type === UserType::Student) {
            if ($student->user_id !== $authenticatedUser->id) {
                throw new \Exception('Você não está autorizado a acessar os dados deste estudante.');
            }
        }

        // Buscar viagens ativas (em progresso) com suas rotas e paradas
        $activeTrips = Trip::with(['route.stops', 'driver', 'vehicle'])
            ->where('status', TripStatus::InProgress)
            ->whereHas('route.stops')
            ->get();

        // Adicionar as viagens ativas ao estudante como uma propriedade dinâmica
        $student->setAttribute('available_trips', $activeTrips);

        return $student;
    }
}
