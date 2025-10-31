<?php

namespace Modules\Auth\Classes\Services;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\{Auth, Hash};
use Modules\User\Models\User;

class AuthService
{
    public function __construct() {}

    /**
     * Realize the user login and return authentication data.
     */
    public function login(array $credentials): array
    {
        $user = User::query()->where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Credenciais inválidas.');
        }

        // Remove existing tokens (optional - only one active session)
        $user->tokens()->delete();

        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Makes user registration and returns authentication data.
     */
    public function register(array $data, ?User $createdBy = null): array
    {
        // Create the user
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'] ?? 'student',
        ]);

        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Removes the current token of the authenticated user.
     */
    public function logout(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Remove only the current token
        $user->currentAccessToken()->delete();

        return true;
    }

    /**
     * Removes all tokens of the authenticated user.
     */
    public function logoutAll(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Remove all tokens
        $user->tokens()->delete();

        return true;
    }
}
