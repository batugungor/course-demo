<?php

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStatisticRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureLessonRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureSectionRepository;
use Timber\Timber;

function course_manager_structure_students()
{
    $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;

    if (!$course_id) {
        wp_die('No course ID provided.');
    }

    $course = CourseRepository::read($course_id);

    $context = Timber::context();
    $context['course'] = $course;
    $context["plugin_url"] = plugin_dir_url(__FILE__);

    $context["students"] = CourseStatisticRepository::get_list_of_enrolled_students($course_id, $_GET["pagination"] ?? 1, 10);
    $context["total_steps_in_course"] = CourseStructureRepository::get_total_steps($course_id);
    $context["total_students_in_course"] = CourseStatisticRepository::get_amount_of_enrolled_students($course_id);
    $context["total_students_finished_course"] = CourseStatisticRepository::get_total_students_finished_course($course_id);

    $context["current_url"] = admin_url("admin.php?page=" . $_REQUEST["page"] . "&course_id=" . $course_id);
    $context["current_page"] = $_GET["pagination"] ?? 1;

    Timber::render('course-manager-structure-students.twig', $context);
}
