<?php

declare(strict_types=1);

namespace Tests\Enrollment\Domain;

use Lms\Enrollment\Domain\Enrollment;
use Lms\Students\Domain\StudentId;
use Lms\Courses\Domain\CourseId;
use PHPUnit\Framework\TestCase;

final class EnrollmentTest extends TestCase
{
    public function test_creates_enrollment_with_valid_dates(): void
    {
        $studentId = new StudentId('student-1');
        $courseId = new CourseId('course-1');
        $startDate = new \DateTimeImmutable('2025-01-01');
        $endDate = new \DateTimeImmutable('2025-01-31');

        $enrollment = new Enrollment($studentId, $courseId, $startDate, $endDate);

        $this->assertTrue($enrollment->studentId()->equals($studentId));
        $this->assertTrue($enrollment->courseId()->equals($courseId));
        $this->assertEquals($startDate, $enrollment->startDate());
        $this->assertEquals($endDate, $enrollment->endDate());
    }

    public function test_throws_exception_when_end_date_is_before_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('End date must be after start date.');

        new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-31'),
            new \DateTimeImmutable('2025-01-01')
        );
    }

    public function test_throws_exception_when_end_date_equals_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('End date must be after start date.');

        $date = new \DateTimeImmutable('2025-01-01');
        new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            $date,
            $date
        );
    }

    public function test_enrollment_is_active_when_current_date_is_within_period(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertTrue($enrollment->isActiveAt(new \DateTimeImmutable('2025-01-15')));
    }

    public function test_enrollment_is_active_on_start_date(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertTrue($enrollment->isActiveAt(new \DateTimeImmutable('2025-01-01')));
    }

    public function test_enrollment_is_active_on_end_date(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertTrue($enrollment->isActiveAt(new \DateTimeImmutable('2025-01-31')));
    }

    public function test_enrollment_is_not_active_before_start_date(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertFalse($enrollment->isActiveAt(new \DateTimeImmutable('2024-12-31')));
    }

    public function test_enrollment_is_not_active_after_end_date(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertFalse($enrollment->isActiveAt(new \DateTimeImmutable('2025-02-01')));
    }

    public function test_enrollment_has_started_when_current_date_is_on_or_after_start_date(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertTrue($enrollment->hasStarted(new \DateTimeImmutable('2025-01-01')));
        $this->assertTrue($enrollment->hasStarted(new \DateTimeImmutable('2025-01-15')));
    }

    public function test_enrollment_has_not_started_when_current_date_is_before_start_date(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertFalse($enrollment->hasStarted(new \DateTimeImmutable('2024-12-31')));
    }

    public function test_enrollment_has_expired_when_current_date_is_after_end_date(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertTrue($enrollment->hasExpired(new \DateTimeImmutable('2025-02-01')));
    }

    public function test_enrollment_has_not_expired_when_current_date_is_on_or_before_end_date(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertFalse($enrollment->hasExpired(new \DateTimeImmutable('2025-01-31')));
        $this->assertFalse($enrollment->hasExpired(new \DateTimeImmutable('2025-01-15')));
    }

    public function test_can_shorten_enrollment_period(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $shortened = $enrollment->shortenTo(new \DateTimeImmutable('2025-01-15'));

        $this->assertEquals(new \DateTimeImmutable('2025-01-15'), $shortened->endDate());
        $this->assertTrue($shortened->isActiveAt(new \DateTimeImmutable('2025-01-15')));
        $this->assertFalse($shortened->isActiveAt(new \DateTimeImmutable('2025-01-16')));
    }

    public function test_can_extend_enrollment_period(): void
    {
        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $extended = $enrollment->extendTo(new \DateTimeImmutable('2025-02-28'));

        $this->assertEquals(new \DateTimeImmutable('2025-02-28'), $extended->endDate());
        $this->assertTrue($extended->isActiveAt(new \DateTimeImmutable('2025-02-15')));
    }

    public function test_cannot_shorten_enrollment_to_before_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $enrollment = new Enrollment(
            new StudentId('student-1'),
            new CourseId('course-1'),
            new \DateTimeImmutable('2025-01-15'),
            new \DateTimeImmutable('2025-01-31')
        );

        $enrollment->shortenTo(new \DateTimeImmutable('2025-01-01'));
    }
}
