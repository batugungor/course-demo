<?php

namespace Nwire_Dev\LearnManagementSystem\Repositories;

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;

class CourseStructureSectionRepository
{
    public static function read(int $id): ?array
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$sections_table;

        $query = $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id);

        $result = $wpdb->get_row($query, ARRAY_A);

        return $result ?: null;
    }

    public static function create(int $course_id, string $section_name, int $section_order = null): int
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$sections_table;

        if ($section_order === null) {
            $section_order = self::get_next_section_order($course_id);
        }

        $wpdb->insert($table, [
            'course_id' => $course_id,
            'section_name' => $section_name,
            'section_order' => $section_order
        ]);

        return $wpdb->insert_id;
    }

    public static function update(int $section_id, array $data): void
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$sections_table;

        $wpdb->update($table, $data, ['id' => $section_id]);
    }

    public static function delete(int $section_id): void
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$sections_table;

        $wpdb->delete($table, ['id' => $section_id]);

        CourseStructureLessonRepository::delete_lessons_by_section($section_id);
    }


    private static function get_next_section_order(int $course_id): int
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$sections_table;

        $query = $wpdb->prepare("
            SELECT MAX(section_order) + 1 AS next_section_order
            FROM $table
            WHERE course_id = %d
        ", $course_id);

        $result = $wpdb->get_row($query);

        return $result ? $result->next_section_order : 1;
    }

    public static function update_course_section_order(int $course_id, int $section_id, int $new_order): void
    {
        global $wpdb;
        $table = $wpdb->prefix . DatabaseManager::$sections_table;

        $wpdb->update($table, [
            'section_order' => $new_order,
        ], [
            'course_id' => $course_id,
            'id' => $section_id
        ]);
    }

    public static function get_section_by_course_and_lesson(int $course_id, int $lesson_id): array
    {
        global $wpdb;
        $sections_table_name = $wpdb->prefix . DatabaseManager::$sections_table;
        $structure_table_name = $wpdb->prefix . DatabaseManager::$structure_table;

        $query = $wpdb->prepare(
            "SELECT s.*, st.*
             FROM $structure_table_name st
             INNER JOIN $sections_table_name s ON st.course_section_id = s.id
             WHERE st.course_id = %d AND st.lesson_id = %d", $course_id, $lesson_id
        );

        $result = $wpdb->get_row($query, ARRAY_A);

        return $result ?: [];
    }
}