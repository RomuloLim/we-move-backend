<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Validation\ValidationException;
use Modules\User\Enums\UserType;
use Modules\User\Http\Requests\{CreateUserByAdminRequest, RegisterStudentRequest};
use Modules\User\Http\Resources\UserResource;
use Modules\User\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    /**
     * Lista usuários (apenas admins).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user || !$user->can('viewAny', \Modules\User\Models\User::class)) {
            return response()->json([
                'message' => 'Acesso negado. Apenas administradores podem listar usuários.',
            ], 403);
        }

        $type = $request->input('type') ? UserType::from($request->input('type')) : null;
        $perPage = $request->input('per_page', 15);

        $users = $this->userService->listUsers($type, $perPage);

        return response()->json([
            'data' => UserResource::collection($users->items()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Registra um novo estudante (rota pública).
     */
    public function register(RegisterStudentRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $this->userService->registerStudent($data);

            return response()->json([
                'message' => 'Estudante registrado com sucesso.',
                'data' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Cria um novo usuário (apenas admins para tipos admin/driver).
     */
    public function store(CreateUserByAdminRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $this->userService->createUserByAdmin($data);

            return response()->json([
                'message' => 'Usuário criado com sucesso.',
                'data' => new UserResource($user),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Atualiza o tipo de um usuário.
     */
    public function updateType(Request $request, int $userId): JsonResponse
    {
        $currentUser = $request->user();
        $user = \Modules\User\Models\User::findOrFail($userId);

        $request->validate([
            'user_type' => ['required', 'string', 'in:admin,student,driver'],
        ]);

        $newType = UserType::from($request->input('user_type'));

        if (!$currentUser || !$currentUser->can('updateUserType', [$user, $newType])) {
            return response()->json([
                'message' => 'Acesso negado.',
            ], 403);
        }

        $updatedUser = $this->userService->updateUserType($user, $newType, $currentUser);

        return response()->json([
            'message' => 'Tipo de usuário atualizado com sucesso.',
            'data' => new UserResource($updatedUser),
        ]);
    }
}
