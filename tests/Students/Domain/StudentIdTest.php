<?php

declare(strict_types=1);

namespace Tests\Students\Domain;

use Lms\Students\Domain\StudentId;
use PHPUnit\Framework\TestCase;

final class StudentIdTest extends TestCase
{
    public function test_creates_student_id_with_valid_string(): void
    {
        $id = new StudentId('student-123');

        $this->assertEquals('student-123', $id->toString());
    }

    public function test_throws_exception_when_id_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Student ID cannot be empty.');

        new StudentId('');
    }

    public function test_throws_exception_when_id_is_only_whitespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Student ID cannot be empty.');

        new StudentId('   ');
    }

    public function test_equals_returns_true_for_same_id(): void
    {
        $id1 = new StudentId('student-123');
        $id2 = new StudentId('student-123');

        $this->assertTrue($id1->equals($id2));
    }

    public function test_equals_returns_false_for_different_id(): void
    {
        $id1 = new StudentId('student-123');
        $id2 = new StudentId('student-456');

        $this->assertFalse($id1->equals($id2));
    }

    public function test_converts_to_string(): void
    {
        $id = new StudentId('student-123');

        $this->assertEquals('student-123', (string) $id);
    }
}
