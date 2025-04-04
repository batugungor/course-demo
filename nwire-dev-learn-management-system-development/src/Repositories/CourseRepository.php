<?php

namespace Nwire_Dev\LearnManagementSystem\Repositories;

use Nwire_Dev\LearnManagementSystem\Post_Types\Course;
use WP_Error;

class CourseRepository
{
    public static function create($data): WP_Error|int
    {
        return wp_insert_post($data);
    }

    public static function read(int $course_id): Course
    {
        return new Course($course_id);
    }

    public static function update($data): WP_Error|int
    {
        return wp_update_post($data);
    }

    public static function delete(int $course_id): bool|array|\WP_Post|null
    {
        return wp_delete_post($course_id);
    }

    public static function find_all(): array
    {
        return get_posts([
            "post_type" => "course"
        ]);
    }

    public static function get_courses_by_category(string $category, int $user_id = null): array
    {
        $courses = [];

        $results = get_posts([
            'post_type' => 'course',
            'numberposts' => -1,
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field' => 'name',
                    'terms' => $category,
                    'include_children' => false
                ]
            ]
        ]);

        foreach ($results as $result) {
            $to_add = [];

            $to_add["course"] = $result;

            if (!is_null($user_id)) {
                $is_enrolled = CourseEnrollmentRepository::check_if_user_is_enrolled($result->ID, $user_id);
                if ($is_enrolled)
                    $to_add["progress"] = CourseProgressRepository::get_student_progress_of_course($result->ID, $user_id);
            }

            $courses[] = $to_add;
        }

        return $courses;
    }

    public static function get_courses_by_teacher(string $teacher): array
    {
        return get_posts(array(
            'post_type' => 'course',
            'numberposts' => -1,
            'tax_query' => [
                [
                    'taxonomy' => 'teacher',
                    'field' => 'name',
                    'terms' => $teacher,
                    'include_children' => false
                ]
            ]
        ));
    }
}
