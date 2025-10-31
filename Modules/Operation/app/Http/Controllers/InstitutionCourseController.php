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

        $this->service->linkCourse($institutionId, $validated['courses_ids']);

        return response()->json(['message' => 'Cursos vinculados com sucesso.'], StatusCode::HTTP_CREATED);
    }

    public function unlinkCourse(LinkCourseRequest $request, int $institutionId): JsonResponse
    {
        $deleted = $this->service->unlinkCourse($institutionId, $request->input('courses_ids', []));

        if (!$deleted) {
            return response()->json(['message' => 'Vínculo não encontrado.'], StatusCode::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Curso desvinculado com sucesso.']);
    }
}
