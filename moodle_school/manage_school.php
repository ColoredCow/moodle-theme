<?php

require_once('../../../config.php');
require_login();
if (!has_capability('local/moodle_survey:view-school', context_system::instance())) {
    redirect(new moodle_url('/'));
}
initialize_page();
echo $OUTPUT->header();
$helper = new \theme_academi\helper();
$schools = $helper->get_school_list();
echo display_page($schools);
echo $OUTPUT->footer();

/**
 * Initializes the page context and resources.
 */
function initialize_page() {
    global $PAGE;

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/theme/academi/moodle_moocs/manage_school.php'));
    $PAGE->set_title(get_string('school', 'theme_academi'));
}

function display_page($schools) {
    global $OUTPUT, $SESSION;

    // Include the HTML for the survey management interface

    include(__DIR__ . '/templates/manage_school_header.php');
    if ($schools) {
        include(__DIR__ . '/templates/manage_school_table.php');
    } else {
        echo html_writer::tag('div', 'No MOOCs Found.', ['class' => 'alert alert-info']);
    }
}