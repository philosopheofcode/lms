<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\MaterialName;
use Lms\Courses\Domain\PreparationMaterial;
use Lms\Courses\Domain\PreparationMaterials;
use PHPUnit\Framework\TestCase;

final class PreparationMaterialsTest extends TestCase
{
    public function test_it_can_be_created_empty(): void
    {
        $materials = new PreparationMaterials();

        $this->assertCount(0, $materials);
        $this->assertTrue($materials->isEmpty());
    }

    public function test_it_can_be_created_with_materials(): void
    {
        $material1 = new PreparationMaterial(new MaterialName('Material 1'));
        $material2 = new PreparationMaterial(new MaterialName('Material 2'));
        $materials = new PreparationMaterials($material1, $material2);

        $this->assertCount(2, $materials);
        $this->assertFalse($materials->isEmpty());
    }

    public function test_it_can_be_iterated(): void
    {
        $material1 = new PreparationMaterial(new MaterialName('Material 1'));
        $material2 = new PreparationMaterial(new MaterialName('Material 2'));
        $materials = new PreparationMaterials($material1, $material2);

        $items = [];
        foreach ($materials as $material) {
            $items[] = $material;
        }

        $this->assertCount(2, $items);
        $this->assertSame($material1, $items[0]);
        $this->assertSame($material2, $items[1]);
    }

    public function test_it_can_find_material_by_name(): void
    {
        $material1 = new PreparationMaterial(new MaterialName('Material 1'));
        $material2 = new PreparationMaterial(new MaterialName('Material 2'));
        $materials = new PreparationMaterials($material1, $material2);

        $found = $materials->findByName(new MaterialName('Material 1'));

        $this->assertSame($material1, $found);
    }

    public function test_it_returns_null_when_material_not_found(): void
    {
        $material1 = new PreparationMaterial(new MaterialName('Material 1'));
        $materials = new PreparationMaterials($material1);

        $found = $materials->findByName(new MaterialName('Material 99'));

        $this->assertNull($found);
    }

    public function test_it_can_check_if_material_exists(): void
    {
        $material1 = new PreparationMaterial(new MaterialName('Material 1'));
        $materials = new PreparationMaterials($material1);

        $this->assertTrue($materials->contains(new MaterialName('Material 1')));
        $this->assertFalse($materials->contains(new MaterialName('Material 99')));
    }

    public function test_it_can_add_material(): void
    {
        $material1 = new PreparationMaterial(new MaterialName('Material 1'));
        $material2 = new PreparationMaterial(new MaterialName('Material 2'));
        $materials = new PreparationMaterials($material1);

        $newMaterials = $materials->add($material2);

        $this->assertNotSame($materials, $newMaterials);
        $this->assertCount(1, $materials);
        $this->assertCount(2, $newMaterials);
    }
}
