<?php

namespace Nwire_Dev\LearnManagementSystem\Post_Types;

use Carbon_Fields\Field\Field;
use Rareloop\Lumberjack\Post;

class Course extends Post
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
        return 'course';
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
                'name' => __('Courses'),
                'singular_name' => __('Course'),
                'add_new_item' => __('Add New Course'),
            ],
            'public' => true,
            'has_archive ' => true,
            'supports' => ['title'],
            'menu_icon' => 'dashicons-columns',
        ];
    }

    public static function getPostTypeCustomFields(): ?array
    {
        return [
            Field::make('checkbox', 'available_for_all', 'Beschikbaar maken voor iedereen'),
//            Field::make('complex', 'structure')
//                ->set_layout('tabbed-vertical')
//                ->add_fields([
//                    Field::make('text', 'title'),
//                    Field::make('association', 'content')
//                        ->set_types([
//                            [
//                                'type' => 'post',
//                                'post_type' => 'lesson',
//                            ],
//                            [
//                                'type' => 'post',
//                                'post_type' => 'quiz',
//                            ]
//                        ])
//                ])

//            Field::make('association', 'structure')
//                ->set_types([
//                    [
//                        'type' => 'post',
//                        'post_type' => 'lesson',
//                    ],
//                    [
//                        'type' => 'post',
//                        'post_type' => 'quiz',
//                    ]
//                ])
        ];
    }
}
