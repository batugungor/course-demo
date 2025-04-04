<?php

namespace Nwire_Dev\LearnManagementSystem\Database;

class DatabaseManager
{
    public static string $enrollment_table = "course_enrollments";
    public static string $progress_table = "course_progress";
    public static string $structure_table = "course_structure";
    public static string $sections_table = "course_structure_sections";
    public static string $questions_table = "course_questions";
    public static string $question_options_table = "course_question_options";
    public static string $submissions_table = "course_submissions";

    private static ?DatabaseManager $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): DatabaseManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createTable(): void
    {
        global $wpdb;

        // Enrollment Table
        $enrollment_table = $wpdb->prefix . self::$enrollment_table;
        $enrollment_sql = "
            CREATE TABLE IF NOT EXISTS $enrollment_table (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT(20) UNSIGNED NOT NULL,
                course_id BIGINT(20) UNSIGNED NOT NULL,
                enrollment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY (user_id, course_id)
            ) {$wpdb->get_charset_collate()};
        ";

        // Progress Table
        $progress_table = $wpdb->prefix . self::$progress_table;
        $progress_sql = "
            CREATE TABLE IF NOT EXISTS $progress_table (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT(20) UNSIGNED NOT NULL,
                lesson_id BIGINT(20) UNSIGNED NOT NULL,
                course_id BIGINT(20) UNSIGNED NOT NULL,
                completed TINYINT(1) DEFAULT 0,
                completion_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY (user_id, lesson_id)
            ) {$wpdb->get_charset_collate()};
        ";

        // Sections Table
        $sections_table = $wpdb->prefix . self::$sections_table;
        $sections_sql = "
            CREATE TABLE IF NOT EXISTS $sections_table (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                course_id BIGINT(20) UNSIGNED NOT NULL,
                section_name VARCHAR(255) NOT NULL,
                section_order INT(11) NOT NULL,
                FOREIGN KEY (course_id) REFERENCES wp_posts(ID) ON DELETE CASCADE
            ) {$wpdb->get_charset_collate()};
        ";

        // Structure Table
        $structure_table = $wpdb->prefix . self::$structure_table;
        $structure_sql = "
            CREATE TABLE IF NOT EXISTS $structure_table (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                course_id BIGINT(20) UNSIGNED NOT NULL,
                lesson_id BIGINT(20) UNSIGNED NOT NULL,
                course_section_id BIGINT(20) UNSIGNED NOT NULL,
                lesson_order INT(11) NOT NULL,
                FOREIGN KEY (course_id) REFERENCES wp_posts(ID) ON DELETE CASCADE,
                FOREIGN KEY (lesson_id) REFERENCES wp_posts(ID) ON DELETE CASCADE,
                FOREIGN KEY (course_section_id) REFERENCES $sections_table(id) ON DELETE CASCADE
            ) {$wpdb->get_charset_collate()};
        ";

        // Questions Table
        $questions_table = $wpdb->prefix . self::$questions_table;
        $questions_sql = "
            CREATE TABLE IF NOT EXISTS $questions_table (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                lesson_id BIGINT(20) UNSIGNED NOT NULL,
                question_text TEXT NOT NULL,
                question_type ENUM('mcq', 'open') DEFAULT 'mcq',
                question_order INT(11) NOT NULL,
                FOREIGN KEY (lesson_id) REFERENCES wp_posts(ID) ON DELETE CASCADE
            ) {$wpdb->get_charset_collate()};
        ";

        // Question Options Table
        $question_options_table = $wpdb->prefix . self::$question_options_table;
        $question_options_sql = "
            CREATE TABLE IF NOT EXISTS $question_options_table (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                question_id BIGINT(20) UNSIGNED NOT NULL,
                option_text TEXT NOT NULL,
                is_correct TINYINT(1) DEFAULT 0,
                FOREIGN KEY (question_id) REFERENCES $questions_table(id) ON DELETE CASCADE
            ) {$wpdb->get_charset_collate()};
        ";

        // Submissions Table
        $submissions_table = $wpdb->prefix . self::$submissions_table;
        $submissions_sql = "
            CREATE TABLE IF NOT EXISTS $submissions_table (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT(20) UNSIGNED NOT NULL,
                lesson_id BIGINT(20) UNSIGNED NOT NULL,
                answers_json LONGTEXT NOT NULL,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE,
                FOREIGN KEY (lesson_id) REFERENCES wp_posts(ID) ON DELETE CASCADE
            ) {$wpdb->get_charset_collate()};
        ";

        // Execute all table creation queries
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($enrollment_sql);
        dbDelta($progress_sql);
        dbDelta($sections_sql);
        dbDelta($structure_sql);
        dbDelta($questions_sql);
        dbDelta($question_options_sql);
        dbDelta($submissions_sql);
    }
}
