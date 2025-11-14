<?php

namespace Modules\Operation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Operation\Http\Requests\InstitutionFormRequest;
use Modules\Operation\Http\Resources\InstitutionResource;
use Modules\Operation\Services\InstitutionServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class InstitutionController extends Controller
{
    public function __construct(protected InstitutionServiceInterface $service) {}

    public function index(Request $request): JsonResponse
    {
        $institutions = $this->service->paginate($request->get('per_page', 15));

        return InstitutionResource::collection($institutions)->response();
    }

    public function show(int $id): JsonResponse
    {
        $institution = $this->service->find($id);

        if (!$institution) {
            return response()->json(['message' => 'Instituição não encontrada.'], StatusCode::HTTP_NOT_FOUND);
        }

        return InstitutionResource::make($institution)->response();
    }

    public function store(InstitutionFormRequest $request): JsonResponse
    {
        $institution = $this->service->create($request->toDto());

        return InstitutionResource::make($institution)
            ->response()
            ->setStatusCode(StatusCode::HTTP_CREATED);
    }

    public function update(InstitutionFormRequest $request, int $id): JsonResponse
    {
        $institution = $this->service->update($id, $request->toDto());

        if (!$institution) {
            return response()->json(['message' => 'Instituição não encontrada.'], StatusCode::HTTP_NOT_FOUND);
        }

        return InstitutionResource::make($institution)->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Instituição não encontrada.'], StatusCode::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Instituição removida com sucesso.']);
    }

}
