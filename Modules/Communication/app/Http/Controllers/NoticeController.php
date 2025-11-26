<?php

namespace Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Communication\Http\Requests\{ListNoticeRequest, StoreNoticeRequest};
use Modules\Communication\Http\Resources\NoticeResource;
use Modules\Communication\Services\NoticeServiceInterface;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class NoticeController extends Controller
{
    public function __construct(protected NoticeServiceInterface $service) {}

    public function index(ListNoticeRequest $request): JsonResponse
    {
        $routeIds = $request->input('route_ids');
        $perPage = $request->input('per_page', 5);

        $notices = $this->service->list($routeIds, $perPage);

        return NoticeResource::collection($notices)->response();
    }

    public function unread(ListNoticeRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $perPage = $request->input('per_page', 5);

        $notices = $this->service->getUnreadForUser($userId, $perPage);

        return NoticeResource::collection($notices)->response();
    }

    public function store(StoreNoticeRequest $request): JsonResponse
    {
        $authorUserId = Auth::id();

        $notices = $this->service->createNotices($authorUserId, $request->validated());

        return response()->json([
            'message' => 'Aviso(s) criado(s) com sucesso.',
            'data' => NoticeResource::collection($notices),
        ], StatusCode::HTTP_CREATED);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Aviso não encontrado.'], StatusCode::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Aviso removido com sucesso.']);
    }
}
