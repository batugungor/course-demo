<?php

namespace Nwire_Dev\LearnManagementSystem\Repositories;

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Enums\ProgressStatus;
use Nwire_Dev\LearnManagementSystem\Post_Types\Lesson;

class CourseStructureRepository
{
    public static function get_structure_by_course(int $course_id, bool $published = false): array
    {
        global $wpdb;
        $sections_table = $wpdb->prefix . DatabaseManager::$sections_table;
        $structure_table = $wpdb->prefix . DatabaseManager::$structure_table;

        $post_status = "p.post_status != 'trash'";

        if ($published)
            $post_status = "p.post_status = 'publish'";

        $query = $wpdb->prepare("
        SELECT 
            s.id AS section_id,
            s.section_name,
            s.section_order,
            cs.lesson_id,
            p.post_title AS lesson_title,
            cs.lesson_order,
            p.post_status AS lesson_status
        FROM $sections_table s
        LEFT JOIN $structure_table cs ON s.id = cs.course_section_id
        LEFT JOIN $wpdb->posts p ON cs.lesson_id = p.ID
        WHERE s.course_id = %d AND $post_status
        ORDER BY s.section_order ASC, cs.lesson_order ASC
    ", $course_id);

        $results = $wpdb->get_results($query);

        $grouped_lessons = [];

        foreach ($results as $row) {
            if (!isset($grouped_lessons[$row->section_id])) {
                $grouped_lessons[$row->section_id] = [
                    'section_id' => $row->section_id,
                    'section_name' => $row->section_name,
                    'section_order' => $row->section_order,
                    'lessons' => [],
                    'lesson_count' => 0
                ];
            }
            if ($row->lesson_id) {
                $grouped_lessons[$row->section_id]['lessons'][] = [
                    'lesson_id' => $row->lesson_id,
                    'lesson_title' => $row->lesson_title,
                    'lesson_order' => $row->lesson_order,
                    'lesson_status' => $row->lesson_status
                ];

                $grouped_lessons[$row->section_id]['lesson_count']++;
            }
        }

        return $grouped_lessons;
    }

    public static function get_total_steps(int $course_id): int
    {
        global $wpdb;
        $structure_table = $wpdb->prefix . DatabaseManager::$structure_table;

        $query = $wpdb->prepare("
            SELECT COUNT(DISTINCT cs.lesson_id) AS lesson_count
            FROM $structure_table cs
            LEFT JOIN $wpdb->posts p ON cs.lesson_id = p.ID
            WHERE course_id = %d AND p.post_status = 'publish';
        ", $course_id);

        return (int) $wpdb->get_row($query)->lesson_count;
    }
}
