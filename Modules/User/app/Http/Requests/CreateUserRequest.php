<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\User\Enums\UserType;

class CreateUserRequest extends FormRequest
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
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'user_type.required' => 'O campo tipo de usuário é obrigatório.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        $inputType = $this->input('user_type');

        $requestedType = $inputType ? UserType::from($inputType) : UserType::Student;

        if (!$user) {
            return $requestedType->canBeCreatedPublicly();
        }

        // Drivers cannot create users
        if ($user->user_type === UserType::Driver) {
            return false;
        }

        // Admins and SuperAdmins can create admin/driver types
        if (in_array($requestedType, UserType::adminOnlyTypes(), true)) {
            return $user?->canCreateAdminUsers() ?? false;
        }

        // Students or other types may be created by authenticated non-driver users
        return true;
    }
}
