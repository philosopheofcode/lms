<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\HomeworkName;
use PHPUnit\Framework\TestCase;

final class HomeworkNameTest extends TestCase
{
    public function test_it_can_be_created_with_valid_name(): void
    {
        $name = new HomeworkName('Assignment 1');

        $this->assertSame('Assignment 1', $name->value());
    }

    public function test_it_throws_exception_when_name_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Homework name cannot be empty.');

        new HomeworkName('');
    }

    public function test_it_throws_exception_when_name_is_whitespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Homework name cannot be empty.');

        new HomeworkName('   ');
    }

    public function test_it_trims_whitespace_from_name(): void
    {
        $name = new HomeworkName('  Assignment 1  ');

        $this->assertSame('Assignment 1', $name->value());
    }

    public function test_it_can_be_converted_to_string(): void
    {
        $name = new HomeworkName('Assignment 1');

        $this->assertSame('Assignment 1', (string) $name);
    }
}
