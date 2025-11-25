<?php

declare(strict_types=1);

namespace Lms\Students\Domain;

final class StudentName
{
    private string $name;

    public function __construct(string $name)
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Student name cannot be empty.');
        }

        $this->name = trim($name);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
