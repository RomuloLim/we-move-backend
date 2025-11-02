<?php

namespace Modules\Operation\Enums;

enum DocumentType: string
{
    case EnrollmentProof = 'enrollment_proof';
    case ResidencyProof = 'residency_proof';
    case IdentificationDocument = 'identification_document';
    case ProfilePicture = 'profile_picture';

    /**
     * Get a human-readable label for the document type.
     */
    public function label(): string
    {
        return match ($this) {
            self::EnrollmentProof => 'Comprovante de Matrícula',
            self::ResidencyProof => 'Comprovante de Endereço',
            self::IdentificationDocument => 'Documento com Foto',
            self::ProfilePicture => 'Foto 3x4',
        };
    }
}
