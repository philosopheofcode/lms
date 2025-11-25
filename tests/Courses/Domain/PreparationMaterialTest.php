<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\MaterialName;
use Lms\Courses\Domain\PreparationMaterial;
use PHPUnit\Framework\TestCase;

final class PreparationMaterialTest extends TestCase
{
    public function test_it_can_be_created_with_name(): void
    {
        $name = new MaterialName('PHP Documentation');
        $material = new PreparationMaterial($name);

        $this->assertSame($name, $material->name());
    }
}
