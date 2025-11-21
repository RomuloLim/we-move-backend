<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Operation\Database\Factories\StudentFactory;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'institution_course_id',
        'city_of_origin',
        'status',
        'qrcode_token',
    ];

    protected static function newFactory(): StudentFactory
    {
        return StudentFactory::new();
    }
}
