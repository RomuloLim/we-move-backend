<?php

namespace Modules\User\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\User\DTOs\SearchParamsDTO;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;

class UserService
{
    /**
     * Registra um novo estudante (rota pública).
     * Apenas estudantes podem ser criados por esta rota.
     */
    public function registerStudent(array $data): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'gender' => $data['gender'] ?? null,
            'rg' => $data['rg'] ?? null,
            'phone_contact' => $data['phone_contact'],
            'profile_picture_url' => $data['profile_picture_url'] ?? null,
            'password' => Hash::make($data['password']),
            'user_type' => UserType::Student->value,
        ]);

        return $user;
    }

    /**
     * Cria um novo usuário por um admin (rota protegida).
     * Admin/SuperAdmin podem criar: Student, Admin, Driver.
     * SuperAdmin não pode ser criado por esta rota.
     */
    public function createUserByAdmin(array $data): User
    {
        $requestedType = UserType::from($data['user_type']);

        // Prevent creating super-admin
        if ($requestedType === UserType::SuperAdmin) {
            throw ValidationException::withMessages(['user_type' => 'Não é permitido criar Super Administrador por essa rota.']);
        }

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'gender' => $data['gender'] ?? null,
            'rg' => $data['rg'] ?? null,
            'phone_contact' => $data['phone_contact'],
            'profile_picture_url' => $data['profile_picture_url'] ?? null,
            'password' => Hash::make($data['password']),
            'user_type' => $requestedType->value,
        ]);

        return $user;
    }

    /**
     * Lista usuários com filtros.
     */
    public function listUsers(SearchParamsDTO $searchParams): LengthAwarePaginator
    {
        $query = User::query()
            ->when($searchParams->search, function (Builder $query, $search) {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('cpf', $search)
                        ->orWhere('rg', $search);
                });
            })
            ->when($searchParams->type, fn (Builder $q) => $q->where('user_type', $searchParams->type->value))
            ->orderBy('created_at', 'desc');

        return $query->paginate($searchParams->perPage);
    }

    /**
     * Atualiza os dados de um usuário.
     */
    public function updateUser(User $user, array $data): User
    {
        $user->fill($data);

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $user->load([
            'studentProfile',
        ]);

        return $user;
    }

    /**
     * Atualiza o tipo de um usuário.
     */
    public function updateUserType(User $user, UserType $newType, User $updatedBy): User
    {
        if ($user->user_type === UserType::SuperAdmin) {
            throw new \Exception('Não é possível alterar o tipo de um Super Administrador.');
        }

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
