<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Course
{
    public function __construct(
        public readonly string $id,
        public readonly string $name
    ) {
        $this->ensureNameIsNotEmpty($name);
    }

    private function ensureNameIsNotEmpty(string $name): void
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Course name cannot be empty.');
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
