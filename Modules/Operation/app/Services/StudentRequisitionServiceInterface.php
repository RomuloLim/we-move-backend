<?php

namespace Modules\Operation\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\{RequisitionListParamsDto, StudentRequisitionDto};
use Modules\Operation\Models\StudentRequisition;

interface StudentRequisitionServiceInterface
{
    /**
     * List requisitions ordering by status.
     */
    public function listOrderingByStatus(?RequisitionListParamsDto $listParams = null): LengthAwarePaginator;

    public function find(int $id): StudentRequisition;

    /**
     * Check if a student has an approved requisition.
     */
    public function hasApprovedRequisition(int $studentId): bool;

    /**
     * Create or update a student requisition.
     *
     * @param  array<string, UploadedFile>  $files
     * @return array{protocol: string, status: string}
     */
    public function createOrUpdate(
        int $studentId,
        StudentRequisitionDto $requisitionData,
        array $files
    ): array;

    /**
     * Generate a unique protocol.
     */
    public function generateUniqueProtocol(): string;

    public function approve(int $id): StudentRequisition;

    public function reprove(int $id, array $reprovedFields, ?string $reason): StudentRequisition;
}
