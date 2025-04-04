<?php

/**
 * Plugin Name: NWIRE.DEV Learn Management System
 * Description: The default plugin
 * Version: 1.0
 * Author: Batuhan Güngör
 */

use App\Http\Lumberjack;
use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;

spl_autoload_register(function ($class) {
    if (!defined('NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM'))
        define('NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM', 'Nwire_Dev\LearnManagementSystem\\');

    $len = strlen(NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM);
    if (strncmp(NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }

});

add_action('after_setup_theme', function () {
    define('NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_NAME', "NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM");
    define('NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_PATH', plugin_dir_path(__FILE__));
    define('NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_URL', plugin_dir_url(__FILE__));
    define('NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_VIEWS', '@' . NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_NAME);
    define('NWIRE_DEV_LEARN_MANAGEMENT_SYSTEM_VIEWS_THEME', '/learn-management-system');

    Lumberjack::recursive_loader(__DIR__ . '/admin');
    Lumberjack::recursive_loader(__DIR__ . '/config');
    Lumberjack::recursive_loader(__DIR__ . '/includes');
    Lumberjack::recursive_loader(__DIR__ . '/views');
}, 0, 20);

register_activation_hook(__FILE__, function () {
    $db_manager = DatabaseManager::getInstance();
    $db_manager->createTable();
});