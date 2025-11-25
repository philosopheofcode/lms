<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Homework
{
    private HomeworkName $name;

    public function __construct(HomeworkName $name)
    {
        $this->name = $name;
    }

    public function name(): HomeworkName
    {
        return $this->name;
    }
}
