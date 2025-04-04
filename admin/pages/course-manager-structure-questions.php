<?php

use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureLessonRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureSectionRepository;
use Timber\Timber;

function course_manager_structure_questions()
{
    global $wpdb;

    // Table names
    $questions_table = $wpdb->prefix . 'course_questions';
    $options_table = $wpdb->prefix . 'course_question_options';

    // Get lesson ID (passed via GET or POST, for example)
    $lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;

    // Fetch questions and options for the lesson
    $questions = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $questions_table WHERE lesson_id = %d ORDER BY id ASC",
            $lesson_id
        )
    );


    foreach ($questions as $question) {
        $question->options = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $options_table WHERE question_id = %d ORDER BY option_id ASC",
                $question->question_id
            )
        );
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Save questions and options
        if (isset($_POST['questions'])) {
            foreach ($_POST['questions'] as $question_id => $question_data) {
                // Check if it's a new question
                if (strpos($question_id, 'new_') === 0) {
                    $wpdb->insert($questions_table, [
                        'lesson_id' => $lesson_id,
                        'question_text' => sanitize_text_field($question_data['question_text']),
                    ]);
                    $question_id = $wpdb->insert_id; // Get the new question ID
                } else {
                    $wpdb->update($questions_table, [
                        'question_text' => sanitize_text_field($question_data['question_text']),
                    ], ['question_id' => intval($question_id)]);
                }

                // Save options for the question
                if (isset($question_data['options'])) {
                    foreach ($question_data['options'] as $option_id => $option_data) {
                        if (strpos($option_id, 'new_') === 0) {
                            $wpdb->insert($options_table, [
                                'question_id' => intval($question_id),
                                'option_text' => sanitize_text_field($option_data['option_text']),
                                'is_correct' => isset($question_data['correct_option']) && $question_data['correct_option'] == $option_id ? 1 : 0,
                            ]);
                        } else {
                            $wpdb->update($options_table, [
                                'option_text' => sanitize_text_field($option_data['option_text']),
                                'is_correct' => isset($question_data['correct_option']) && $question_data['correct_option'] == $option_id ? 1 : 0,
                            ], ['option_id' => intval($option_id)]);
                        }
                    }
                }
            }
        }

        // Redirect to avoid resubmission
        wp_redirect(add_query_arg('lesson_id', $lesson_id, $_SERVER['REQUEST_URI']));
        exit;
    }

    ob_start(); // Start output buffering for HTML
    ?>

    <div class="wrap">
        <h1>Manage Questions for Lesson: <?php echo esc_html(get_the_title($lesson_id)); ?></h1>
        <form method="post" id="lesson-questions-form">
            <table class="wp-list-table widefat fixed striped table-view-list questions">
                <thead>
                <tr>
                    <th>Question</th>
                    <th>Options</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="sortable-questions">
                <?php foreach ($questions as $question): ?>
                    <tr class="question-row" data-question-id="<?php echo esc_attr($question->question_id); ?>">
                        <td>
                            <input type="text" name="questions[<?php echo esc_attr($question->question_id); ?>][question_text]" value="<?php echo esc_attr($question->question_text); ?>" placeholder="Enter question text">
                        </td>
                        <td>
                            <ul class="question-options" data-question-id="<?php echo esc_attr($question->question_id); ?>">
                                <?php foreach ($question->options as $option): ?>
                                    <li class="option-row">
                                        <input type="text" name="questions[<?php echo esc_attr($question->question_id); ?>][options][<?php echo esc_attr($option->option_id); ?>][option_text]" value="<?php echo esc_attr($option->option_text); ?>" placeholder="Option text">
                                        <label>
                                            <input type="radio" name="questions[<?php echo esc_attr($question->question_id); ?>][correct_option]" value="<?php echo esc_attr($option->option_id); ?>" <?php checked($option->is_correct, 1); ?>>
                                            Correct
                                        </label>
                                        <a href="javascript:void(0)" class="delete-option">Delete</a>
                                    </li>
                                <?php endforeach; ?>
                                <li>
                                    <a href="javascript:void(0)" class="add-option" data-question-id="<?php echo esc_attr($question->question_id); ?>">+ Add Option</a>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="delete-question">Delete Question</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <a href="javascript:void(0)" id="add-question-button" class="button button-primary">+ Add Question</a>
            <input type="submit" class="button button-primary" value="Save Changes">
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const questionsContainer = document.getElementById('sortable-questions');
            const addQuestionButton = document.getElementById('add-question-button');

            // Add a new question
            addQuestionButton.addEventListener('click', () => {
                const newQuestionId = `new_${Date.now()}`;
                const newQuestionRow = `
                <tr class="question-row" data-question-id="${newQuestionId}">
                    <td>
                        <input type="text" name="questions[${newQuestionId}][question_text]" value="" placeholder="Enter question text">
                    </td>
                    <td>
                        <ul class="question-options" data-question-id="${newQuestionId}">
                            <li>
                                <a href="javascript:void(0)" class="add-option" data-question-id="${newQuestionId}">+ Add Option</a>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="delete-question">Delete Question</a>
                    </td>
                </tr>`;
                questionsContainer.insertAdjacentHTML('beforeend', newQuestionRow);
            });

            // Add a new option
            questionsContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('add-option')) {
                    const questionId = e.target.dataset.questionId;
                    const newOptionId = `new_${Date.now()}`;
                    const newOptionRow = `
                    <li class="option-row">
                        <input type="text" name="questions[${questionId}][options][${newOptionId}][option_text]" value="" placeholder="Option text">
                        <label>
                            <input type="radio" name="questions[${questionId}][correct_option]" value="${newOptionId}">
                            Correct
                        </label>
                        <a href="javascript:void(0)" class="delete-option">Delete</a>
                    </li>`;
                    const optionsList = e.target.closest('.question-options');
                    optionsList.insertAdjacentHTML('beforeend', newOptionRow);
                }
            });

            // Delete a question
            questionsContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('delete-question')) {
                    const questionRow = e.target.closest('.question-row');
                    questionRow.remove();
                }
            });

            // Delete an option
            questionsContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('delete-option')) {
                    const optionRow = e.target.closest('.option-row');
                    optionRow.remove();
                }
            });
        });
    </script>

    <?php
    return ob_get_clean(); // Return the buffered content
}

