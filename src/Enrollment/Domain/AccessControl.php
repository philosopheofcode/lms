<?php

declare(strict_types=1);

namespace Lms\Enrollment\Domain;

use Lms\Courses\Domain\Course;
use Lms\Courses\Domain\Lesson;
use Lms\Courses\Domain\Homework;
use Lms\Courses\Domain\PreparationMaterial;

final class AccessControl
{
    /**
     * Check if a student can access a lesson
     * Rules:
     * - Student must be currently enrolled in the course
     * - Course must have started
     * - Lesson must be available from its scheduled datetime
     */
    public function canAccessLesson(
        Enrollment $enrollment,
        Course $course,
        Lesson $lesson,
        \DateTimeImmutable $currentDate
    ): bool {
        if (!$enrollment->isActiveAt($currentDate)) {
            return false;
        }

        if (!$course->hasStarted($currentDate)) {
            return false;
        }

        if ($currentDate < $lesson->scheduledAt()) {
            return false;
        }

        return true;
    }

    /**
     * Check if a student can access homework
     * Rules:
     * - Student must be currently enrolled in the course
     * - Course must have started
     * - Homework is available from course start onward
     */
    public function canAccessHomework(
        Enrollment $enrollment,
        Course $course,
        Homework $homework, //TODO there are no rules for now, check with business
        \DateTimeImmutable $currentDate
    ): bool {
        if (!$enrollment->isActiveAt($currentDate)) {
            return false;
        }

        if (!$course->hasStarted($currentDate)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a student can access preparation material
     * Rules:
     * - Student must be currently enrolled in the course
     * - Course must have started
     * - Preparation material is available from course start onward
     */
    public function canAccessPreparationMaterial(
        Enrollment $enrollment,
        Course $course,
        PreparationMaterial $preparationMaterial,//TODO there are no rules for now, check with business
        \DateTimeImmutable $currentDate
    ): bool {
        if (!$enrollment->isActiveAt($currentDate)) {
            return false;
        }

        if (!$course->hasStarted($currentDate)) {
            return false;
        }

        return true;
    }

}
