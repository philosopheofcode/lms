<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Homeworks implements \IteratorAggregate, \Countable
{
    /**
     * @var array<Homework>
     */
    private array $items;

    public function __construct(Homework ...$items)
    {
        $this->ensureAtLeastOneHomework($items);
        $this->items = $items;
    }

    /**
     * @param array<Homework> $items
     */
    private function ensureAtLeastOneHomework(array $items): void
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Course must have at least one homework.');
        }
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
     * Find homework by name
     */
    public function findByName(HomeworkName $name): ?Homework
    {
        foreach ($this->items as $homework) {
            if ($homework->name()->value() === $name->value()) {
                return $homework;
            }
        }
        return null;
    }

    /**
     * Check if homework exists
     */
    public function contains(HomeworkName $name): bool
    {
        return $this->findByName($name) !== null;
    }

    /**
     * Add a new homework
     */
    public function add(Homework $homework): self
    {
        return new self(...[...$this->items, $homework]);
    }
}
