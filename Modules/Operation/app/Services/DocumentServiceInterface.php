<?php

namespace Modules\Operation\Services;

use Illuminate\Support\Collection;
use Modules\Operation\DTOs\DocumentDto;
use Modules\Operation\Models\Document;

interface DocumentServiceInterface
{
    /**
     * Find a document by ID.
     */
    public function find(int $id): ?Document;

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
}
