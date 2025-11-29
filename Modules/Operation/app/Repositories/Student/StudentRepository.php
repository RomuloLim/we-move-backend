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

    public function findById(int $id): ?Student
    {
        return Student::find($id);
    }

    public function findByIdWithFullData(int $id): ?Student
    {
        return Student::with([
            'user',
            'requisitions' => function ($query) {
                $query->latest()->limit(1);
            },
            'requisitions.institutionCourse.institution',
            'requisitions.institutionCourse.course',
        ])->find($id);
    }
}
