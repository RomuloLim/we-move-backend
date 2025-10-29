<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\User\Enums\UserType;

class CreateUserByAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'string', 'max:14', 'unique:users,cpf'],
            'rg' => ['required', 'string', 'max:12', 'unique:users,rg'],
            'phone_contact' => ['required', 'string', 'max:15'],
            'profile_picture_url' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'user_type' => ['required', new Enum(UserType::class)],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está sendo usado por outro usuário.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está sendo usado por outro usuário.',
            'rg.required' => 'O campo RG é obrigatório.',
            'rg.unique' => 'Este RG já está sendo usado por outro usuário.',
            'phone_contact.required' => 'O campo telefone é obrigatório.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'user_type.required' => 'O campo tipo de usuário é obrigatório.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     * Only admin users can create new users, with restrictions.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        // Check if user can create users in general
        if (! $user->can('create', \Modules\User\Models\User::class)) {
            return false;
        }

        // Check if user can create this specific user type
        $requestedType = UserType::tryFrom($this->input('user_type'));

        if (! $requestedType) {
            return false;
        }

        return $user->can('createUserType', [\Modules\User\Models\User::class, $requestedType]);
    }
}

