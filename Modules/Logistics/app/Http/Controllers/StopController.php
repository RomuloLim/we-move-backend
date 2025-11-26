<?php

namespace Modules\Logistics\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Logistics\Http\Requests\{StopFormRequest, UpdateStopsOrderRequest};
use Modules\Logistics\Http\Resources\StopResource;
use Modules\Logistics\Services\StopServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class StopController extends Controller
{
    public function __construct(protected StopServiceInterface $service) {}

    public function index(Request $request): JsonResponse
    {
        $stops = $this->service->paginate($request->get('per_page', 15));

        return StopResource::collection($stops)->response();
    }

    public function show(int $id): JsonResponse
    {
        $stop = $this->service->find($id);

        if (!$stop) {
            return response()->json(['message' => 'Parada não encontrada.'], StatusCode::HTTP_NOT_FOUND);
        }

        return StopResource::make($stop)->response();
    }

    public function store(StopFormRequest $request): JsonResponse
    {
        $stop = $this->service->create($request->toDto());

        return StopResource::make($stop)
            ->response()
            ->setStatusCode(StatusCode::HTTP_CREATED);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Parada não encontrada.'], StatusCode::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Parada removida com sucesso.']);
    }

    public function updateOrder(UpdateStopsOrderRequest $request): JsonResponse
    {
        $this->service->updateOrder($request->input('stops'));

        return response()->json(['message' => 'Ordem das paradas atualizada com sucesso.']);
    }
}
