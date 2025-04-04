<?php

namespace Nwire_Dev\LearnManagementSystem\Post_Types;

use Carbon_Fields\Container\Container;
use Carbon_Fields\Field\Field;
use Rareloop\Lumberjack\Post;

class Lesson extends Post
{
    /**
     * Return the key used to register the post type with WordPress
     * First parameter of the `register_post_type` function:
     * https://codex.wordpress.org/Function_Reference/register_post_type
     *
     * @return string
     */
    public static function getPostType(): string
    {
        return 'course_lesson';
    }

    /**
     * Return the config to use to register the post type with WordPress
     * Second parameter of the `register_post_type` function:
     * https://codex.wordpress.org/Function_Reference/register_post_type
     *
     * @return array|null
     */
    protected static function getPostTypeConfig(): ?array
    {
        return [
            'labels' => [
                'name' => __('Lessons'),
                'singular_name' => __('Lesson'),
                'add_new_item' => __('Add New Lesson'),
            ],
            'public' => true,
            'supports' => ['title'],
            'menu_icon' => 'dashicons-book',
            'has_archive ' => false,
        ];
    }

    public static function getPostTypeCustomFields(): ?array
    {
        return [
            Field::make('select', 'lesson_type', __('Type les'))
                ->set_options([
                    "text" => "Text",
                    "video" => "Video",
                ]),
            Field::make('textarea', 'video', __('Youtube video iFrame link'))
                ->set_help_text( 'Voorbeeld van een link:' ),
            Field::make('rich_text', 'lesson_text', 'Inhoud')
                ->set_conditional_logic(
                    [
                        'relation' => 'AND',
                        [
                            'field' => 'lesson_type',
                            'value' => 'text',
                            'compare' => '=',
                        ]
                    ]),
        ];
    }
}
