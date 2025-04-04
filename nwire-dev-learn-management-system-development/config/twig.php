<?php

add_filter('timber/loader/loader', function ($loader) {
    $loader->addPath(NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_PATH . '/views', NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_NAME);
    return $loader;
});