<?php

namespace Nwire_Dev\LearnManagementSystem\ViewModels\Course;

use Nwire_Dev\LearnManagementSystem\Post_Types\Course;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseEnrollmentRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseProgressRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStudentsRepository;
use Rareloop\Lumberjack\Post;

class CourseViewModel
{
    public Course $course;
    public ?\WP_User $user;
    public array $course_structure;
    public array $course_steps;
    public string $video;
    public ?array $current_lesson;
    public array $course_finished_lessons;
    public bool $course_has_content;
    public bool $course_finished_by_user;
    public bool $course_enrolled;
    public bool $user_logged_in;

    public function __construct(Course $course, ?\WP_User $user = null)
    {
        // Set up the student info
        $this->user = $user ?? wp_get_current_user();
        $this->user_logged_in = is_user_logged_in();

        // Set up the course structure
        $this->course = $course;
        $this->course_structure = CourseStructureRepository::get_structure_by_course($this->course->ID, true);

        // Check if user is enrolled
        $this->course_enrolled = CourseEnrollmentRepository::check_if_user_is_enrolled($this->course->ID, $this->user->ID);

        // Initialize the course steps
        $this->course_steps = CourseProgressRepository::get_student_progress_of_course($this->course->ID, $this->user->ID);

        // Initialize the lessons
        $this->current_lesson = CourseStudentsRepository::get_current_content($this->course->ID, $this->user->ID);
        $this->course_finished_lessons = CourseProgressRepository::get_completed_lessons($this->course->ID, $this->user->ID);
        $this->course_has_content = $this->course_steps["total"] === 0;

        if ($this->user_logged_in && $this->user) {
            $this->course_finished_by_user = $this->course_steps["total"] === $this->course_steps["finished"];
        }

        if (!is_null($this->current_lesson)) {
            $this->video = (new Post($this->current_lesson["lesson"]->ID))->post_content;
        }
    }
}