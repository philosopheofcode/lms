<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class PreparationMaterials implements \IteratorAggregate, \Countable
{
    /**
     * @var array<PreparationMaterial>
     */
    private array $items;

    public function __construct(PreparationMaterial ...$items)
    {
        $this->items = $items;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Find material by name
     */
    public function findByName(MaterialName $name): ?PreparationMaterial
    {
        foreach ($this->items as $material) {
            if ($material->name()->value() === $name->value()) {
                return $material;
            }
        }
        return null;
    }

    /**
     * Check if material exists
     */
    public function contains(MaterialName $name): bool
    {
        return $this->findByName($name) !== null;
    }

    /**
     * Add a new preparation material
     */
    public function add(PreparationMaterial $material): self
    {
        return new self(...[...$this->items, $material]);
    }

    /**
     * Check if the collection is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
