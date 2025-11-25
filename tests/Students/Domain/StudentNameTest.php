<?php

declare(strict_types=1);

namespace Tests\Students\Domain;

use Lms\Students\Domain\StudentName;
use PHPUnit\Framework\TestCase;

final class StudentNameTest extends TestCase
{
    public function test_creates_student_name_with_valid_string(): void
    {
        $name = new StudentName('Emma Watson');

        $this->assertEquals('Emma Watson', $name->toString());
    }

    public function test_throws_exception_when_name_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Student name cannot be empty.');

        new StudentName('');
    }

    public function test_throws_exception_when_name_is_only_whitespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Student name cannot be empty.');

        new StudentName('   ');
    }

    public function test_trims_whitespace_from_name(): void
    {
        $name = new StudentName('  Emma Watson  ');

        $this->assertEquals('Emma Watson', $name->toString());
    }

    public function test_converts_to_string(): void
    {
        $name = new StudentName('Emma Watson');

        $this->assertEquals('Emma Watson', (string) $name);
    }
}
