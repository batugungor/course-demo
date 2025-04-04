<?php

namespace Nwire_Dev\LearnManagementSystem\Repositories;

use Nwire_Dev\LearnManagementSystem\Enums\ProgressStatus;
use Nwire_Dev\LearnManagementSystem\Post_Types\Course;
use Nwire_Dev\LearnManagementSystem\Post_Types\Lesson;
use Timber\Post;
use WP_User;

class CourseStudentsRepository
{
    public static function get_current_content(int $course_id, int $user_id): ?array
    {
        if (isset($_SESSION["courses"][$course_id])) {
            return CourseStructureLessonRepository::read($course_id, $_SESSION["courses"][$course_id]);
        }

        return CourseProgressRepository::get_next_incomplete_lesson($course_id, $user_id);
    }
}