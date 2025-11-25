<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class PreparationMaterial
{
    private MaterialName $name;

    public function __construct(MaterialName $name)
    {
        $this->name = $name;
    }

    public function name(): MaterialName
    {
        return $this->name;
    }
}
