<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\Course;
use Lms\Courses\Domain\Homework;
use Lms\Courses\Domain\Homeworks;
use Lms\Courses\Domain\Lesson;
use Lms\Courses\Domain\Lessons;
use Lms\Courses\Domain\PreparationMaterial;
use Lms\Courses\Domain\PreparationMaterials;
use PHPUnit\Framework\TestCase;

final class CourseTest extends TestCase
{
    public function test_it_can_be_created_with_valid_data(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01 10:00:00');
        $lessons = new Lessons(new Lesson('Lesson 1', new \DateTimeImmutable('2024-01-02 10:00:00')));
        $homeworks = new Homeworks(new Homework('Homework 1'));
        $materials = new PreparationMaterials(new PreparationMaterial('Material 1'));
        $endDate = new \DateTimeImmutable('2024-02-01 10:00:00');

        $course = new Course(
            '1',
            'Introduction to PHP',
            $startDate,
            $endDate,
            $lessons,
            $homeworks,
            $materials
        );

        $this->assertSame('Introduction to PHP', $course->name);
        $this->assertSame($startDate, $course->startDate);
        $this->assertSame($endDate, $course->endDate);
        $this->assertSame($lessons, $course->lessons);
        $this->assertSame($homeworks, $course->homeworks);
        $this->assertSame($materials, $course->preparationMaterials);
    }

    public function test_it_can_be_created_without_optional_data(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01 10:00:00');
        $lessons = new Lessons(new Lesson('Lesson 1', new \DateTimeImmutable('2024-01-02 10:00:00')));
        $homeworks = new Homeworks(new Homework('Homework 1'));

        $course = new Course(
            '1',
            'Introduction to PHP',
            $startDate,
            null,
            $lessons,
            $homeworks
        );

        $this->assertNull($course->endDate);
        $this->assertCount(0, $course->preparationMaterials);
        $this->assertInstanceOf(PreparationMaterials::class, $course->preparationMaterials);
    }

    public function test_it_throws_exception_when_name_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course name cannot be empty.');

        new Course(
            '1',
            '',
            new \DateTimeImmutable(),
            null,
            new Lessons(new Lesson('L1', new \DateTimeImmutable())),
            new Homeworks(new Homework('H1'))
        );
    }

    public function test_it_throws_exception_when_no_lessons_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course must have at least one lesson.');

        new Lessons();
    }

    public function test_it_throws_exception_when_invalid_lesson_provided(): void
    {
        $this->expectException(\TypeError::class);

        new Lessons('not a lesson');
    }

    public function test_it_throws_exception_when_no_homeworks_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course must have at least one homework.');

        new Homeworks();
    }

    public function test_it_throws_exception_when_invalid_homework_provided(): void
    {
        $this->expectException(\TypeError::class);

        new Homeworks('not a homework');
    }

    public function test_it_throws_exception_when_end_date_is_before_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('End date must be after start date.');

        new Course(
            '1',
            'PHP Course',
            new \DateTimeImmutable('2024-02-01'),
            new \DateTimeImmutable('2024-01-01'),
            new Lessons(new Lesson('L1', new \DateTimeImmutable())),
            new Homeworks(new Homework('H1'))
        );
    }

    public function test_it_can_be_converted_to_string(): void
    {
        $name = 'PHP 101 Course';
        $course = new Course(
            '1',
            $name,
            new \DateTimeImmutable(),
            null,
            new Lessons(new Lesson('L1', new \DateTimeImmutable())),
            new Homeworks(new Homework('H1'))
        );

        $this->assertSame($name, (string) $course);
    }
}
