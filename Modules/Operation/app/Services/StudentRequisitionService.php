<?php

namespace Modules\Operation\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\{DB};
use Illuminate\Support\Str;
use Modules\Operation\DTOs\{DocumentDto, RequisitionListParamsDto, StudentRequisitionDto};
use Modules\Operation\Enums\{DocumentType, RequisitionStatus};
use Modules\Operation\Models\StudentRequisition;
use Modules\Operation\Repositories\Document\DocumentRepositoryInterface;
use Modules\Operation\Repositories\Student\StudentRepositoryInterface;
use Modules\Operation\Repositories\StudentRequisition\StudentRequisitionRepositoryInterface;

class StudentRequisitionService implements StudentRequisitionServiceInterface
{
    public function __construct(
        protected StudentRequisitionRepositoryInterface $requisitionRepository,
        protected DocumentRepositoryInterface $documentRepository,
        protected StudentRepositoryInterface $studentRepository
    ) {}

    public function listOrderingByStatus(?RequisitionListParamsDto $listParams = null): LengthAwarePaginator
    {
        return $this->requisitionRepository->listOrderingByStatus($listParams);
    }

    public function find(int $id): StudentRequisition
    {
        $data = $this->requisitionRepository->findOrFail($id);

        return $data;
    }

    public function hasApprovedRequisition(int $studentId): bool
    {
        return $this->requisitionRepository->hasApprovedRequisition($studentId);
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

            $this->syncStudent($studentId, $requisitionData);

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

    protected function syncStudent(int $studentId, StudentRequisitionDto $requisitionData): void
    {
        $studentData = [
            'user_id' => $studentId,
            'institution_course_id' => $requisitionData->institution_course_id,
            'city_of_origin' => $requisitionData->city,
            'status' => $requisitionData->status->value,
            'qrcode_token' => Str::uuid()->toString(),
        ];

        $student = $this->studentRepository->findByUserId($studentId);

        if ($student) {
            $this->studentRepository->update($student, $studentData);
        } else {
            $this->studentRepository->create($studentData);
        }
    }

    private function getPendingRequisition(int $studentId): ?StudentRequisition
    {
        return $this->requisitionRepository->getPendingRequisition($studentId);
    }

    public function approve(int $id): StudentRequisition
    {
        $requisition = $this->requisitionRepository->find($id);

        $updatedRequisition = $this->requisitionRepository->update($requisition, new StudentRequisitionDto(
            student_id: $requisition->student_id,
            semester: $requisition->semester,
            status: RequisitionStatus::Approved
        ));

        $this->syncStudent($requisition->student_id, new StudentRequisitionDto(
            student_id: $requisition->student_id,
            semester: $requisition->semester,
            status: RequisitionStatus::Approved,
            city: $requisition->city,
            institution_course_id: $requisition->institution_course_id
        ));

        return $updatedRequisition;
    }

    public function reprove(int $id, array $reprovedFields, ?string $reason): StudentRequisition
    {
        $requisition = $this->requisitionRepository->find($id);

        $updatedRequisition = $this->requisitionRepository->update($requisition, new StudentRequisitionDto(
            student_id: $requisition->student_id,
            semester: $requisition->semester,
            status: RequisitionStatus::Reproved,
            deny_reason: $reason,
            reproved_fields: $reprovedFields
        ));

        return $updatedRequisition;
    }
}
