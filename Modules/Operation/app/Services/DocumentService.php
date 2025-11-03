<?php

namespace Modules\Operation\Services;

use Illuminate\Support\Collection;
use Modules\Operation\DTOs\DocumentDto;
use Modules\Operation\Models\Document;
use Modules\Operation\Repositories\Document\DocumentRepositoryInterface;

class DocumentService implements DocumentServiceInterface
{
    public function __construct(protected DocumentRepositoryInterface $repository) {}

    public function find(int $id): ?Document
    {
        return $this->repository->find($id);
    }

    public function getByStudentId(int $studentId): Collection
    {
        return $this->repository->getByStudentId($studentId);
    }

    public function create(DocumentDto $data): Document
    {
        return $this->repository->create($data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
