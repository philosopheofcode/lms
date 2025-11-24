<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\Course;
use PHPUnit\Framework\TestCase;

final class CourseTest extends TestCase
{
    public function test_it_can_be_created_with_valid_data(): void
    {
        $course = new Course('1', 'Introduction to PHP');

        $this->assertSame('Introduction to PHP', $course->name);
    }

    public function test_it_throws_exception_when_name_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course name cannot be empty.');

        new Course('1', '');
    }

    public function test_it_can_be_converted_to_string(): void
    {
        $name = 'PHP 101 Course';
        $course = new Course('1', $name);

        $this->assertSame($name, (string) $course);
    }
}
