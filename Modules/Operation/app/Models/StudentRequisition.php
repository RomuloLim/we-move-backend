<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Modules\Operation\Database\Factories\StudentRequisitionFactory;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};
use Modules\User\Models\User;

class StudentRequisition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'semester',
        'protocol',
        'status',
        'street_name',
        'house_number',
        'neighborhood',
        'city',
        'phone_contact',
        'birth_date',
        'institution_email',
        'institution_registration',
        'institution_course_id',
        'semester',
        'protocol',
        'atuation_form',
        'deny_reason',
        'reproved_fields',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => RequisitionStatus::class,
            'atuation_form' => AtuationForm::class,
            'birth_date' => 'date',
            'reproved_fields' => 'array',
        ];
    }

    /**
     * Get the student that owns the requisition.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the institution course associated with the requisition.
     */
    public function institutionCourse(): BelongsTo
    {
        return $this->belongsTo(InstitutionCourse::class);
    }

    /**
     * Get the documents associated with the requisition.
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(
            Document::class,
            'requisition_documents',
            'requisition_id',
            'document_id'
        );
    }

    /**
     * Check if the requisition is pending.
     */
    public function isPending(): bool
    {
        return $this->status === RequisitionStatus::Pending;
    }

    /**
     * Check if the requisition is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === RequisitionStatus::Approved;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): StudentRequisitionFactory
    {
        return StudentRequisitionFactory::new();
    }
}
