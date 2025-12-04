<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Operation\DTOs\StudentRequisitionDto;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};

class StudentRequisitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->user_type->value === 'student';
    }

    public function rules(): array
    {
        return [
            'street_name' => ['required', 'string', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:20'],
            'neighborhood' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'phone_contact' => ['required', 'string', 'max:20'],
            'birth_date' => ['required', 'date', 'before:today'],
            'institution_email' => ['required', 'email', 'max:255'],
            'institution_registration' => ['required', 'string', 'max:100'],
            'semester' => ['required', 'integer', 'min:1'],
            'institution_course_id' => ['required', 'exists:institution_courses,id'],
            'atuation_form' => ['required', Rule::enum(AtuationForm::class)],

            // Documents as file uploads
            'residency_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'identification_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'profile_picture' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'enrollment_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'street_name.required' => 'O endereço é obrigatório.',
            'neighborhood.required' => 'O bairro é obrigatório.',
            'city.required' => 'A cidade é obrigatória.',
            'phone_contact.required' => 'O telefone de contato é obrigatório.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.date' => 'A data de nascimento deve ser uma data válida.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'institution_email.required' => 'O email da universidade é obrigatório.',
            'institution_email.email' => 'O email da universidade deve ser válido.',
            'institution_registration.required' => 'A matrícula é obrigatória.',
            'semester.required' => 'O semestre é obrigatório.',
            'semester.integer' => 'O semestre deve ser um número.',
            'semester.min' => 'O semestre deve ser no mínimo 1.',
            'institution_course_id.required' => 'O curso da instituição é obrigatório.',
            'institution_course_id.exists' => 'O curso da instituição selecionado não existe.',
            'atuation_form.required' => 'A forma de atuação é obrigatória.',
            'residency_proof.required' => 'O comprovante de endereço é obrigatório.',
            'identification_document.required' => 'O documento com foto é obrigatório.',
            'profile_picture.required' => 'A foto 3x4 é obrigatória.',
            'enrollment_proof.required' => 'O comprovante de matrícula é obrigatório.',
            '*.file' => 'O arquivo deve ser válido.',
            '*.mimes' => 'O arquivo deve ser do tipo: :values.',
            '*.max' => 'O arquivo não pode ser maior que :max KB.',
        ];
    }

    public function toDto(): StudentRequisitionDto
    {
        return new StudentRequisitionDto(
            semester: $this->input('semester'),
            status: RequisitionStatus::Pending,
            street_name: $this->input('street_name'),
            house_number: $this->input('house_number'),
            neighborhood: $this->input('neighborhood'),
            city: $this->input('city'),
            phone_contact: $this->input('phone_contact'),
            birth_date: $this->date('birth_date'),
            institution_email: $this->input('institution_email'),
            institution_registration: $this->input('institution_registration'),
            institution_course_id: $this->input('institution_course_id'),
            atuation_form: AtuationForm::from($this->input('atuation_form')),
        );

    }
}
