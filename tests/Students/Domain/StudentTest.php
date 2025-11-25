<?php

declare(strict_types=1);

namespace Tests\Students\Domain;

use Lms\Students\Domain\Student;
use Lms\Students\Domain\StudentId;
use Lms\Students\Domain\StudentName;
use PHPUnit\Framework\TestCase;

final class StudentTest extends TestCase
{
    public function test_creates_student_with_id_and_name(): void
    {
        $studentId = new StudentId('student-123');
        $studentName = new StudentName('Emma Watson');

        $student = new Student($studentId, $studentName);

        $this->assertTrue($student->id()->equals($studentId));
        $this->assertEquals('Emma Watson', $student->name()->toString());
    }

    public function test_student_converts_to_string_using_name(): void
    {
        $student = new Student(
            new StudentId('student-123'),
            new StudentName('Emma Watson')
        );

        $this->assertEquals('Emma Watson', (string) $student);
    }
}
