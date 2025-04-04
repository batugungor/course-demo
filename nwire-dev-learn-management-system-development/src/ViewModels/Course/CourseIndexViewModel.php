<?php

namespace Nwire_Dev\LearnManagementSystem\ViewModels\Course;

use Nwire_Dev\LearnManagementSystem\Repositories\CourseEnrollmentRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseProgressRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;

class CourseIndexViewModel
{
    public array $categories;
    public array $courses;
    public array $enrolled_courses;

    public function __construct(int $user_id)
    {
        $this->categories = get_terms([
            "taxonomy" => "category",
            'exclude' => 1,
        ]);
//        $this->courses =

        if (is_user_logged_in()) {
            $enrolled_courses = CourseEnrollmentRepository::get_all_courses_where_user_is_enrolled($user_id);
            $this->enrolled_courses = [];
            foreach ($enrolled_courses as $enrolled_course) {
                $this->enrolled_courses[] = [
                    "course" => $enrolled_course,
                    "progress" => CourseProgressRepository::get_student_progress_of_course($enrolled_course->ID, $user_id)
                ];

            }
        }

    }

}
