<?php

namespace Modules\Operation\Repositories\Student;

use Modules\Operation\Models\Student;

class StudentRepository implements StudentRepositoryInterface
{
    public function create(array $data): Student
    {
        return Student::create($data);
    }

    public function update(Student $student, array $data): Student
    {
        $student->update($data);

        return $student;
    }

    public function findByUserId(int $userId): ?Student
    {
        return Student::where('user_id', $userId)->first();
    }
}
