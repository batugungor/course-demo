<?php
add_action('init', function () {

    wp_register_style('learn-management-system',
        NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_URL . 'assets/css/learn-management-system.css'
    );

    wp_register_style('learn-management-system-index',
        NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_URL . 'assets/css/learn-management-system-index.css'
    );

    wp_register_style('learn-management-system-old',
        NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_URL . 'assets/css/learn-management-system-old.css'
    );

    wp_register_style('swiper',
        NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_URL . 'assets/css/swiper-bundle.min.css'
    );

    wp_register_script('swiper',
        NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_URL . 'assets/js/swiper-bundle.min.js'
    );

    wp_register_script('learn-management-system-course',
        NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_URL . 'assets/js/learn-management-system-course.js'
    );

});
