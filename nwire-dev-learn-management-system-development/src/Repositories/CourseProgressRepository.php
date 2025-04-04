<?php

namespace Nwire_Dev\LearnManagementSystem\Repositories;

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Enums\ProgressStatus;
use Nwire_Dev\LearnManagementSystem\Post_Types\Course;
use WP_Error;

class CourseProgressRepository
{
    public static function create(int $user_id, int $lesson_id, int $course_id, ?ProgressStatus $completed = null, ?string $completion_date = null): int
    {
        global $wpdb;
        $table_name = $wpdb->prefix . DatabaseManager::$progress_table;

        if (empty($completion_date)) {
            $completion_date = (new \DateTime())->format('Y-m-d H:i:s');
        }

        $wpdb->insert($table_name, [
            'user_id' => $user_id,
            'lesson_id' => $lesson_id,
            'course_id' => $course_id,
            'completed' => $completed == ProgressStatus::Finished ? ProgressStatus::Finished->value : ProgressStatus::InProgress->value,
            'completion_date' => $completion_date,
        ]);

        return $wpdb->insert_id;
    }


    public static function read(int $user_id, int $lesson_id): ?array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . DatabaseManager::$progress_table;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND lesson_id = %d",
                $user_id,
                $lesson_id
            ),
            ARRAY_A
        );
    }

    public static function update(int $user_id, int $lesson_id, ProgressStatus $completed, ?string $completion_date = null): bool
    {
        global $wpdb;
        $table_name = $wpdb->prefix . DatabaseManager::$progress_table;

        $updated = $wpdb->update(
            $table_name,
            [
                'completed' => $completed->value,
                'completion' => $completion_date,
            ],
            [
                'user_id' => $user_id,
                'lesson_id' => $lesson_id,
            ]
        );


        return $updated !== false;
    }

    public static function delete(int $user_id, int $lesson_id): bool
    {
        global $wpdb;
        $table_name = $wpdb->prefix . DatabaseManager::$progress_table;

        $deleted = $wpdb->delete($table_name, [
            'user_id' => $user_id,
            'lesson_id' => $lesson_id,
        ]);

        return $deleted !== false;
    }

    public static function get_completed_lessons(int $course_id, int $user_id,): array
    {
        global $wpdb;

        $table_name = $wpdb->prefix . DatabaseManager::$progress_table;

        $query = $wpdb->prepare(
            "SELECT lesson_id FROM $table_name WHERE user_id = %d AND course_id = %d AND completed = %d",
            $user_id, $course_id, ProgressStatus::Finished->value
        );

        $results = $wpdb->get_results($query);

        return array_map(function ($row) {
            return (int)$row->lesson_id;
        }, $results);
    }

    public static function set_progress(int $course_id, int $user_id, int $lesson_id, ?ProgressStatus $status = null): void
    {
        $lesson = self::read($user_id, $lesson_id);

        if (is_null($lesson)) {
            self::create(
                $user_id,
                $lesson_id,
                $course_id,
                $status == ProgressStatus::Finished ? ProgressStatus::Finished : ProgressStatus::InProgress,
            );

            return;
        }

        self::update(
            $user_id,
            $lesson_id,
            $status == ProgressStatus::Finished ? ProgressStatus::Finished : ProgressStatus::InProgress
        );
    }


    public static function get_total_completed_steps(int $user_id, int $course_id): int
    {
        global $wpdb;
        $table_name = $wpdb->prefix . DatabaseManager::$progress_table;

        return (int)$wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND course_id = %d AND completed = 1",
                $user_id,
                $course_id
            )
        );
    }

    public static function get_next_incomplete_lesson(int $course_id, int $user_id): ?array
    {
        $course_structure = CourseStructureRepository::get_structure_by_course($course_id, true);

        $completed_lessons = CourseProgressRepository::get_completed_lessons($course_id, $user_id);

        foreach ($course_structure as $section) {
            foreach ($section["lessons"] as $lesson) {
                if (!in_array((int)$lesson['lesson_id'], $completed_lessons, true)) {
                    return CourseStructureLessonRepository::read($course_id, (int)$lesson["lesson_id"]);
                }
            }
        }

        return null;
    }

    public static function get_student_progress_of_course(int $course_id, int $user_id): array
    {
        $course_steps = [
            "total" => CourseStructureRepository::get_total_steps($course_id),
            "finished" => 0,
            "percentage" => 0
        ];

        if ($course_steps["total"] > 0) {
            $course_steps["finished"] = CourseProgressRepository::get_total_completed_steps($user_id, $course_id);
            $course_steps["percentage"] = round($course_steps["finished"] / $course_steps["total"] * 100, 2);
        }

        return $course_steps;
    }
}
