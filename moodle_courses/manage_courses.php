<?php

require_once('../../../config.php');
require_once('components/modal.php');
require_login();

initialize_page();
echo $OUTPUT->header();
$helper = new \theme_academi\helper();
$filters = get_filters();
$courses = $helper->get_courses_list_by_top_level_category('Courses', $filters);
$categories = $helper->get_all_course_categories(null);
echo display_page($courses, $filters, $categories);
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

function get_filters() {
    $search = optional_param('search', '', PARAM_RAW_TRIMMED);
    $coursecategoryid = optional_param('categoryid', '', PARAM_ALPHANUMEXT);
    $createdon = optional_param('createdon', '', PARAM_RAW_TRIMMED);

    return [
        'search' => $search,
        'categoryid' => $coursecategoryid,
        'createdon' => $createdon,
    ];
}


function display_page($courses, $filters, $categories) {
    global $OUTPUT;

    // Include the HTML for the survey management interface

    include(__DIR__ . '/templates/manage_course_header.php');
    if ($courses) {
        include(__DIR__ . '/templates/manage_course_table.php');
    } else {
        echo html_writer::tag('div', 'No Courses Found.', ['class' => 'alert alert-info']);
    }
}