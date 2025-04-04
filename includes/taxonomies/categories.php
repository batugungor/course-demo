<?php
function custom_course_category_taxonomy() {
    $labels = array(
        'name'              => _x( 'Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Categories' ),
        'all_items'         => __( 'All Categories' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Categories' ),
    );

    $args = array(
        'hierarchical'      => true, // True for category-like behavior, false for tag-like
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'category' ),
        'capabilities'      => array(
            'manage_terms'  => 'edit_posts',
            'edit_terms'    => 'edit_posts',
            'delete_terms'  => 'edit_posts',
            'assign_terms'  => 'edit_posts'
        )
    );

    register_taxonomy( 'category', array( 'course' ), $args );
}

add_action( 'init', 'custom_course_category_taxonomy' );


function custom_categories_redirect_function()
{
    if (!headers_sent()) { // ✅ Check if headers are already sent
        wp_redirect(admin_url('edit-tags.php?taxonomy=category'));
        exit;
    } else {
        echo "<script>window.location.href='" . admin_url('edit-tags.php?taxonomy=category') . "';</script>";
        exit;
    }
}