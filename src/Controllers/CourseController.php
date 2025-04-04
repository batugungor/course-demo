<?php

namespace Nwire_Dev\LearnManagementSystem\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;
use Nwire_Dev\LearnManagementSystem\Enums\ProgressStatus;
use Nwire_Dev\LearnManagementSystem\Post_Types\Course;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseEnrollmentRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseProgressRepository;
use Nwire_Dev\LearnManagementSystem\ViewModels\Course\CourseIndexViewModel;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;
use Nwire_Dev\LearnManagementSystem\ViewModels\Course\CourseViewModel;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\RedirectResponse;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\Timber;

class CourseController extends BaseController
{
    public function index()
    {
        $context = Timber::get_context();
        $courses = get_posts([
            'post_type' => 'course',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        $course_index_viewmodel = new CourseIndexViewModel(wp_get_current_user()->ID);

        if (is_user_logged_in())
            $context["enrolled_courses"] = $course_index_viewmodel->enrolled_courses;

        $context["categories"] = $course_index_viewmodel->categories;
        $context["courses"] = $courses;

        return new HtmlResponse(
            Timber::compile(
                [
                    'learn-management-system/pages/course/index.twig',
                    NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_VIEWS . '/pages/course/index.twig',
                ], $context
            )
        );
    }

    public function view($course)
    {
        $context = Timber::get_context();

        $course = new Course($course);
        $course_view_model = new CourseViewModel($course, wp_get_current_user());
        $context["course"] = $course_view_model;

        if (!$course_view_model->course_enrolled) {
            if (isset($_GET["apply"]) && $_GET["apply"] == "true" && wp_get_current_user()->ID != 0) {
                CourseEnrollmentRepository::create($course->ID, wp_get_current_user()->ID);
                return new RedirectResponse(get_permalink($course->ID));
            }
            return new HtmlResponse(
                Timber::compile(
                    [
                        'learn-management-system/pages/course/status/apply.twig',
                        NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_VIEWS . '/pages/course/status/apply.twig',
                    ], $context
                )
            );
        }

        if (isset($_GET["lesson_selected"])) {
            $_SESSION["courses"][$course->id] = $_GET["lesson_selected"];
            return new RedirectResponse(get_permalink($course->ID));
        }

        if ($course_view_model->course_finished_by_user && isset($_GET["finish_screen"]) && $_GET["finish_screen"] == "true") {
            unset($_SESSION["courses"][$course->id]);
            return new RedirectResponse(get_permalink($course->ID));
        }

        if (isset($_GET["next_lesson"]) && $_GET["next_lesson"] == "true") {
            unset($_SESSION["courses"][$course->id]);
            return new RedirectResponse(get_permalink($course->ID));
        }

        if (isset($_GET["lesson_completed"]) && $_GET["lesson_completed"] == "true") {
            CourseProgressRepository::set_progress(
                $course->ID,
                wp_get_current_user()->ID,
                $course_view_model->current_lesson["lesson"]->ID,
                ProgressStatus::Finished
            );
            unset($_SESSION["courses"][$course->id]);
            return new RedirectResponse(get_permalink($course->ID));
        }

        if (is_null($course_view_model->current_lesson)) {
            if ($course_view_model->course_has_content) {
                return new HtmlResponse(
                    Timber::compile(
                        [
                            'learn-management-system/pages/course/status/no-content.twig',
                            NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_VIEWS . '/pages/course/status/no-content.twig',
                        ], $context
                    )
                );
            }
            if ($course_view_model->course_finished_by_user) {
                return new HtmlResponse(
                    Timber::compile(
                        [
                            'learn-management-system/pages/course/status/finished.twig',
                            NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_VIEWS . '/pages/course/status/finished.twig',
                        ], $context
                    )
                );
            }
        }

        return new HtmlResponse(
            Timber::compile(
                [
                    'learn-management-system/pages/course/view.twig',
                    NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_VIEWS . '/pages/course/view.twig',
                ], $context
            )
        );
    }

    public function category($category)
    {
        $context = Timber::get_context();
        $context["courses"] = CourseRepository::get_courses_by_category($category, wp_get_current_user()->ID);
        $context["category"] = get_term_by("slug", $category, "category");

        return new HtmlResponse(
            Timber::compile(
                [
                    'learn-management-system/pages/taxonomy/index.twig',
                    NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_VIEWS . '/pages/taxonomy/index.twig',
                ], $context
            )
        );
    }

    public function teacher($category)
    {
        $context = Timber::get_context();
        $context["courses"] = CourseRepository::get_courses_by_teacher($category);

        return new TimberResponse('templates/home.twig', $context);
    }
}
