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

    /**
     * Get all upcoming lessons
     */
    public function getUpcoming(?\DateTimeImmutable $currentDate = null): self
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        $upcomingLessons = array_filter(
            $this->items,
            fn (Lesson $lesson) => $lesson->isUpcoming($currentDate)
        );

        if (empty($upcomingLessons)) {
            throw new \InvalidArgumentException('Course must have at least one lesson.');
        }

        return new self(...$upcomingLessons);
    }

    /**
     * Get all past lessons
     */
    public function getPast(?\DateTimeImmutable $currentDate = null): array
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        return array_filter(
            $this->items,
            fn (Lesson $lesson) => $lesson->hasOccurred($currentDate)
        );
    }

    /**
     * Get the next upcoming lesson
     */
    public function getNext(?\DateTimeImmutable $currentDate = null): ?Lesson
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        $upcomingLessons = $this->getUpcoming($currentDate);

        $sorted = iterator_to_array($upcomingLessons);
        usort($sorted, fn (Lesson $a, Lesson $b) => $a->scheduledAt() <=> $b->scheduledAt());

        return $sorted[0] ?? null;
    }

    /**
     * Find lesson scheduled at a specific date
     */
    public function findByDate(\DateTimeImmutable $date): ?Lesson
    {
        foreach ($this->items as $lesson) {
            if ($lesson->scheduledAt()->format('Y-m-d') === $date->format('Y-m-d')) {
                return $lesson;
            }
        }
        return null;
    }

    /**
     * Add a new lesson to the collection
     */
    public function add(Lesson $lesson): self
    {
        return new self(...[...$this->items, $lesson]);
    }
}
