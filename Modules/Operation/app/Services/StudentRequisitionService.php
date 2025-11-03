<?php

namespace Modules\Operation\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB};
use Modules\Operation\DTOs\{DocumentDto, StudentRequisitionDto};
use Modules\Operation\Enums\DocumentType;
use Modules\Operation\Models\StudentRequisition;
use Modules\Operation\Repositories\Document\DocumentRepositoryInterface;
use Modules\Operation\Repositories\StudentRequisition\StudentRequisitionRepositoryInterface;

class StudentRequisitionService implements StudentRequisitionServiceInterface
{
    public function __construct(
        protected StudentRequisitionRepositoryInterface $requisitionRepository,
        protected DocumentRepositoryInterface $documentRepository
    ) {}

    public function hasApprovedRequisition(int $studentId): bool
    {
        return $this->requisitionRepository->hasApprovedRequisition($studentId);
    }

    public function getPendingRequisition(int $studentId): ?StudentRequisition
    {
        return $this->requisitionRepository->getPendingRequisition($studentId);
    }

    public function createOrUpdate(
        int $studentId,
        StudentRequisitionDto $requisitionData,
        array $files
    ): array {
        return DB::transaction(function () use ($studentId, $requisitionData, $files): array {
            $documentIds = $this->processDocuments($studentId, $files);

            $pendingRequisition = $this->getPendingRequisition($studentId);

            if ($pendingRequisition) {
                $requisition = $this->requisitionRepository->update(
                    $pendingRequisition,
                    $requisitionData
                );

                $this->documentRepository->syncWithRequisition(
                    $requisition->id,
                    $documentIds
                );
            } else {
                $requisitionData->protocol = $this->generateUniqueProtocol();

                $requisition = $this->requisitionRepository->create($requisitionData);

                $this->documentRepository->attachToRequisition(
                    $requisition->id,
                    $documentIds
                );
            }

            return [
                'protocol' => $requisition->protocol,
                'status' => $requisition->status->value,
            ];
        });
    }

    public function generateUniqueProtocol(): string
    {
        do {
            $protocol = 'REQ-' . date('Y') . '-' . strtoupper(substr(md5(uniqid((string) rand(), true)), 0, 8));
        } while ($this->requisitionRepository->protocolExists($protocol));

        return $protocol;
    }

    /**
     * Process and store documents.
     *
     * @param  array<string, UploadedFile>  $files
     * @return array<int>
     */
    protected function processDocuments(int $studentId, array $files): array
    {
        $documentIds = [];
        $documentTypes = DocumentType::getFormFieldMapping();

        foreach ($documentTypes as $fieldName => $documentType) {
            if (isset($files[$fieldName])) {
                $file = $files[$fieldName];
                $path = $file->store('documents/' . $studentId, 'public');

                $document = $this->documentRepository->create(
                    new DocumentDto(
                        student_id: $studentId,
                        type: $documentType,
                        file_url: $path,
                        uploaded_at: now()
                    )
                );

                $documentIds[] = $document->id;
            }
        }

        return $documentIds;
    }
}
