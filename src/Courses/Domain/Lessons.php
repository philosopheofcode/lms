<?php

declare(strict_types=1);

namespace Lms\Courses\Domain;

final class Lessons implements \IteratorAggregate, \Countable
{
    /**
     * @var array<Lesson>
     */
    private array $items;

    public function __construct(Lesson ...$items)
    {
        $this->ensureAtLeastOneLesson($items);
        $this->items = $items;
    }

    /**
     * @param array<Lesson> $items
     */
    private function ensureAtLeastOneLesson(array $items): void
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Course must have at least one lesson.');
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
}
