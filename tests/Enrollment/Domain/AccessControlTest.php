<?php

declare(strict_types=1);

namespace Tests\Enrollment\Domain;

use Lms\Enrollment\Domain\AccessControl;
use Lms\Enrollment\Domain\Enrollment;
use Lms\Students\Domain\StudentId;
use Lms\Courses\Domain\Course;
use Lms\Courses\Domain\CourseId;
use Lms\Courses\Domain\CourseName;
use Lms\Courses\Domain\Lesson;
use Lms\Courses\Domain\LessonName;
use Lms\Courses\Domain\Lessons;
use Lms\Courses\Domain\Homework;
use Lms\Courses\Domain\HomeworkName;
use Lms\Courses\Domain\Homeworks;
use Lms\Courses\Domain\PreparationMaterial;
use Lms\Courses\Domain\MaterialName;
use Lms\Courses\Domain\PreparationMaterials;
use PHPUnit\Framework\TestCase;

final class AccessControlTest extends TestCase
{
    private AccessControl $accessControl;

    protected function setUp(): void
    {
        $this->accessControl = new AccessControl();
    }

    /**
     * Comprehensive scenario test: Emma's journey through A-Level Biology course
     *
     * Setup:
     * - Course: "A-Level Biology", starts 13/05/2025, ends 12/06/2025
     * - Lesson: "Cell Structure" scheduled for 15/05/2025 10:00
     * - Homework: "Label a Plant Cell"
     * - Prep Material: "Biology Reading Guide"
     * - Student: Emma, initially enrolled 01/05/2025 → 30/05/2025
     */
    public function test_comprehensive_enrollment_and_access_control_scenario(): void
    {
        $courseId = new CourseId('biology-alevel');
        $courseName = new CourseName('A-Level Biology');
        $courseStartDate = new \DateTimeImmutable('2025-05-13');
        $courseEndDate = new \DateTimeImmutable('2025-06-12');

        $lesson = new Lesson(
            new LessonName('Cell Structure'),
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );
        $lessons = new Lessons($lesson);

        $homework = new Homework(new HomeworkName('Label a Plant Cell'));
        $homeworks = new Homeworks($homework);

        $prepMaterial = new PreparationMaterial(new MaterialName('Biology Reading Guide'));
        $prepMaterials = new PreparationMaterials($prepMaterial);

        $course = new Course(
            $courseId,
            $courseName,
            $courseStartDate,
            $courseEndDate,
            $lessons,
            $homeworks,
            $prepMaterials
        );

        $emmaId = new StudentId('emma');
        $enrollment = new Enrollment(
            $emmaId,
            $courseId,
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-30')
        );

        // Scenario 1: On 01/05/2025, Emma tries to access Prep Material → ❌ Denied (course not started)
        $date1 = new \DateTimeImmutable('2025-05-01');
        $canAccessPrepMaterial1 = $this->accessControl->canAccessPreparationMaterial(
            $enrollment,
            $course,
            $prepMaterial,
            $date1
        );
        $this->assertFalse($canAccessPrepMaterial1, 'Emma should not access prep material before course starts');

        // Scenario 2: On 13/05/2025, she accesses Prep Material → ✅ Allowed
        $date2 = new \DateTimeImmutable('2025-05-13');
        $canAccessPrepMaterial2 = $this->accessControl->canAccessPreparationMaterial(
            $enrollment,
            $course,
            $prepMaterial,
            $date2
        );
        $this->assertTrue($canAccessPrepMaterial2, 'Emma should access prep material on course start date');

        // Scenario 3: On 15/05/2025 at 10:01, she accesses the Lesson → ✅ Allowed
        $date3 = new \DateTimeImmutable('2025-05-15 10:01:00');
        $canAccessLesson = $this->accessControl->canAccessLesson(
            $enrollment,
            $course,
            $lesson,
            $date3
        );
        $this->assertTrue($canAccessLesson, 'Emma should access lesson after its scheduled time');

        // Scenario 4: On 20/05/2025, external system shortens Emma's enrollment → new end date is 20/05/2025
        $enrollment = $enrollment->shortenTo(new \DateTimeImmutable('2025-05-20'));

        // Scenario 5: On 21/05/2025, she tries to access Homework → ❌ Denied (enrollment expired early)
        $date5 = new \DateTimeImmutable('2025-05-21');
        $canAccessHomework1 = $this->accessControl->canAccessHomework(
            $enrollment,
            $course,
            $homework,
            $date5
        );
        $this->assertFalse($canAccessHomework1, 'Emma should not access homework after enrollment expired');

        // Scenario 6: On 30/05/2025, she tries again → ❌ Denied
        $date6 = new \DateTimeImmutable('2025-05-30');
        $canAccessHomework2 = $this->accessControl->canAccessHomework(
            $enrollment,
            $course,
            $homework,
            $date6
        );
        $this->assertFalse($canAccessHomework2, 'Emma should not access homework after enrollment expired');

        // Scenario 7: On 10/06/2025, the course is still running, but Emma is no longer enrolled → ❌ Denied
        $date7 = new \DateTimeImmutable('2025-06-10');
        $canAccessPrepMaterial3 = $this->accessControl->canAccessPreparationMaterial(
            $enrollment,
            $course,
            $prepMaterial,
            $date7
        );
        $this->assertFalse($canAccessPrepMaterial3, 'Emma should not access content when course is running but enrollment expired');
        $this->assertTrue($course->isOngoing($date7), 'Course should still be ongoing');
    }

    public function test_student_cannot_access_lesson_when_not_enrolled(): void
    {
        $course = $this->createSampleCourse();
        $lesson = new Lesson(
            new LessonName('Introduction'),
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-10')
        );

        $currentDate = new \DateTimeImmutable('2025-05-15 10:01:00');

        $canAccess = $this->accessControl->canAccessLesson($enrollment, $course, $lesson, $currentDate);

        $this->assertFalse($canAccess, 'Student should not access lesson when enrollment has expired');
    }

    public function test_student_cannot_access_lesson_before_course_starts(): void
    {
        $course = $this->createSampleCourse();
        $lesson = new Lesson(
            new LessonName('Introduction'),
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $currentDate = new \DateTimeImmutable('2025-05-12');

        $canAccess = $this->accessControl->canAccessLesson($enrollment, $course, $lesson, $currentDate);

        $this->assertFalse($canAccess, 'Student should not access lesson before course starts');
    }

    public function test_student_cannot_access_lesson_before_its_scheduled_time(): void
    {
        $course = $this->createSampleCourse();
        $lesson = new Lesson(
            new LessonName('Introduction'),
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $currentDate = new \DateTimeImmutable('2025-05-15 09:59:00');

        $canAccess = $this->accessControl->canAccessLesson($enrollment, $course, $lesson, $currentDate);

        $this->assertFalse($canAccess, 'Student should not access lesson before its scheduled time');
    }

    public function test_student_can_access_lesson_when_all_conditions_met(): void
    {
        $course = $this->createSampleCourse();
        $lesson = new Lesson(
            new LessonName('Introduction'),
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $currentDate = new \DateTimeImmutable('2025-05-15 10:00:00');

        $canAccess = $this->accessControl->canAccessLesson($enrollment, $course, $lesson, $currentDate);

        $this->assertTrue($canAccess, 'Student should access lesson when enrolled, course started, and lesson time reached');
    }

    public function test_student_cannot_access_homework_when_not_enrolled(): void
    {
        $course = $this->createSampleCourse();
        $homework = new Homework(new HomeworkName('Exercise 1'));

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-10')
        );

        $currentDate = new \DateTimeImmutable('2025-05-15');

        $canAccess = $this->accessControl->canAccessHomework($enrollment, $course, $homework, $currentDate);

        $this->assertFalse($canAccess, 'Student should not access homework when enrollment has expired');
    }

    public function test_student_cannot_access_homework_before_course_starts(): void
    {
        $course = $this->createSampleCourse();
        $homework = new Homework(new HomeworkName('Exercise 1'));

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $currentDate = new \DateTimeImmutable('2025-05-12');

        $canAccess = $this->accessControl->canAccessHomework($enrollment, $course, $homework, $currentDate);

        $this->assertFalse($canAccess, 'Student should not access homework before course starts');
    }

    public function test_student_can_access_homework_from_course_start_onward(): void
    {
        $course = $this->createSampleCourse();
        $homework = new Homework(new HomeworkName('Exercise 1'));

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $currentDate = new \DateTimeImmutable('2025-05-13');

        $canAccess = $this->accessControl->canAccessHomework($enrollment, $course, $homework, $currentDate);

        $this->assertTrue($canAccess, 'Student should access homework from course start date onward');
    }

    public function test_student_cannot_access_prep_material_when_not_enrolled(): void
    {
        $course = $this->createSampleCourse();
        $prepMaterial = new PreparationMaterial(new MaterialName('Reading Guide'));

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-10')
        );

        $currentDate = new \DateTimeImmutable('2025-05-15');

        $canAccess = $this->accessControl->canAccessPreparationMaterial($enrollment, $course, $prepMaterial, $currentDate);

        $this->assertFalse($canAccess, 'Student should not access prep material when enrollment has expired');
    }

    public function test_student_cannot_access_prep_material_before_course_starts(): void
    {
        $course = $this->createSampleCourse();
        $prepMaterial = new PreparationMaterial(new MaterialName('Reading Guide'));

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $currentDate = new \DateTimeImmutable('2025-05-12');

        $canAccess = $this->accessControl->canAccessPreparationMaterial($enrollment, $course, $prepMaterial, $currentDate);

        $this->assertFalse($canAccess, 'Student should not access prep material before course starts');
    }

    public function test_student_can_access_prep_material_from_course_start_onward(): void
    {
        $course = $this->createSampleCourse();
        $prepMaterial = new PreparationMaterial(new MaterialName('Reading Guide'));

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            $course->id(),
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $currentDate = new \DateTimeImmutable('2025-05-13');

        $canAccess = $this->accessControl->canAccessPreparationMaterial($enrollment, $course, $prepMaterial, $currentDate);

        $this->assertTrue($canAccess, 'Student should access prep material from course start date onward');
    }

    private function createSampleCourse(): Course
    {
        $dummyLesson = new Lesson(
            new LessonName('Dummy Lesson'),
            new \DateTimeImmutable('2025-05-14 10:00:00')
        );

        $dummyHomework = new Homework(new HomeworkName('Dummy Homework'));

        return new Course(
            new CourseId('course-1'),
            new CourseName('Sample Course'),
            new \DateTimeImmutable('2025-05-13'),
            new \DateTimeImmutable('2025-06-12'),
            new Lessons($dummyLesson),
            new Homeworks($dummyHomework),
            new PreparationMaterials()
        );
    }
}
