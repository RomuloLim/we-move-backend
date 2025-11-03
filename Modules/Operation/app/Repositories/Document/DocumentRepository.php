<?php

namespace Modules\Operation\Repositories\Document;

use Illuminate\Support\Collection;
use Modules\Operation\DTOs\DocumentDto;
use Modules\Operation\Models\{Document, StudentRequisition};

class DocumentRepository implements DocumentRepositoryInterface
{
    public function find(int $id): ?Document
    {
        return Document::query()->find($id);
    }

    public function findOrFail(int $id): Document
    {
        return Document::query()->findOrFail($id);
    }

    public function getByStudentId(int $studentId): Collection
    {
        return Document::query()
            ->where('student_id', $studentId)
            ->get();
    }

    public function create(DocumentDto $data): Document
    {
        return Document::query()
            ->create($data->toArray());
    }

    public function delete(int $id): bool
    {
        $document = $this->find($id);

        return (bool) $document?->delete();
    }

    public function attachToRequisition(int $requisitionId, array $documentIds): void
    {
        $requisition = StudentRequisition::query()->findOrFail($requisitionId);
        $requisition->documents()->attach($documentIds);
    }

    public function syncWithRequisition(int $requisitionId, array $documentIds): void
    {
        $requisition = StudentRequisition::query()->findOrFail($requisitionId);
        $requisition->documents()->sync($documentIds);
    }
}
