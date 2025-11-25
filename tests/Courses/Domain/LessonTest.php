<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\Lesson;
use Lms\Courses\Domain\LessonName;
use PHPUnit\Framework\TestCase;

final class LessonTest extends TestCase
{
    public function test_it_can_be_created_with_name_and_scheduled_date(): void
    {
        $name = new LessonName('Lesson 1');
        $scheduledAt = new \DateTimeImmutable('2024-01-15 10:00:00');
        $lesson = new Lesson($name, $scheduledAt);

        $this->assertSame($name, $lesson->name());
        $this->assertSame($scheduledAt, $lesson->scheduledAt());
    }

    public function test_lesson_has_occurred_when_current_date_is_after_scheduled_date(): void
    {
        $scheduledAt = new \DateTimeImmutable('2024-01-15 10:00:00');
        $currentDate = new \DateTimeImmutable('2024-01-15 10:00:01');
        $lesson = new Lesson(new LessonName('Lesson 1'), $scheduledAt);

        $this->assertTrue($lesson->hasOccurred($currentDate));
    }

    public function test_lesson_has_not_occurred_when_current_date_equals_scheduled_date(): void
    {
        $scheduledAt = new \DateTimeImmutable('2024-01-15 10:00:00');
        $lesson = new Lesson(new LessonName('Lesson 1'), $scheduledAt);

        $this->assertFalse($lesson->hasOccurred($scheduledAt));
    }

    public function test_lesson_has_not_occurred_when_current_date_is_before_scheduled_date(): void
    {
        $scheduledAt = new \DateTimeImmutable('2024-01-15 10:00:00');
        $currentDate = new \DateTimeImmutable('2024-01-15 09:59:59');
        $lesson = new Lesson(new LessonName('Lesson 1'), $scheduledAt);

        $this->assertFalse($lesson->hasOccurred($currentDate));
    }

    public function test_lesson_is_today_when_scheduled_on_same_date(): void
    {
        $scheduledAt = new \DateTimeImmutable('2024-01-15 10:00:00');
        $currentDate = new \DateTimeImmutable('2024-01-15 18:30:00');
        $lesson = new Lesson(new LessonName('Lesson 1'), $scheduledAt);

        $this->assertTrue($lesson->isToday($currentDate));
    }

    public function test_lesson_is_not_today_when_scheduled_on_different_date(): void
    {
        $scheduledAt = new \DateTimeImmutable('2024-01-15 10:00:00');
        $currentDate = new \DateTimeImmutable('2024-01-16 10:00:00');
        $lesson = new Lesson(new LessonName('Lesson 1'), $scheduledAt);

        $this->assertFalse($lesson->isToday($currentDate));
    }

    public function test_lesson_is_upcoming_when_not_occurred(): void
    {
        $scheduledAt = new \DateTimeImmutable('2024-01-15 10:00:00');
        $currentDate = new \DateTimeImmutable('2024-01-15 09:59:59');
        $lesson = new Lesson(new LessonName('Lesson 1'), $scheduledAt);

        $this->assertTrue($lesson->isUpcoming($currentDate));
    }

    public function test_lesson_is_not_upcoming_when_occurred(): void
    {
        $scheduledAt = new \DateTimeImmutable('2024-01-15 10:00:00');
        $currentDate = new \DateTimeImmutable('2024-01-15 10:00:01');
        $lesson = new Lesson(new LessonName('Lesson 1'), $scheduledAt);

        $this->assertFalse($lesson->isUpcoming($currentDate));
    }

    public function test_lesson_can_be_rescheduled(): void
    {
        $originalDate = new \DateTimeImmutable('2024-01-15 10:00:00');
        $newDate = new \DateTimeImmutable('2024-01-20 14:00:00');
        $lesson = new Lesson(new LessonName('Lesson 1'), $originalDate);

        $rescheduledLesson = $lesson->reschedule($newDate);

        $this->assertNotSame($lesson, $rescheduledLesson);
        $this->assertSame($originalDate, $lesson->scheduledAt());
        $this->assertSame($newDate, $rescheduledLesson->scheduledAt());
        $this->assertSame($lesson->name(), $rescheduledLesson->name());
    }
}
