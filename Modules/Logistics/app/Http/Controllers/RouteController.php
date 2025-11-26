<?php

namespace Modules\Logistics\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Logistics\Http\Requests\RouteFormRequest;
use Modules\Logistics\Http\Resources\RouteResource;
use Modules\Logistics\Services\RouteServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class RouteController extends Controller
{
    public function __construct(protected RouteServiceInterface $service) {}

    public function index(Request $request): JsonResponse
    {
        $routes = $this->service->paginate($request->get('per_page', 15));

        return RouteResource::collection($routes)->response();
    }

    public function show(int $id): JsonResponse
    {
        $route = $this->service->find($id);

        if (!$route) {
            return response()->json(['message' => 'Rota não encontrada.'], StatusCode::HTTP_NOT_FOUND);
        }

        return RouteResource::make($route)->response();
    }

    public function store(RouteFormRequest $request): JsonResponse
    {
        $route = $this->service->create($request->toDto());

        return RouteResource::make($route)
            ->response()
            ->setStatusCode(StatusCode::HTTP_CREATED);
    }

    public function update(RouteFormRequest $request, int $id): JsonResponse
    {
        $route = $this->service->update($id, $request->toDto());

        if (!$route) {
            return response()->json(['message' => 'Rota não encontrada.'], StatusCode::HTTP_NOT_FOUND);
        }

        return RouteResource::make($route)->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Rota não encontrada.'], StatusCode::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Rota removida com sucesso.']);
    }
}
