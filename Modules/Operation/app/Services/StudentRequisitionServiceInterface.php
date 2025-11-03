<?php

namespace Modules\Operation\Services;

use Illuminate\Http\UploadedFile;
use Modules\Operation\DTOs\StudentRequisitionDto;
use Modules\Operation\Models\StudentRequisition;

interface StudentRequisitionServiceInterface
{
    /**
     * Check if a student has an approved requisition.
     */
    public function hasApprovedRequisition(int $studentId): bool;

    /**
     * Get pending requisition for a student.
     */
    public function getPendingRequisition(int $studentId): ?StudentRequisition;

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
}
