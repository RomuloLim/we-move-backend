<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStudentRequest extends FormRequest
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
            'rg' => ['nullable', 'string', 'max:12', 'unique:users,rg'],
            'gender' => ['nullable', 'string', 'in:M,F,O'],
            'phone_contact' => ['required', 'string', 'max:15'],
            'profile_picture_url' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
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
        ];
    }
}
