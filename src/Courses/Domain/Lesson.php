<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Lesson
{
    public function __construct(
        public readonly string $name,
        public readonly \DateTimeImmutable $scheduledAt
    ) {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Lesson name cannot be empty.');
        }
    }
}
