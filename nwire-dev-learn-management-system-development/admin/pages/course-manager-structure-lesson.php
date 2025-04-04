<?php

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureLessonRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureSectionRepository;
use Timber\Timber;

function course_manager_structure_lesson()
{
    $lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : null;

    if (!$lesson_id) {
        wp_die('No course ID provided.');
    }

//    $course = CourseStructureLessonRepository::read();
    $course_structure = CourseStructureRepository::get_structure_by_course($lesson_id);

    wp_enqueue_script('course-manager-structure', plugin_dir_url(__FILE__) . 'course-manager-structure.js', ['jquery']);
    wp_enqueue_style('course-manager-structure', plugin_dir_url(__FILE__) . 'course-manager.css');

    $context = Timber::context();
    $context["plugin_url"] = plugin_dir_url(__FILE__);
    $context['course_structure'] = $course_structure;

    Timber::render('course-manager-structure-lesson.twig', $context);
}
