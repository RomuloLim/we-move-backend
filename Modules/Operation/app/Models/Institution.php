<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Operation\Database\Factories\InstitutionFactory;

class Institution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'acronym',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
    ];

    /**
     * Get the courses associated with this institution.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'institution_courses')->withPivot('id');
    }

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): InstitutionFactory
    {
        return InstitutionFactory::new();
    }
}
