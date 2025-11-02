<?php

namespace Modules\Operation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{DB, Storage};
use Modules\Operation\Enums\{DocumentType, RequisitionStatus};
use Modules\Operation\Http\Requests\StudentRequisitionRequest;
use Modules\Operation\Models\{Document, StudentRequisition};
use Symfony\Component\HttpFoundation\Response as StatusCode;

class StudentRequisitionController extends Controller
{
    /**
     * Store a newly created requisition in storage.
     */
    public function store(StudentRequisitionRequest $request): JsonResponse
    {
        $user = $request->user();

        // Check if student has an approved requisition
        $hasApproved = StudentRequisition::where('student_id', $user->id)
            ->where('status', RequisitionStatus::Approved)
            ->exists();

        if ($hasApproved) {
            return response()->json([
                'message' => 'Você já possui uma solicitação aprovada e não pode enviar outra.',
            ], StatusCode::HTTP_FORBIDDEN);
        }

        // Check if student has a pending requisition
        $pendingRequisition = StudentRequisition::where('student_id', $user->id)
            ->where('status', RequisitionStatus::Pending)
            ->first();

        try {
            DB::beginTransaction();

            // Store documents
            $documents = [];
            $documentTypes = [
                'residency_proof' => DocumentType::ResidencyProof,
                'identification_document' => DocumentType::IdentificationDocument,
                'profile_picture' => DocumentType::ProfilePicture,
                'enrollment_proof' => DocumentType::EnrollmentProof,
            ];

            foreach ($documentTypes as $fieldName => $documentType) {
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $path = $file->store('documents/' . $user->id, 'public');

                    $document = Document::create([
                        'student_id' => $user->id,
                        'type' => $documentType,
                        'file_url' => $path,
                        'uploaded_at' => now(),
                    ]);

                    $documents[] = $document->id;
                }
            }

            $requisitionData = [
                'student_id' => $user->id,
                'semester' => $request->semester,
                'status' => RequisitionStatus::Pending,
                'street_name' => $request->street_name,
                'house_number' => $request->house_number,
                'neighborhood' => $request->neighborhood,
                'city' => $request->city,
                'phone_contact' => $request->phone_contact,
                'birth_date' => $request->birth_date,
                'institution_email' => $request->institution_email,
                'institution_registration' => $request->institution_registration,
                'institution_id' => $request->institution_id,
                'course_id' => $request->course_id,
                'atuation_form' => $request->atuation_form,
            ];

            if ($pendingRequisition) {
                // Update existing pending requisition
                $requisition = $pendingRequisition;
                $requisition->update($requisitionData);

                // Update documents
                $requisition->documents()->sync($documents);
            } else {
                // Create new requisition with protocol
                $requisitionData['protocol'] = $this->generateProtocol();
                $requisition = StudentRequisition::create($requisitionData);

                // Attach documents
                $requisition->documents()->attach($documents);
            }

            DB::commit();

            return response()->json([
                'message' => 'Solicitação enviada com sucesso.',
                'data' => [
                    'protocol' => $requisition->protocol,
                    'status' => $requisition->status->value,
                ],
            ], StatusCode::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao processar solicitação.',
                'error' => $e->getMessage(),
            ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate a unique protocol for the requisition.
     */
    private function generateProtocol(): string
    {
        do {
            $protocol = 'REQ-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (StudentRequisition::where('protocol', $protocol)->exists());

        return $protocol;
    }
}
