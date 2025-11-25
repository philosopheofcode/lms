<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class HomeworkName
{
    private string $value;

    public function __construct(string $value)
    {
        if (trim($value) === '') {
            throw new \InvalidArgumentException('Homework name cannot be empty.');
        }

        $this->value = trim($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
