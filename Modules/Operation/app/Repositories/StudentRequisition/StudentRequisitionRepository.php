<?php

namespace Modules\Operation\Repositories\StudentRequisition;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\{RequisitionListParamsDto, StudentRequisitionDto};
use Modules\Operation\Enums\RequisitionStatus;
use Modules\Operation\Models\StudentRequisition;

class StudentRequisitionRepository implements StudentRequisitionRepositoryInterface
{
    public function listOrderingByStatus(?RequisitionListParamsDto $listParams = null): LengthAwarePaginator
    {
        $query = StudentRequisition::query()
            ->with(['student', 'institutionCourse'])
            ->when($listParams?->protocol, fn ($q, $v) => $q->where('protocol', 'ilike', "%$v%"))
            ->when($listParams?->status, fn ($q, $v) => $q->where('status', $v))
            ->when($listParams?->institution_course_id, fn ($q, $v) => $q->where('institution_course_id', $v))
            ->when($listParams?->atuation_form, fn ($q, $v) => $q->where('atuation_form', $v))
            ->orderByRaw(
                "CASE
                    WHEN status = '" . RequisitionStatus::Pending->value . "' THEN 1
                    WHEN status = '" . RequisitionStatus::Approved->value . "' THEN 2
                    WHEN status = '" . RequisitionStatus::Reproved->value . "' THEN 3
                    WHEN status = '" . RequisitionStatus::Expired->value . "' THEN 4
                    ELSE 5
                END"
            )->paginate();

        return $query;
    }

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
