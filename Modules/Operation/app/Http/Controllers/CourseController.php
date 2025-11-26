<?php

namespace Modules\Operation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Operation\Http\Requests\CourseFormRequest;
use Modules\Operation\Http\Resources\CourseResource;
use Modules\Operation\Services\CourseServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class CourseController extends Controller
{
    public function __construct(protected CourseServiceInterface $service) {}

    public function index(Request $request): JsonResponse
    {
        $courses = $this->service->paginate($request->get('per_page', 15));

        return CourseResource::collection($courses)->response();
    }

    public function show(int $id): JsonResponse
    {
        $course = $this->service->find($id);

        if (!$course) {
            return response()->json(['message' => 'Curso não encontrado.'], StatusCode::HTTP_NOT_FOUND);
        }

        return CourseResource::make($course)->response();
    }

    public function store(CourseFormRequest $request): JsonResponse
    {
        $course = $this->service->create($request->toDto());

        return CourseResource::make($course)
            ->response()
            ->setStatusCode(StatusCode::HTTP_CREATED);
    }

    public function update(CourseFormRequest $request, int $id): JsonResponse
    {
        $course = $this->service->update($id, $request->toDto());

        if (!$course) {
            return response()->json(['message' => 'Curso não encontrado.'], StatusCode::HTTP_NOT_FOUND);
        }

        return CourseResource::make($course)->response();
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id);

            if (!$deleted) {
                return response()->json(['message' => 'Curso não encontrado.'], StatusCode::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Curso removido com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], StatusCode::HTTP_CONFLICT);
        }
    }
}
