<?php

namespace Modules\Operation\Repositories\StudentRequisition;

use Modules\Operation\DTOs\StudentRequisitionDto;
use Modules\Operation\Models\StudentRequisition;

interface StudentRequisitionRepositoryInterface
{
    /**
     * Find a requisition by ID.
     */
    public function find(int $id): ?StudentRequisition;

    /**
     * Find a requisition by ID or fail.
     */
    public function findOrFail(int $id): StudentRequisition;

    /**
     * Find a requisition by protocol.
     */
    public function findByProtocol(string $protocol): ?StudentRequisition;

    /**
     * Check if a student has an approved requisition.
     */
    public function hasApprovedRequisition(int $studentId): bool;

    /**
     * Get pending requisition for a student.
     */
    public function getPendingRequisition(int $studentId): ?StudentRequisition;

    /**
     * Create a new requisition.
     */
    public function create(StudentRequisitionDto $data): StudentRequisition;

    /**
     * Update a requisition.
     */
    public function update(StudentRequisition $requisition, StudentRequisitionDto $data): StudentRequisition;

    /**
     * Delete a requisition by ID.
     */
    public function delete(int $id): bool;

    /**
     * Check if a protocol exists.
     */
    public function protocolExists(string $protocol): bool;
}
