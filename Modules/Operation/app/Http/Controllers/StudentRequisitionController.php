<?php

namespace Modules\Operation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Operation\Enums\{DocumentType};
use Modules\Operation\Http\Requests\{RequisitionListRequest, StudentRequisitionRequest, ReproveRequisitionRequest};
use Modules\Operation\Http\Resources\StudentRequisitionResource;
use Modules\Operation\Services\StudentRequisitionServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class StudentRequisitionController extends Controller
{
    public function __construct(
        protected StudentRequisitionServiceInterface $requisitionService
    ) {}

    public function index(RequisitionListRequest $request): JsonResponse
    {
        $dto = $request->toDto();

        $data = $this->requisitionService->listOrderingByStatus($dto);

        return StudentRequisitionResource::collection($data)
            ->response()
            ->setStatusCode(StatusCode::HTTP_OK);
    }

    /**
     * Store a newly created requisition in storage.
     */
    public function store(StudentRequisitionRequest $request): JsonResponse
    {
        $user = $request->user();

        // Check if student has an approved requisition
        if ($this->requisitionService->hasApprovedRequisition($user->id)) {
            return response()->json([
                'message' => 'Você já possui uma solicitação aprovada e não pode enviar outra.',
            ], StatusCode::HTTP_FORBIDDEN);
        }

        try {
            $requisitionDto = $request->toDto();

            // Collect files
            $files = [];
            $documentTypes = DocumentType::getFormFieldMapping();

            foreach ($documentTypes as $fieldName => $documentType) {
                if ($request->hasFile($fieldName)) {
                    $files[$fieldName] = $request->file($fieldName);
                }
            }

            // Create or update requisition
        $result = $this->requisitionService->createOrUpdate(
                $user->id,
                $requisitionDto,
                $files
            );

            return response()->json([
                'message' => 'Solicitação enviada com sucesso.',
                'data' => $result,
            ], StatusCode::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('Error processing student requisition', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Erro ao processar solicitação. Por favor, tente novamente mais tarde.',
            ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function approve(int $id): JsonResponse
    {
        try {
            $requisition = $this->requisitionService->approve($id);

            return response()->json([
                'message' => 'Solicitação aprovada com sucesso.',
                'data' => new StudentRequisitionResource($requisition),
            ], StatusCode::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error approving requisition', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao aprovar solicitação.',
            ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reprove(ReproveRequisitionRequest $request, int $id): JsonResponse
    {
        try {
            $requisition = $this->requisitionService->reprove(
                $id,
                $request->validated('reproved_fields'),
                $request->validated('deny_reason')
            );

            return response()->json([
                'message' => 'Solicitação reprovada com sucesso.',
                'data' => new StudentRequisitionResource($requisition),
            ], StatusCode::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error reproving requisition', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao reprovar solicitação.',
            ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
