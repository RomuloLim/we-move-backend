<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Auth\Classes\Services\AuthService;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Resources\UserResource;
use Modules\User\Enums\UserType;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * Realize the user login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $authData = $this->authService->login($credentials);

            return response()->json([
                'message' => 'Login realizado com sucesso.',
                'data' => [
                    'user' => new UserResource($authData['user']),
                    'token' => $authData['token'],
                    'token_type' => $authData['token_type'],
                ],
            ], 200);
        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'Erro de autenticação.',
                'errors' => [$e->getMessage()],
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Realiza o logout do usuário.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $success = $this->authService->logout();

            if (!$success) {
                return response()->json([
                    'message' => 'Usuário não autenticado.',
                ], 401);
            }

            return response()->json([
                'message' => 'Logout realizado com sucesso.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Realiza o logout de todos os dispositivos.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $success = $this->authService->logoutAll();

            if (!$success) {
                return response()->json([
                    'message' => 'Usuário não autenticado.',
                ], 401);
            }

            return response()->json([
                'message' => 'Logout de todos os dispositivos realizado com sucesso.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Returns the authenticated user's information.
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuário não autenticado.',
                ], 401);
            }

            $relationToLoad = match ($user->user_type) {
                UserType::Student => 'studentProfile',
                // UserType::Driver => 'driverProfile',
                default => null,
            };

            if ($relationToLoad) {
                $user->load($relationToLoad);
            }

            $user->load('studentProfile');

            return response()->json([
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }
}
