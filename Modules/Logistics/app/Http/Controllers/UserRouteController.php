<?php

namespace Modules\Logistics\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Logistics\Http\Requests\{LinkRoutesToUserRequest, UnlinkRoutesFromUserRequest};
use Modules\Logistics\Http\Resources\UserRouteResource;
use Modules\Logistics\Services\UserRouteServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class UserRouteController extends Controller
{
    public function __construct(protected UserRouteServiceInterface $service) {}

    public function index(Request $request, int $userId): JsonResponse
    {
        $routes = $this->service->getRoutesByUserId($userId, $request->get('per_page', 15));

        return UserRouteResource::collection($routes)->response();
    }

    public function getAllOrderedByUser(Request $request, int $userId): JsonResponse
    {
        $routes = $this->service->getAllRoutesOrderedByUser($userId, $request->get('per_page', 15));

        return UserRouteResource::collection($routes)->response();
    }

    public function linkRoutes(LinkRoutesToUserRequest $request): JsonResponse
    {
        $linked = $this->service->linkRoutesToUser($request->toDto());

        if (!$linked) {
            return response()->json(
                ['message' => 'Erro ao vincular rotas ao usuário.'],
                StatusCode::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            ['message' => 'Rotas vinculadas com sucesso.'],
            StatusCode::HTTP_OK
        );
    }

    public function unlinkRoutes(UnlinkRoutesFromUserRequest $request): JsonResponse
    {
        $unlinked = $this->service->unlinkRoutesFromUser($request->toDto());

        if (!$unlinked) {
            return response()->json(
                ['message' => 'Erro ao desvincular rotas do usuário.'],
                StatusCode::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            ['message' => 'Rotas desvinculadas com sucesso.'],
            StatusCode::HTTP_OK
        );
    }
}
