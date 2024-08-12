<?php

require_once('../../../config.php');
require_login();

initialize_page();
echo $OUTPUT->header();
echo $OUTPUT->footer();

/**
 * Initializes the page context and resources.
 */
function initialize_page() {
    global $PAGE;

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/theme/academi/moodle_courses/manage_courses.php'));
    $PAGE->set_title('Courses');
    $PAGE->set_heading("Courses");
}
