<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\UnauthorizedException;
use Modules\User\Enums\UserType;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        $hasUserType = $this->has('user_type');

        if ($hasUserType) {
            $typeEnum = UserType::tryFrom($this->input('user_type'));

            return $this->user() && $this->user()->can('updateUserType', [$user, $typeEnum]);
        }

        return $this->user() && $this->user()->can('update', $user);
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'cpf' => ['sometimes', 'string', 'max:14', Rule::unique('users', 'cpf')->ignore($userId)],
            'rg' => ['sometimes', 'string', 'max:20', Rule::unique('users', 'rg')->ignore($userId)],
            'user_type' => ['sometimes', 'string', Rule::in([UserType::Admin, UserType::Driver, UserType::Student])],
            'phone_contact' => ['sometimes', 'string', 'max:20'],
            'profile_picture_url' => ['sometimes', 'string', 'max:2048'],
            'password' => ['sometimes', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'rg.unique' => 'Este RG já está cadastrado.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ];
    }

    protected function failedAuthorization()
    {
        throw new UnauthorizedException('Você não tem permissão para atualizar este usuário.');
    }
}

