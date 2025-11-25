<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\LessonName;
use PHPUnit\Framework\TestCase;

final class LessonNameTest extends TestCase
{
    public function test_it_can_be_created_with_valid_name(): void
    {
        $name = new LessonName('Introduction to PHP');

        $this->assertSame('Introduction to PHP', $name->value());
    }

    public function test_it_throws_exception_when_name_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Lesson name cannot be empty.');

        new LessonName('');
    }

    public function test_it_throws_exception_when_name_is_whitespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Lesson name cannot be empty.');

        new LessonName('   ');
    }

    public function test_it_trims_whitespace_from_name(): void
    {
        $name = new LessonName('  Introduction to PHP  ');

        $this->assertSame('Introduction to PHP', $name->value());
    }

    public function test_it_can_be_converted_to_string(): void
    {
        $name = new LessonName('Introduction to PHP');

        $this->assertSame('Introduction to PHP', (string) $name);
    }
}
