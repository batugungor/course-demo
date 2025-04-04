<?php

namespace Nwire_Dev\LearnManagementSystem\Tables;

use app\Http\Tables\DataTable;
use Nwire_Dev\LearnManagementSystem\Database\DatabaseManager;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;
use WP_List_Table;

class CoursesTable extends DataTable
{
    public function __construct($items)
    {
        parent::__construct();

        $this
            ->setBulkActions([
                // Define any bulk actions if needed
            ])
            ->setColumns([
                "cb" => "<input type='checkbox' />",  // Checkbox for selecting rows
                "name" => "Naam",  // Define column names
            ])
            ->setItems($this->mappers($items))
            ->setCheckbox("ID")
            ->generateTable();
    }

    public function mappers($items): array
    {
        $mapping = [];
        foreach ($items as $item) {
            $mapping[] = [
                "ID" => $item->ID,
                "name" => $item->post_title,
                "actions" => $this->setColumnActions($item),  // Add the actions here
            ];
        }

        return $mapping;
    }

    public function setColumnActions($item): string
    {
        $actions = [
            'lessons' => sprintf('
                <span>
                    <a href="%s" data-course-id="%d">Bekijk structuur</a>
                </span>
                ', esc_url(admin_url('admin.php?page=course-manager-structure&course_id=' . $item->ID)), $item->ID
            ),
            'students' => sprintf('
                <span>
                    <a href="%s" data-course-id="%d">Bekijk studenten</a>
                </span>
                ', esc_url(admin_url('admin.php?page=course-manager-structure-students&course_id=' . $item->ID)), $item->ID
            ),
            'edit' => sprintf('
                <span>
                    <a href="%s">Bewerken</a>
                </span>', get_edit_post_link($item->ID)
            ),
            'delete' => sprintf('
                <span class="delete">
                    <a href="%s" class="delete" data-course-id="%d">Verwijderen</a>
                </span>
                ', esc_url(admin_url('admin-post.php?action=delete_course')), $item->ID
            ),
        ];

        return implode(' | ', $actions);
    }

    public function column_default($item, $column_name)
    {
        return match ($column_name) {
            'name' => sprintf(
                '<strong>%s</strong><br /><div class="row-actions">%s</div>',
                esc_html($item['name']),
                $item['actions']
            ),
            default => $column_name,
        };
    }
}

