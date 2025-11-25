<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\Lesson;
use Lms\Courses\Domain\LessonName;
use Lms\Courses\Domain\Lessons;
use PHPUnit\Framework\TestCase;

final class LessonsTest extends TestCase
{
    public function test_it_can_be_created_with_lessons(): void
    {
        $lesson1 = new Lesson(new LessonName('Lesson 1'), new \DateTimeImmutable('2024-01-15'));
        $lesson2 = new Lesson(new LessonName('Lesson 2'), new \DateTimeImmutable('2024-01-20'));
        $lessons = new Lessons($lesson1, $lesson2);

        $this->assertCount(2, $lessons);
    }

    public function test_it_can_be_iterated(): void
    {
        $lesson1 = new Lesson(new LessonName('Lesson 1'), new \DateTimeImmutable('2024-01-15'));
        $lesson2 = new Lesson(new LessonName('Lesson 2'), new \DateTimeImmutable('2024-01-20'));
        $lessons = new Lessons($lesson1, $lesson2);

        $items = [];
        foreach ($lessons as $lesson) {
            $items[] = $lesson;
        }

        $this->assertCount(2, $items);
        $this->assertSame($lesson1, $items[0]);
        $this->assertSame($lesson2, $items[1]);
    }

    public function test_it_can_get_upcoming_lessons(): void
    {
        $lesson1 = new Lesson(new LessonName('Past Lesson'), new \DateTimeImmutable('2024-01-10'));
        $lesson2 = new Lesson(new LessonName('Future Lesson'), new \DateTimeImmutable('2024-01-20'));
        $lessons = new Lessons($lesson1, $lesson2);
        $currentDate = new \DateTimeImmutable('2024-01-15');

        $upcoming = $lessons->getUpcoming($currentDate);

        $this->assertCount(1, $upcoming);
    }

    public function test_it_throws_exception_when_no_upcoming_lessons(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course must have at least one lesson.');

        $lesson1 = new Lesson(new LessonName('Past Lesson'), new \DateTimeImmutable('2024-01-10'));
        $lessons = new Lessons($lesson1);
        $currentDate = new \DateTimeImmutable('2024-01-15');

        $lessons->getUpcoming($currentDate);
    }

    public function test_it_can_get_past_lessons(): void
    {
        $lesson1 = new Lesson(new LessonName('Past Lesson'), new \DateTimeImmutable('2024-01-10'));
        $lesson2 = new Lesson(new LessonName('Future Lesson'), new \DateTimeImmutable('2024-01-20'));
        $lessons = new Lessons($lesson1, $lesson2);
        $currentDate = new \DateTimeImmutable('2024-01-15');

        $past = $lessons->getPast($currentDate);

        $this->assertCount(1, $past);
    }

    public function test_it_returns_empty_array_when_no_past_lessons(): void
    {
        $lesson1 = new Lesson(new LessonName('Future Lesson'), new \DateTimeImmutable('2024-01-20'));
        $lessons = new Lessons($lesson1);
        $currentDate = new \DateTimeImmutable('2024-01-15');

        $past = $lessons->getPast($currentDate);

        $this->assertCount(0, $past);
    }

    public function test_it_can_get_next_upcoming_lesson(): void
    {
        $lesson1 = new Lesson(new LessonName('Future Lesson 1'), new \DateTimeImmutable('2024-01-25'));
        $lesson2 = new Lesson(new LessonName('Future Lesson 2'), new \DateTimeImmutable('2024-01-20'));
        $lesson3 = new Lesson(new LessonName('Future Lesson 3'), new \DateTimeImmutable('2024-01-30'));
        $lessons = new Lessons($lesson1, $lesson2, $lesson3);
        $currentDate = new \DateTimeImmutable('2024-01-15');

        $next = $lessons->getNext($currentDate);

        $this->assertSame($lesson2, $next);
    }

    public function test_it_can_find_lesson_by_date(): void
    {
        $lesson1 = new Lesson(new LessonName('Lesson 1'), new \DateTimeImmutable('2024-01-15 10:00:00'));
        $lesson2 = new Lesson(new LessonName('Lesson 2'), new \DateTimeImmutable('2024-01-20 14:00:00'));
        $lessons = new Lessons($lesson1, $lesson2);

        $found = $lessons->findByDate(new \DateTimeImmutable('2024-01-15 18:00:00'));

        $this->assertSame($lesson1, $found);
    }

    public function test_it_returns_null_when_lesson_not_found_by_date(): void
    {
        $lesson1 = new Lesson(new LessonName('Lesson 1'), new \DateTimeImmutable('2024-01-15'));
        $lessons = new Lessons($lesson1);

        $found = $lessons->findByDate(new \DateTimeImmutable('2024-01-20'));

        $this->assertNull($found);
    }

    public function test_it_can_add_lesson(): void
    {
        $lesson1 = new Lesson(new LessonName('Lesson 1'), new \DateTimeImmutable('2024-01-15'));
        $lesson2 = new Lesson(new LessonName('Lesson 2'), new \DateTimeImmutable('2024-01-20'));
        $lessons = new Lessons($lesson1);

        $newLessons = $lessons->add($lesson2);

        $this->assertNotSame($lessons, $newLessons);
        $this->assertCount(1, $lessons);
        $this->assertCount(2, $newLessons);
    }
}
