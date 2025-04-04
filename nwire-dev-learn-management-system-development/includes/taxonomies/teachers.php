<?php
function custom_course_teacher_taxonomy()
{
    $labels = array(
        'name' => _x('Teachers', 'taxonomy general name'),
        'singular_name' => _x('Teacher', 'taxonomy singular name'),
        'search_items' => __('Search Teachers'),
        'all_items' => __('All Teachers'),
        'parent_item' => __('Parent Teacher'),
        'parent_item_colon' => __('Parent Teacher:'),
        'edit_item' => __('Edit Teacher'),
        'update_item' => __('Update Teacher'),
        'add_new_item' => __('Add New Teacher'),
        'new_item_name' => __('New Teacher Name'),
        'menu_name' => __('Teachers'),
    );

    $args = array(
        'hierarchical' => true, // True for category-like behavior, false for tag-like
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'teachers'),
        'capabilities' => array(
            'manage_terms' => 'edit_posts',
            'edit_terms' => 'edit_posts',
            'delete_terms' => 'edit_posts',
            'assign_terms' => 'edit_posts'
        ),
        'show_in_admin_bar' => true
    );

    register_taxonomy('teacher', array('course'), $args);
}

add_action('init', 'custom_course_teacher_taxonomy');


function custom_teacher_redirect_function()
{
    if (!headers_sent()) { // ✅ Check if headers are already sent
        wp_redirect(admin_url('edit-tags.php?taxonomy=teacher'));
        exit;
    } else {
        echo "<script>window.location.href='" . admin_url('edit-tags.php?taxonomy=teacher') . "';</script>";
        exit;
    }
}