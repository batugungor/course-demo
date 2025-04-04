<?php

namespace Nwire_Dev\LearnManagementSystem\Post_Types;

use Carbon_Fields\Container\Container;
use Carbon_Fields\Field\Field;
use Rareloop\Lumberjack\Post;

class Quiz extends Post
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
        return 'quiz';
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
                'name' => __('Quizzes'),
                'singular_name' => __('Quiz'),
                'add_new_item' => __('Add New Quiz'),
            ],
            'public' => true,
            'has_archive ' => false,
            'supports' => ['title'],
            'menu_icon' => 'dashicons-editor-insertmore',
        ];
    }

    public static function getPostTypeCustomFields(): ?array
    {
        return [
            Field::make( 'complex', 'questions', 'Vragen' )
                ->setup_labels([
                    'plural_name' => 'questions',
                    'singular_name' => 'question',
                ])
                ->set_layout('grid')
                ->add_fields('question_list', [
                    Field::make( 'text', 'question', 'De vraag')->set_help_text('De vraag' ),
                    Field::make('select', 'question_type', __('Type vraag'))
                        ->set_options([
                            "text" => "Open vraag",
                            "multiple" => "Meerkeuze vraag",
                        ]),
                    Field::make( 'complex', 'multiple_choice', 'Meerkeuze vraag')
                        ->add_fields([
                            Field::make( 'text', 'multiple_choice_choice', __('Keuze') ),
                            Field::make( 'checkbox', 'multiple_choice_choice_correct', __( 'Correcte antwoord' ) )
                                ->set_option_value("true")
                        ])
                        ->set_layout('tabbed-vertical')
//                        ->set_conditional_logic(
//                            [
//                                'relation' => 'AND',
//                                [
//                                    'field' => 'question_type',
//                                    'value' => 'multiple_choice',
//                                    'compare' => '=',
//                                ]
//                            ]),
                ])
        ];
    }
}
