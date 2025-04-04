<?php

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureLessonRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureSectionRepository;
use Timber\Timber;

function course_manager_structure()
{
    $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;

    if (!$course_id) {
        wp_die('No course ID provided.');
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $handled = [
            "sections" => [],
            "lessons" => []
        ];


        $section_order = 1;

//        dd($_POST);
        foreach ($_POST["section"] as $section_data) {
            $section_name = $section_data['section_name'] ?? null;

            if ($section_name === null) {
                continue;
            }

            if (ctype_digit($section_data['section_id'])) {
                CourseStructureSectionRepository::update($section_data['section_id'], [
                    'section_name' => $section_name
                ]);
                CourseStructureSectionRepository::update_course_section_order($course_id, $section_data['section_id'], $section_order);

                $handled["sections"][] = $section_data["section_id"];
            } else {
                $new_section_id = CourseStructureSectionRepository::create($course_id, $section_name, $section_order);
                $section_data['section_id'] = $new_section_id;
                $handled["sections"][] = $new_section_id;
            }
//            dd($section_data);

            if (!empty($section_data['lessons'])) {
                $order = 1;
                foreach ($section_data['lessons'] as $lesson_key => $lesson_data) {
                    $new_section_id = $lesson_data['section_id'] ?? $section_data['section_id'];

//                    dd($lesson_data['lesson_id']);
                    if (!empty($lesson_data['lesson_id'])) {
                        CourseStructureLessonRepository::update(
                            $course_id,
                            (int)$lesson_data['lesson_id'],
                            $order,
                            $new_section_id,
                            $lesson_data['lesson_name']
                        );

                        $handled["lessons"][] = (int)$lesson_data['lesson_id'];

                    } elseif (strpos($lesson_key, 'new_') === 0) {
                        $new_lesson_id = CourseStructureLessonRepository::create(
                            ['post_title' => $lesson_data['lesson_name']],
                            $course_id,
                            $new_section_id
                        );

                        $handled["lessons"][] = (int)$new_lesson_id;
                    }

                    $order++;
                }
            }

            $section_order++;
        }

        $to_remove = [
            "sections" => [],
            "lessons" => []
        ];


        $course_structure = CourseStructureRepository::get_structure_by_course($course_id);

        foreach ($course_structure as $crs_structure) {
            $crs_structure_found = array_search($crs_structure["section_id"], $handled["sections"]);

            if ($crs_structure_found === false) {
                $to_remove["sections"][] = $crs_structure["section_id"];
            }

            foreach ($crs_structure["lessons"] as $crs_lesson) {
                $crs_lesson_found = array_search($crs_lesson["lesson_id"], $handled["lessons"]);

                if ($crs_lesson_found === false) {
                    $to_remove["lessons"][] = $crs_lesson["lesson_id"];
                }
            }
        }

//        foreach ($to_remove["sections"] as $removed_section) {
//            CourseStructureSectionRepository::delete((int) $removed_section);
//        }
//
//        foreach ($to_remove["lessons"] as $removed_lesson) {
//            CourseStructureLessonRepository::delete($course_id, (int) $removed_lesson);
//        }
    }

    $course = CourseRepository::read($course_id);
    $course_structure = CourseStructureRepository::get_structure_by_course($course_id);

    wp_enqueue_script('course-manager-structure', plugin_dir_url(__FILE__) . 'course-manager-structure.js', ['jquery']);
    wp_enqueue_style('course-manager-structure', plugin_dir_url(__FILE__) . 'course-manager.css');

    $context = Timber::context();
    $context['course'] = $course;
    $context["plugin_url"] = plugin_dir_url(__FILE__);
    $context['course_structure'] = $course_structure;

    Timber::render('course-manager-structure.twig', $context);
}
