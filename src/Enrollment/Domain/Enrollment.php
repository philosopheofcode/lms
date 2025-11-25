<?php

declare(strict_types=1);

namespace Lms\Enrollment\Domain;

use Lms\Students\Domain\StudentId;
use Lms\Courses\Domain\CourseId;

final class Enrollment
{
    private StudentId $studentId;
    private CourseId $courseId;
    private \DateTimeImmutable $startDate;
    private \DateTimeImmutable $endDate;

    public function __construct(
        StudentId $studentId,
        CourseId $courseId,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate
    ) {
        $this->ensureEndDateIsAfterStartDate($startDate, $endDate);

        $this->studentId = $studentId;
        $this->courseId = $courseId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function studentId(): StudentId
    {
        return $this->studentId;
    }

    public function courseId(): CourseId
    {
        return $this->courseId;
    }

    public function startDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * Check if the enrollment is currently active at a given date
     */
    public function isActiveAt(\DateTimeImmutable $date): bool
    {
        return $date >= $this->startDate && $date <= $this->endDate;
    }

    /**
     * Check if the enrollment has started
     */
    public function hasStarted(?\DateTimeImmutable $currentDate = null): bool
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        return $currentDate >= $this->startDate;
    }

    /**
     * Check if the enrollment has expired
     */
    public function hasExpired(?\DateTimeImmutable $currentDate = null): bool
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        return $currentDate > $this->endDate;
    }

    /**
     * Shorten the enrollment by updating the end date
     * This simulates external systems modifying enrollment periods
     */
    public function shortenTo(\DateTimeImmutable $newEndDate): self
    {
        return $this->updateEndDate($newEndDate);
    }

    /**
     * Extend the enrollment by updating the end date
     */
    public function extendTo(\DateTimeImmutable $newEndDate): self
    {
        return $this->updateEndDate($newEndDate);
    }

    private function ensureEndDateIsAfterStartDate(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): void
    {
        if ($endDate <= $startDate) {
            throw new \InvalidArgumentException('End date must be after start date.');
        }
    }

    /**
     * @param \DateTimeImmutable $newEndDate
     * @return self
     */
    public function updateEndDate(\DateTimeImmutable $newEndDate): Enrollment
    {
        $this->ensureEndDateIsAfterStartDate($this->startDate, $newEndDate);

        return new self(
            $this->studentId,
            $this->courseId,
            $this->startDate,
            $newEndDate
        );
    }
}
