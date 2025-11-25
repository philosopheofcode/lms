<?php

declare(strict_types=1);

namespace Lms\Students\Domain;

final class StudentId
{
    private string $id;

    public function __construct(string $id)
    {
        if (empty(trim($id))) {
            throw new \InvalidArgumentException('Student ID cannot be empty.');
        }

        $this->id = $id;
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function equals(StudentId $other): bool
    {
        return $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
