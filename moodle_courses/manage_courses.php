<?php

require_once('../../../config.php');
require_once('components/modal.php');
require_login();

initialize_page();
echo $OUTPUT->header();
$helper = new \theme_academi\helper();
$courses = $helper->get_courses_list_by_top_level_category('Courses');
echo display_page($courses, $coursescategory);
echo $OUTPUT->footer();

/**
 * Initializes the page context and resources.
 */
function initialize_page() {
    global $PAGE;

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/theme/academi/moodle_courses/manage_courses.php'));
    $PAGE->set_title(get_string('courses', 'theme_academi'));
}


function display_page($courses, $coursescategory) {
    global $OUTPUT;

    // Include the HTML for the survey management interface

    include(__DIR__ . '/templates/manage_course_header.php');
    if ($courses) {
        include(__DIR__ . '/templates/manage_course_table.php');
    } else {
        echo html_writer::tag('div', 'No Courses Found.', ['class' => 'alert alert-info']);
    }
}