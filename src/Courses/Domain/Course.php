<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Course
{
    private CourseId $id;
    private CourseName $name;
    private \DateTimeImmutable $startDate;
    private ?\DateTimeImmutable $endDate;
    private Lessons $lessons;
    private Homeworks $homeworks;
    private PreparationMaterials $preparationMaterials;

    public function __construct(
        CourseId $id,
        CourseName $name,
        \DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate,
        Lessons $lessons,
        Homeworks $homeworks,
        PreparationMaterials $preparationMaterials = new PreparationMaterials()
    ) {
        $this->ensureEndDateIsAfterStartDate($startDate, $endDate);

        $this->id = $id;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->lessons = $lessons;
        $this->homeworks = $homeworks;
        $this->preparationMaterials = $preparationMaterials;
    }

    public function id(): CourseId
    {
        return $this->id;
    }

    public function name(): CourseName
    {
        return $this->name;
    }

    public function startDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function lessons(): Lessons
    {
        return $this->lessons;
    }

    public function homeworks(): Homeworks
    {
        return $this->homeworks;
    }

    public function preparationMaterials(): PreparationMaterials
    {
        return $this->preparationMaterials;
    }

    /**
     * Check if the course has started
     */
    public function hasStarted(?\DateTimeImmutable $currentDate = null): bool
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        return $currentDate >= $this->startDate;
    }

    /**
     * Check if the course has ended
     */
    public function hasEnded(?\DateTimeImmutable $currentDate = null): bool
    {
        if ($this->endDate === null) {
            return false;
        }
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        return $currentDate > $this->endDate;
    }

    /**
     * Check if the course is currently ongoing
     */
    public function isOngoing(?\DateTimeImmutable $currentDate = null): bool
    {
        return $this->hasStarted($currentDate) && !$this->hasEnded($currentDate);
    }

    /**
     * Get the duration of the course in days
     */
    public function durationInDays(): ?int
    {
        if ($this->endDate === null) {
            return null;
        }
        return $this->startDate->diff($this->endDate)->days;
    }

    /**
     * Check if enrollment should be open (e.g., before course starts)
     */
    public function canEnroll(?\DateTimeImmutable $currentDate = null): bool
    {
        return !$this->hasStarted($currentDate);
    }

    /**
     * Add a preparation material to the course
     */
    public function addPreparationMaterial(PreparationMaterial $material): self
    {
        $this->preparationMaterials = $this->preparationMaterials->add($material);
        return $this;
    }

    private function ensureEndDateIsAfterStartDate(\DateTimeImmutable $startDate, ?\DateTimeImmutable $endDate): void
    {
        if ($endDate !== null && $endDate <= $startDate) {
            throw new \InvalidArgumentException('End date must be after start date.');
        }
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
