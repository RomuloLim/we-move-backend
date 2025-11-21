<?php

namespace Modules\Operation\Enums;

enum ReprovedFieldEnum: string
{
    // Requisition Fields
    case StreetName = 'street_name';
    case HouseNumber = 'house_number';
    case Neighborhood = 'neighborhood';
    case City = 'city';
    case PhoneContact = 'phone_contact';
    case BirthDate = 'birth_date';
    case InstitutionEmail = 'institution_email';
    case InstitutionRegistration = 'institution_registration';
    case InstitutionCourseId = 'institution_course_id';
    case AtuationForm = 'atuation_form';
    case Semester = 'semester';

    // Document Fields (matching DocumentType values)
    case EnrollmentProof = 'enrollment_proof';
    case ResidencyProof = 'residency_proof';
    case IdentificationDocument = 'identification_document';
    case ProfilePicture = 'profile_picture';

    public function label(): string
    {
        return match ($this) {
            self::StreetName => 'Nome da Rua',
            self::HouseNumber => 'Número da Casa',
            self::Neighborhood => 'Bairro',
            self::City => 'Cidade',
            self::PhoneContact => 'Telefone de Contato',
            self::BirthDate => 'Data de Nascimento',
            self::InstitutionEmail => 'Email Institucional',
            self::InstitutionRegistration => 'Matrícula Institucional',
            self::InstitutionCourseId => 'Curso da Instituição',
            self::AtuationForm => 'Forma de Atuação',
            self::Semester => 'Semestre',
            self::EnrollmentProof => 'Comprovante de Matrícula',
            self::ResidencyProof => 'Comprovante de Endereço',
            self::IdentificationDocument => 'Documento com Foto',
            self::ProfilePicture => 'Foto 3x4',
        };
    }
}
