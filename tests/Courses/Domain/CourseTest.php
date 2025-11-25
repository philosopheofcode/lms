<?php

declare(strict_types=1);

namespace Tests\Courses\Domain;

use Lms\Courses\Domain\Course;
use Lms\Courses\Domain\CourseId;
use Lms\Courses\Domain\CourseName;
use Lms\Courses\Domain\Homework;
use Lms\Courses\Domain\HomeworkName;
use Lms\Courses\Domain\Homeworks;
use Lms\Courses\Domain\Lesson;
use Lms\Courses\Domain\LessonName;
use Lms\Courses\Domain\Lessons;
use Lms\Courses\Domain\MaterialName;
use Lms\Courses\Domain\PreparationMaterial;
use Lms\Courses\Domain\PreparationMaterials;
use PHPUnit\Framework\TestCase;

final class CourseTest extends TestCase
{
    public function test_it_can_be_created_with_valid_data(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01 10:00:00');
        $lessons = new Lessons(new Lesson(new LessonName('Lesson 1'), new \DateTimeImmutable('2024-01-02 10:00:00')));
        $homeworks = new Homeworks(new Homework(new HomeworkName('Homework 1')));
        $materials = new PreparationMaterials(new PreparationMaterial(new MaterialName('Material 1')));
        $endDate = new \DateTimeImmutable('2024-02-01 10:00:00');

        $course = new Course(
            new CourseId('1'),
            new CourseName('Introduction to PHP'),
            $startDate,
            $endDate,
            $lessons,
            $homeworks,
            $materials
        );

        $this->assertSame('Introduction to PHP', $course->name()->value());
        $this->assertSame($startDate, $course->startDate());
        $this->assertSame($endDate, $course->endDate());
        $this->assertSame($lessons, $course->lessons());
        $this->assertSame($homeworks, $course->homeworks());
        $this->assertSame($materials, $course->preparationMaterials());
    }

    public function test_it_can_be_created_without_optional_data(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01 10:00:00');
        $lessons = new Lessons(new Lesson(new LessonName('Lesson 1'), new \DateTimeImmutable('2024-01-02 10:00:00')));
        $homeworks = new Homeworks(new Homework(new HomeworkName('Homework 1')));

        $course = new Course(
            new CourseId('1'),
            new CourseName('Introduction to PHP'),
            $startDate,
            null,
            $lessons,
            $homeworks
        );

        $this->assertNull($course->endDate());
        $this->assertCount(0, $course->preparationMaterials());
        $this->assertInstanceOf(PreparationMaterials::class, $course->preparationMaterials());
    }

    public function test_it_throws_exception_when_name_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course name cannot be empty.');

        new CourseName('');
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
            new CourseId('1'),
            new CourseName('PHP Course'),
            new \DateTimeImmutable('2024-02-01'),
            new \DateTimeImmutable('2024-01-01'),
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );
    }

    public function test_it_can_be_converted_to_string(): void
    {
        $name = 'PHP 101 Course';
        $course = new Course(
            new CourseId('1'),
            new CourseName($name),
            new \DateTimeImmutable(),
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertSame($name, (string) $course);
    }

    public function test_it_returns_course_id(): void
    {
        $courseId = new CourseId('course-123');
        $course = new Course(
            $courseId,
            new CourseName('PHP Course'),
            new \DateTimeImmutable(),
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertSame($courseId, $course->id());
    }

    public function test_it_throws_exception_when_end_date_equals_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('End date must be after start date.');

        $date = new \DateTimeImmutable('2024-01-01');
        new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $date,
            $date,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );
    }

    public function test_course_has_started_when_current_date_is_after_start_date(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $currentDate = new \DateTimeImmutable('2024-01-15');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertTrue($course->hasStarted($currentDate));
    }

    public function test_course_has_started_when_current_date_equals_start_date(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertTrue($course->hasStarted($startDate));
    }

    public function test_course_has_not_started_when_current_date_is_before_start_date(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-15');
        $currentDate = new \DateTimeImmutable('2024-01-01');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertFalse($course->hasStarted($currentDate));
    }

    public function test_course_has_ended_when_current_date_is_after_end_date(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $endDate = new \DateTimeImmutable('2024-01-31');
        $currentDate = new \DateTimeImmutable('2024-02-01');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            $endDate,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertTrue($course->hasEnded($currentDate));
    }

    public function test_course_has_not_ended_when_current_date_equals_end_date(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $endDate = new \DateTimeImmutable('2024-01-31');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            $endDate,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertFalse($course->hasEnded($endDate));
    }

    public function test_course_has_not_ended_when_current_date_is_before_end_date(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $endDate = new \DateTimeImmutable('2024-01-31');
        $currentDate = new \DateTimeImmutable('2024-01-15');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            $endDate,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertFalse($course->hasEnded($currentDate));
    }

    public function test_course_has_not_ended_when_end_date_is_null(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $currentDate = new \DateTimeImmutable('2024-12-31');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertFalse($course->hasEnded($currentDate));
    }

    public function test_course_is_ongoing_when_started_but_not_ended(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $endDate = new \DateTimeImmutable('2024-01-31');
        $currentDate = new \DateTimeImmutable('2024-01-15');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            $endDate,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertTrue($course->isOngoing($currentDate));
    }

    public function test_course_is_not_ongoing_when_not_started(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-15');
        $endDate = new \DateTimeImmutable('2024-01-31');
        $currentDate = new \DateTimeImmutable('2024-01-01');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            $endDate,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertFalse($course->isOngoing($currentDate));
    }

    public function test_course_is_not_ongoing_when_ended(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $endDate = new \DateTimeImmutable('2024-01-31');
        $currentDate = new \DateTimeImmutable('2024-02-01');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            $endDate,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertFalse($course->isOngoing($currentDate));
    }

    public function test_duration_in_days_returns_null_when_no_end_date(): void
    {
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            new \DateTimeImmutable('2024-01-01'),
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertNull($course->durationInDays());
    }

    public function test_duration_in_days_returns_correct_number_of_days(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $endDate = new \DateTimeImmutable('2024-01-31');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            $endDate,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertSame(30, $course->durationInDays());
    }

    public function test_can_enroll_when_course_has_not_started(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-15');
        $currentDate = new \DateTimeImmutable('2024-01-01');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertTrue($course->canEnroll($currentDate));
    }

    public function test_cannot_enroll_when_course_has_started(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-01');
        $currentDate = new \DateTimeImmutable('2024-01-15');
        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            $startDate,
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1')))
        );

        $this->assertFalse($course->canEnroll($currentDate));
    }

    public function test_it_can_add_preparation_material(): void
    {
        $material1 = new PreparationMaterial(new MaterialName('Material 1'));
        $material2 = new PreparationMaterial(new MaterialName('Material 2'));
        $materials = new PreparationMaterials($material1);

        $course = new Course(
            new CourseId('1'),
            new CourseName('PHP Course'),
            new \DateTimeImmutable(),
            null,
            new Lessons(new Lesson(new LessonName('L1'), new \DateTimeImmutable())),
            new Homeworks(new Homework(new HomeworkName('H1'))),
            $materials
        );

        $newCourse = $course->addPreparationMaterial($material2);

        $this->assertNotSame($course, $newCourse);
        $this->assertCount(1, $course->preparationMaterials());
        $this->assertCount(2, $newCourse->preparationMaterials());
    }
}
