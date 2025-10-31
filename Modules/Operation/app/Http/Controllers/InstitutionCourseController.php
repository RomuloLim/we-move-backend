<?php

namespace Modules\Operation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Operation\Http\Requests\LinkCourseRequest;
use Modules\Operation\Services\InstitutionCourseServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class InstitutionCourseController extends Controller
{
    public function __construct(protected InstitutionCourseServiceInterface $service) {}

    public function linkCourse(LinkCourseRequest $request, int $institutionId): JsonResponse
    {
        $validated = $request->validated();
        $link = $this->service->linkCourse($institutionId, $validated['course_id']);

        if (!$link) {
            return response()->json(['message' => 'Erro ao vincular curso.'], StatusCode::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => 'Curso vinculado com sucesso.'], StatusCode::HTTP_CREATED);
    }

    public function unlinkCourse(int $institutionId, int $courseId): JsonResponse
    {
        $deleted = $this->service->unlinkCourse($institutionId, $courseId);

        if (!$deleted) {
            return response()->json(['message' => 'Vínculo não encontrado.'], StatusCode::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Curso desvinculado com sucesso.']);
    }
}
