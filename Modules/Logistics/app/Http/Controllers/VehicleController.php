<?php

namespace Modules\Logistics\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse};
use Modules\Logistics\Http\Requests\{VehicleFormRequest, VehicleIndexRequest};
use Modules\Logistics\Http\Resources\VehicleResource;
use Modules\Logistics\Services\VehicleServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class VehicleController extends Controller
{
    public function __construct(protected VehicleServiceInterface $service) {}

    public function index(VehicleIndexRequest $request): JsonResponse
    {
        $vehicles = $this->service->paginate($request->get('search'), $request->get('per_page', 15));

        return VehicleResource::collection($vehicles)->response();
    }

    public function show(int $id): JsonResponse
    {
        $vehicle = $this->service->find($id);

        if (!$vehicle) {
            return response()->json(['message' => 'Veículo não encontrado.'], StatusCode::HTTP_NOT_FOUND);
        }

        return VehicleResource::make($vehicle)->response();
    }

    public function store(VehicleFormRequest $request): JsonResponse
    {
        $vehicle = $this->service->create($request->toDto());

        return VehicleResource::make($vehicle)
            ->response()
            ->setStatusCode(StatusCode::HTTP_CREATED);
    }

    public function update(VehicleFormRequest $request, int $id): JsonResponse
    {
        $vehicle = $this->service->update($id, $request->toDto());

        if (!$vehicle) {
            return response()->json(['message' => 'Veículo não encontrado.'], StatusCode::HTTP_NOT_FOUND);
        }

        return VehicleResource::make($vehicle)->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Veículo não encontrado.'], StatusCode::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Veículo removido com sucesso.']);
    }
}
