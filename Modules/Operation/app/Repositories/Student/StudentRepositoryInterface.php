<?php

namespace Modules\Operation\Repositories\Student;

use Modules\Operation\Models\Student;

interface StudentRepositoryInterface
{
    public function create(array $data): Student;
    public function update(Student $student, array $data): Student;
    public function findByUserId(int $userId): ?Student;
}
