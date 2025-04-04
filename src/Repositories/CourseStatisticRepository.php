<?php

namespace Nwire_Dev\LearnManagementSystem\Repositories;

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Enums\ProgressStatus;
use Nwire_Dev\LearnManagementSystem\Post_Types\Course;
use Nwire_Dev\LearnManagementSystem\Post_Types\Lesson;
use Timber\Post;
use WP_User;

class CourseStatisticRepository
{
    public static function get_amount_of_enrolled_students(int $course_id)
    {
        global $wpdb;

        $enrollment_table = $wpdb->prefix . DatabaseManager::$enrollment_table;

        $query = $wpdb->prepare(
            "SELECT count(*) as amount_of_users
        FROM $enrollment_table e
        WHERE e.course_id = %d",
            $course_id
        );

        return $wpdb->get_row($query, ARRAY_A);
    }

    public static function get_list_of_enrolled_students(int $course_id, int $page = 1, int $per_page = 10)
    {
        global $wpdb;

        $enrollment_table = $wpdb->prefix . DatabaseManager::$enrollment_table;
        $users_table = $wpdb->prefix . 'users';

        $offset = ($page - 1) * $per_page;

        $query = $wpdb->prepare(
            "SELECT u.ID as user_id, u.user_email    
                   FROM $enrollment_table e
                   INNER JOIN $users_table u ON e.user_id = u.ID
                   WHERE e.course_id = %d
                   LIMIT %d OFFSET %d", $course_id, $per_page, $offset
        );

        $enrolled_students = $wpdb->get_results($query, ARRAY_A);

        foreach ($enrolled_students as &$student) {
            $student['completed_steps'] = CourseProgressRepository::get_total_completed_steps($student['user_id'], $course_id);
        }

        return $enrolled_students;
    }

    public static function get_total_students_finished_course($course_id): int
    {
        global $wpdb;

        $progress_table = $wpdb->prefix . DatabaseManager::$progress_table;
        $structure_table = $wpdb->prefix . DatabaseManager::$structure_table;

        $total_lessons = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
             FROM $structure_table
             WHERE course_id = %d",
                $course_id
            )
        );

        if (!$total_lessons) {
            return 0;
        }

        $completed_users = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(user_id)
             FROM (
                 SELECT user_id
                 FROM $progress_table
                 WHERE course_id = %d AND completed = 1
                 GROUP BY user_id
                 HAVING COUNT(*) = %d
             ) as completed_users",
                $course_id,
                $total_lessons
            )
        );

        return (int)$completed_users ?: 0;
    }
}