<?php
add_action('admin_menu', function () {
    add_menu_page(
        'Courses',
        'Courses',
        'manage_options',
        'course-manager',
        'course_manager',
        'dashicons-dashboard',
        20
    );

    add_submenu_page(
        'course-manager-afwahwaihwgagioafghawfwahuowah',
        'Courses',
        'Courses',
        'manage_options',
        'course-manager-structure',
        'course_manager_structure',
        null,
    );

    add_submenu_page(
        'course-manager-afwahwaihwgagioafghawfwahuowah',
        'Courses',
        'Courses',
        'manage_options',
        'course-manager-structure-lesson',
        'course_manager_structure_lesson',
        null,
    );

    add_submenu_page(
        'course-manager-afwahwaihwgagioafghawfwahuowah',
        'Courses',
        'Courses',
        'manage_options',
        'course-manager-structure-questions',
        'course_manager_structure_questions',
        null,
    );

    add_submenu_page(
        'course-manager-afwahwaihwgagioafghawfwahuowah',
        'Courses',
        'Courses',
        'manage_options',
        'course-manager-structure-students',
        'course_manager_structure_students',
        null,
    );

    add_submenu_page(
        'course-manager', // Parent menu slug
        'Manage Teachers', // Page title
        'Teachers', // Menu title
        'manage_options', // Capability
        'custom_teacher_redirect', // Menu slug
        'custom_teacher_redirect_function' // Function to handle redirection
    );

    add_submenu_page(
        'course-manager', // Parent menu slug
        'Manage Categories', // Page title
        'Categories', // Menu title
        'manage_options', // Capability
        'custom_categories_redirect', // Menu slug
        'custom_categories_redirect_function' // Function to handle redirection
    );
});