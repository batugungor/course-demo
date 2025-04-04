<?php

namespace Nwire_Dev\LearnManagementSystem\Repositories;

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Post_Types\Course;
use WP_Error;

class CourseEnrollmentRepository
{
    public static function create(int $course_id, int $user_id): int
    {
        global $wpdb;
        $table_name = $wpdb->prefix . DatabaseManager::$enrollment_table;

        $wpdb->insert($table_name, [
            'user_id' => $user_id,
            'course_id' => $course_id,
        ]);

        return $wpdb->insert_id;
    }

    public static function check_if_user_is_enrolled(int $course_id, int $user_id): bool
    {
        global $wpdb;
        $table_name = $wpdb->prefix . DatabaseManager::$enrollment_table;

        return (bool)$wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(1) as is_enrolled FROM $table_name WHERE course_id = %d AND user_id = %d",
                $course_id,
                $user_id
            ),
            ARRAY_A
        )["is_enrolled"];
    }

    public static function get_all_courses_where_user_is_enrolled(int $user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . DatabaseManager::$enrollment_table;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT course_id FROM $table_name WHERE user_id = %d",
                $user_id
            ),
            ARRAY_A
        );

        $results = array_column($results, 'course_id');

        if (empty($results))
            return [];

        return get_posts([
            "post__in" => $results,
            "post_type" => "course",
        ]);
    }
}

