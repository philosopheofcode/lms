<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\CourseId;
use PHPUnit\Framework\TestCase;

final class CourseIdTest extends TestCase
{
    public function test_it_can_be_created_with_valid_id(): void
    {
        $courseId = new CourseId('course-123');

        $this->assertSame('course-123', $courseId->value());
    }

    public function test_it_throws_exception_when_id_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course ID cannot be empty.');

        new CourseId('');
    }

    public function test_it_throws_exception_when_id_is_whitespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course ID cannot be empty.');

        new CourseId('   ');
    }

    public function test_it_can_compare_equality_with_same_value(): void
    {
        $courseId1 = new CourseId('course-123');
        $courseId2 = new CourseId('course-123');

        $this->assertTrue($courseId1->equals($courseId2));
    }

    public function test_it_can_compare_equality_with_different_value(): void
    {
        $courseId1 = new CourseId('course-123');
        $courseId2 = new CourseId('course-456');

        $this->assertFalse($courseId1->equals($courseId2));
    }

    public function test_it_can_be_converted_to_string(): void
    {
        $courseId = new CourseId('course-123');

        $this->assertSame('course-123', (string) $courseId);
    }
}
