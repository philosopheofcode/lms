<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Course
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly \DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
        public readonly Lessons $lessons,
        public readonly Homeworks $homeworks,
        public readonly PreparationMaterials $preparationMaterials = new PreparationMaterials()
    ) {
        $this->ensureNameIsNotEmpty($name);
        $this->ensureEndDateIsAfterStartDate($startDate, $endDate);
    }

    private function ensureNameIsNotEmpty(string $name): void
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Course name cannot be empty.');
        }
    }

    private function ensureEndDateIsAfterStartDate(\DateTimeImmutable $startDate, ?\DateTimeImmutable $endDate): void
    {
        if ($endDate !== null && $endDate <= $startDate) {
            throw new \InvalidArgumentException('End date must be after start date.');
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
