<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Operation\Enums\DocumentType;
use Modules\User\Models\User;

class Document extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'type',
        'file_url',
        'uploaded_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'uploaded_at' => 'datetime',
        ];
    }

    /**
     * Get the student that owns the document.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the requisitions associated with this document.
     */
    public function requisitions(): BelongsToMany
    {
        return $this->belongsToMany(
            StudentRequisition::class,
            'requisition_documents',
            'document_id',
            'requisition_id'
        );
    }
}
