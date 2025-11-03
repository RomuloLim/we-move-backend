<?php

namespace Modules\Operation\Repositories\StudentRequisition;

use Modules\Operation\DTOs\StudentRequisitionDto;
use Modules\Operation\Enums\RequisitionStatus;
use Modules\Operation\Models\StudentRequisition;

class StudentRequisitionRepository implements StudentRequisitionRepositoryInterface
{
    public function find(int $id): ?StudentRequisition
    {
        return StudentRequisition::query()->find($id);
    }

    public function findOrFail(int $id): StudentRequisition
    {
        return StudentRequisition::query()->findOrFail($id);
    }

    public function findByProtocol(string $protocol): ?StudentRequisition
    {
        return StudentRequisition::query()
            ->where('protocol', $protocol)
            ->first();
    }

    public function hasApprovedRequisition(int $studentId): bool
    {
        return StudentRequisition::query()
            ->where('student_id', $studentId)
            ->where('status', RequisitionStatus::Approved)
            ->exists();
    }

    public function getPendingRequisition(int $studentId): ?StudentRequisition
    {
        return StudentRequisition::query()
            ->where('student_id', $studentId)
            ->where('status', RequisitionStatus::Pending)
            ->first();
    }

    public function create(StudentRequisitionDto $data): StudentRequisition
    {
        return StudentRequisition::query()
            ->create($data->toArray());
    }

    public function update(StudentRequisition $requisition, StudentRequisitionDto $data): StudentRequisition
    {
        $requisition->update($data->toArray());

        return $requisition->fresh();
    }

    public function delete(int $id): bool
    {
        $requisition = $this->find($id);

        return (bool) $requisition?->delete();
    }

    public function protocolExists(string $protocol): bool
    {
        return StudentRequisition::query()
            ->where('protocol', $protocol)
            ->exists();
    }
}
