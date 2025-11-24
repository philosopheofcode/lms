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
}
