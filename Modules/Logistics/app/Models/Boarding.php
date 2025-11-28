<?php

namespace Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Logistics\Database\Factories\BoardingFactory;
use Modules\Operation\Models\Student;

class Boarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'student_id',
        'boarding_timestamp',
        'landed_at',
        'stop_id',
    ];

    protected static function newFactory(): BoardingFactory
    {
        return BoardingFactory::new();
    }

    protected function casts(): array
    {
        return [
            'boarding_timestamp' => 'datetime',
            'landed_at' => 'datetime',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }
}
