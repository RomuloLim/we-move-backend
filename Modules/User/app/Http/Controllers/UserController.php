<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Validation\ValidationException;
use Modules\User\Enums\UserType;
use Modules\User\Http\Requests\{CreateUserByAdminRequest, ListUserRequest, RegisterStudentRequest};
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;
use Modules\User\Services\UserService;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    /**
     * Lista usuários (apenas admins).
     */
    public function index(ListUserRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!$user || !$user->can('viewAny', User::class)) {
            return response()->json([
                'message' => 'Acesso negado. Apenas administradores podem listar usuários.',
            ], 403);
        }

        $dto = $request->toDto();

        $users = $this->userService->listUsers($dto);

        return UserResource::collection($users)
            ->response();
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
        $user = User::findOrFail($userId);

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
