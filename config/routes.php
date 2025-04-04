<?php

use Rareloop\Lumberjack\Facades\Router;

Router::group(apply_filters('nwire_dev_learn_management_system_route_courses', 'courses'), function ($group) {
    $group->get('/', 'Nwire_Dev\LearnManagementSystem\Controllers\CourseController@index');
});

Router::get(
    apply_filters('nwire_dev_learn_management_system_route_course', 'course') . '/{course}',
    'Nwire_Dev\LearnManagementSystem\Controllers\CourseController@view'
);

Router::get(
    apply_filters('nwire_dev_learn_management_system_route_category', 'category') . '/{category}',
    'Nwire_Dev\LearnManagementSystem\Controllers\CourseController@category'
);

Router::get(
    apply_filters('nwire_dev_learn_management_system_route_teacher', 'teacher') . '/{teacher}',
    'Nwire_Dev\LearnManagementSystem\Controllers\CourseController@teacher'
);