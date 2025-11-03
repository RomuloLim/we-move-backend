<?php

namespace Modules\Operation\Repositories\Document;

use Illuminate\Support\Collection;
use Modules\Operation\DTOs\DocumentDto;
use Modules\Operation\Models\Document;

interface DocumentRepositoryInterface
{
    /**
     * Find a document by ID.
     */
    public function find(int $id): ?Document;

    /**
     * Find a document by ID or fail.
     */
    public function findOrFail(int $id): Document;

    /**
     * Get all documents for a student.
     */
    public function getByStudentId(int $studentId): Collection;

    /**
     * Create a new document.
     */
    public function create(DocumentDto $data): Document;

    /**
     * Delete a document by ID.
     */
    public function delete(int $id): bool;

    /**
     * Attach documents to a requisition.
     *
     * @param  array<int>  $documentIds
     */
    public function attachToRequisition(int $requisitionId, array $documentIds): void;

    /**
     * Sync documents with a requisition.
     *
     * @param  array<int>  $documentIds
     */
    public function syncWithRequisition(int $requisitionId, array $documentIds): void;
}
