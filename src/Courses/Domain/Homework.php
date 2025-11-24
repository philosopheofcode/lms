<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Homework
{
    public function __construct(
        public readonly string $name
    ) {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Homework name cannot be empty.');
        }
    }
}
