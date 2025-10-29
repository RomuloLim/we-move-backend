<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;

class UserService
{
    /**
     * Cria um novo usuário aplicando regras de autorização:
     * - Usuários não autenticados podem apenas registrar `student`.
     * - Motoristas não podem criar usuários.
     * - Apenas Admin/SuperAdmin podem criar `admin` ou `driver`.
     * - Não é permitido criar `super-admin` por essa rota.
     */
    public function createUser(array $data): User
    {
        $creator = Auth::user();

        $requestedType = UserType::from($data['user_type']);

        // Prevent creating super-admin here
        if ($requestedType === UserType::SuperAdmin) {
            throw ValidationException::withMessages(['user_type' => 'Não é permitido criar Super Administrador por essa rota.']);
        }

        // Guests can only create students
        if (!$creator) {
            if (!$requestedType->canBeCreatedPublicly()) {
                throw ValidationException::withMessages(['user_type' => 'Visitantes só podem se registrar como Estudante.']);
            }
        } else {
            if ($creator->user_type === UserType::Driver) {
                throw ValidationException::withMessages(['authorization' => 'Motoristas não podem criar usuários.']);
            }

            if (in_array($requestedType, UserType::adminOnlyTypes(), true) && ! $creator->canCreateAdminUsers()) {
                throw ValidationException::withMessages(['user_type' => 'Apenas administradores podem criar usuários desse tipo.']);
            }
        }

        $user = User::query()->create([
            ...$data,
            'password' => Hash::make($data['password']),
            'user_type' => $requestedType->value,
        ]);

        return $user;
    }


    /**
     * Lista usuários com filtros.
     */
    public function listUsers(?UserType $type = null, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = User::query();

        if ($type) {
            $query->where('user_type', $type->value);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Atualiza o tipo de um usuário.
     */
    public function updateUserType(User $user, UserType $newType, User $updatedBy): User
    {
        // Super-admin não pode ter seu tipo alterado
        if ($user->user_type === UserType::SuperAdmin) {
            throw new \Exception('Não é possível alterar o tipo de um Super Administrador.');
        }

        // Não é possível promover alguém para super-admin
        if ($newType === UserType::SuperAdmin) {
            throw new \Exception('Não é possível promover usuário para Super Administrador.');
        }

        $user->user_type = $newType;
        $user->save();

        logger('Tipo de usuário alterado', [
            'user_id' => $user->id,
            'old_type' => $user->getOriginal('user_type'),
            'new_type' => $newType->value,
            'updated_by' => $updatedBy->id,
        ]);

        return $user;
    }
}
