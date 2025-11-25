<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Lesson
{
    private LessonName $name;
    private \DateTimeImmutable $scheduledAt;

    public function __construct(LessonName $name, \DateTimeImmutable $scheduledAt)
    {
        $this->name = $name;
        $this->scheduledAt = $scheduledAt;
    }

    public function name(): LessonName
    {
        return $this->name;
    }

    public function scheduledAt(): \DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    /**
     * Check if the lesson has already occurred
     */
    public function hasOccurred(?\DateTimeImmutable $currentDate = null): bool
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        return $currentDate > $this->scheduledAt;
    }

    /**
     * Check if the lesson is scheduled for today
     */
    public function isToday(?\DateTimeImmutable $currentDate = null): bool
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        return $this->scheduledAt->format('Y-m-d') === $currentDate->format('Y-m-d');
    }

    /**
     * Check if the lesson is upcoming
     */
    public function isUpcoming(?\DateTimeImmutable $currentDate = null): bool
    {
        return !$this->hasOccurred($currentDate);
    }

    /**
     * Reschedule the lesson to a new date/time
     */
    public function reschedule(\DateTimeImmutable $newScheduledAt): self
    {
        return new self($this->name, $newScheduledAt);
    }
}
