<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Lms\Courses\Domain\Course;

$course = new Course('1', 'PHP 101 Course');

echo $course;
