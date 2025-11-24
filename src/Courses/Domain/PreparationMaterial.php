<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class PreparationMaterial
{
    public function __construct(
        public readonly string $name
    ) {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Preparation material name cannot be empty.');
        }
    }
}
