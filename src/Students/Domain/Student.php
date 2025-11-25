<?php

declare(strict_types=1);

namespace Lms\Students\Domain;

final class Student
{
    private StudentId $id;
    private StudentName $name;

    public function __construct(StudentId $id, StudentName $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): StudentId
    {
        return $this->id;
    }

    public function name(): StudentName
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
