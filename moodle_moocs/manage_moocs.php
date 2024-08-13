<?php

require_once('../../../config.php');
require_login();

initialize_page();
echo $OUTPUT->header();
$helper = new \theme_academi\helper();
$mooccategory = $helper->get_top_level_category_by_name('MOOCs');
$moocs = $helper->get_courses_list_by_category_id($mooccategory->id);
echo display_page($moocs, $mooccategory);
echo $OUTPUT->footer();

/**
 * Initializes the page context and resources.
 */
function initialize_page() {
    global $PAGE;

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/theme/academi/moodle_moocs/manage_moocs.php'));
    $PAGE->set_title('MOOCs');
}

function display_page($moocs, $mooccategory) {
    global $OUTPUT;

    // Include the HTML for the survey management interface

    include(__DIR__ . '/templates/manage_moocs_header.php');
    if ($moocs) {
        include(__DIR__ . '/templates/manage_moocs_table.php');
    } else {
        echo html_writer::tag('div', 'No MOOCs Found.', ['class' => 'alert alert-info']);
    }
}