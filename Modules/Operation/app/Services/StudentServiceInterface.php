<?php

namespace Modules\Operation\Services;

use Modules\Operation\Models\Student;
use Modules\User\Models\User;

interface StudentServiceInterface
{
    public function getStudentFullData(int $studentId, ?User $authenticatedUser = null): Student;
}
