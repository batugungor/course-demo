<?php

use Nwire_Dev\LearnManagementSystem\Repositories\CourseRepository;
use Nwire_Dev\LearnManagementSystem\Repositories\CourseStructureRepository;
use Nwire_Dev\LearnManagementSystem\Tables\CoursesTable;

function course_manager() {

    $table = new CoursesTable(CourseRepository::find_all());
    $table->prepare_items(); // Prepare the data for the table
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Course Lessons</h1>
        <a href="<?php echo admin_url("post-new.php?post_type=course") ?>" class="page-title-action">
            Voeg nieuwe cursus toe
        </a>
        <form method="post">
            <?php
            $table->display(); // Display the table
            ?>
        </form>
    </div>
    <?php
}