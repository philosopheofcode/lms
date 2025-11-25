<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\Homework;
use Lms\Courses\Domain\HomeworkName;
use PHPUnit\Framework\TestCase;

final class HomeworkTest extends TestCase
{
    public function test_it_can_be_created_with_name(): void
    {
        $name = new HomeworkName('Assignment 1');
        $homework = new Homework($name);

        $this->assertSame($name, $homework->name());
    }
}
