<?php

namespace Nwire_Dev\LearnManagementSystem\Repositories;

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Post_Types\Lesson;
use Timber\Post;

class CourseStructureLessonRepository
{
    public static function create(array $lesson_data, int $course_id, int $section_id): int
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$structure_table;

        $lesson_id = wp_insert_post([
            "post_title" => $lesson_data["post_title"],
            "post_content" => "",
            'post_status' => 'draft',
            'post_type' => Lesson::getPostType(),
        ]);

        $wpdb->insert($table, [
            'course_id' => $course_id,
            'lesson_id' => $lesson_id,
            'lesson_order' => self::get_next_lesson_order($course_id, $section_id),
            'course_section_id' => $section_id
        ]);

        return $lesson_id;
    }

    public static function read(int $course_id, int $lesson_id): array
    {
        return [
            "lesson" => new Post($lesson_id),
            "section" => CourseStructureSectionRepository::get_section_by_course_and_lesson($course_id, $lesson_id)
        ];
    }

    public static function update(int $course_id, int $lesson_id, int $lesson_order, int $new_section_id, ?string $post_title = null): void
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$structure_table;

        if (!is_null($post_title)) {
            wp_update_post([
                "ID" => $lesson_id,
                "post_title" => $post_title,
            ]);
        }

        $wpdb->update($table, [
            'course_section_id' => $new_section_id,
            'lesson_order' => $lesson_order
        ], [
            'course_id' => $course_id,
            'lesson_id' => $lesson_id
        ]);
    }

    public static function delete(int $course_id, int $lesson_id): void
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$structure_table;

        $deleted_from_structure = $wpdb->delete($table, [
            'course_id' => $course_id,
            'lesson_id' => $lesson_id
        ]);

        if ($deleted_from_structure === false) {
            error_log("Error deleting lesson with ID $lesson_id from the structure table.");
            return;
        }

        $deleted_post = wp_delete_post($lesson_id, true);

        if (!$deleted_post) {
            error_log("Error deleting lesson post with ID $lesson_id.");
            return;
        }

        error_log("Successfully deleted lesson with ID $lesson_id from course $course_id.");
    }

    public static function delete_lessons_by_section(int $section_id): void
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$structure_table;

        $wpdb->delete($table, ['course_section_id' => $section_id]);
    }

    private static function get_next_lesson_order(int $course_id, int $section_id): int
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$structure_table;

        $query = $wpdb->prepare("
        SELECT MAX(lesson_order) AS max_lesson_order
        FROM $table
        WHERE course_id = %d AND course_section_id = %d
    ", $course_id, $section_id);

        $result = $wpdb->get_row($query);

        return $result && $result->max_lesson_order ? (int)$result->max_lesson_order + 1 : 1;
    }
}
