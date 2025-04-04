<?php

register_post_type('course', [
        'labels' => [
            'name' => __('Courses'),
            'singular_name' => __('Course'),
            'add_new_item' => __('Add New Course'),
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'has_archive ' => false,
        'supports' => ['title', 'thumbnail', 'editor'],
        'menu_icon' => 'dashicons-columns',
    ]
);