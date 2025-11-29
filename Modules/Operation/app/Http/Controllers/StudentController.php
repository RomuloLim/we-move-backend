<?php

namespace Modules\Operation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Operation\Http\Resources\StudentResource;
use Modules\Operation\Services\StudentServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class StudentController
{
    public function __construct(
        protected StudentServiceInterface $studentService
    ) {}

    /**
     * Get full data for a specific student.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $authenticatedUser = Auth::user();
            $student = $this->studentService->getStudentFullData($id, $authenticatedUser);

            return StudentResource::make($student)
                ->response()
                ->setStatusCode(StatusCode::HTTP_OK);
        } catch (\Exception $e) {
            $statusCode = str_contains($e->getMessage(), 'autorizado')
                ? StatusCode::HTTP_FORBIDDEN
                : StatusCode::HTTP_NOT_FOUND;

            return response()->json([
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }
}
