<?php

register_post_type('course_lesson', [
        'labels' => [
            'name' => __('Lessons'),
            'singular_name' => __('Lesson'),
            'add_new_item' => __('Add New Lesson'),
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'show_in_rest' => false,
        'has_archive ' => true,
        'supports' => ['title', 'editor'],
        'menu_icon' => 'dashicons-columns',
    ]
);

add_filter('use_block_editor_for_post_type', 'disable_block_editor_for_page_post_type', 10, 2);

function disable_block_editor_for_page_post_type($use_block_editor, $post_type)
{
    return ('course_lesson' === $post_type) ? false : $use_block_editor;
}