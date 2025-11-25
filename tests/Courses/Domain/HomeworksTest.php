<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\Homework;
use Lms\Courses\Domain\HomeworkName;
use Lms\Courses\Domain\Homeworks;
use PHPUnit\Framework\TestCase;

final class HomeworksTest extends TestCase
{
    public function test_it_can_be_created_with_homeworks(): void
    {
        $homework1 = new Homework(new HomeworkName('Assignment 1'));
        $homework2 = new Homework(new HomeworkName('Assignment 2'));
        $homeworks = new Homeworks($homework1, $homework2);

        $this->assertCount(2, $homeworks);
    }

    public function test_it_can_be_iterated(): void
    {
        $homework1 = new Homework(new HomeworkName('Assignment 1'));
        $homework2 = new Homework(new HomeworkName('Assignment 2'));
        $homeworks = new Homeworks($homework1, $homework2);

        $items = [];
        foreach ($homeworks as $homework) {
            $items[] = $homework;
        }

        $this->assertCount(2, $items);
        $this->assertSame($homework1, $items[0]);
        $this->assertSame($homework2, $items[1]);
    }

    public function test_it_can_find_homework_by_name(): void
    {
        $homework1 = new Homework(new HomeworkName('Assignment 1'));
        $homework2 = new Homework(new HomeworkName('Assignment 2'));
        $homeworks = new Homeworks($homework1, $homework2);

        $found = $homeworks->findByName(new HomeworkName('Assignment 1'));

        $this->assertSame($homework1, $found);
    }

    public function test_it_returns_null_when_homework_not_found(): void
    {
        $homework1 = new Homework(new HomeworkName('Assignment 1'));
        $homeworks = new Homeworks($homework1);

        $found = $homeworks->findByName(new HomeworkName('Assignment 99'));

        $this->assertNull($found);
    }

    public function test_it_can_check_if_homework_exists(): void
    {
        $homework1 = new Homework(new HomeworkName('Assignment 1'));
        $homeworks = new Homeworks($homework1);

        $this->assertTrue($homeworks->contains(new HomeworkName('Assignment 1')));
        $this->assertFalse($homeworks->contains(new HomeworkName('Assignment 99')));
    }

    public function test_it_can_add_homework(): void
    {
        $homework1 = new Homework(new HomeworkName('Assignment 1'));
        $homework2 = new Homework(new HomeworkName('Assignment 2'));
        $homeworks = new Homeworks($homework1);

        $newHomeworks = $homeworks->add($homework2);

        $this->assertNotSame($homeworks, $newHomeworks);
        $this->assertCount(1, $homeworks);
        $this->assertCount(2, $newHomeworks);
    }
}
