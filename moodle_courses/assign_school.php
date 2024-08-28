<?php

require_once('../../../config.php');
require_login();

$courseid = required_param('course', PARAM_INT);
initialize_page();

echo $OUTPUT->header();
if (!has_capability('local/moodle_survey:assign-course-to-school', context_system::instance())) {
    redirect(new moodle_url('/theme/academi/moodle_courses/manage_courses.php'));
}
echo display_page($courseid);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $helper = new \theme_academi\helper();
    $schooltoassign = $_POST['schools'];
    $alreadyassignedschoolsids = $helper->get_assigned_schools_for_course($courseid);

    foreach ($schooltoassign as $schoolid) {
        $existingmapping = $helper->get_mapping_for_school_course($schoolid, $courseid);
        
        if ($existingmapping) {
            $indextoremove = array_search($schoolid, $alreadyassignedschoolsids);
            
            if ($indextoremove !== false) {
                unset($alreadyassignedschoolsids[$indextoremove]);
            }
            
            $alreadyassignedschoolsids = array_values($alreadyassignedschoolsids);
            continue;
        }

        $schoolcourse = new stdClass();
        $schoolcourse->courseid = $courseid;
        $schoolcourse->companyid = $schoolid;
        $schoolcourse->departmentid = $helper->get_department_for_school($schoolid)->id;
        $helper->assign_course_to_school($schoolcourse);
    }
    $helper->unassign_course_from_school($alreadyassignedschoolsids, $courseid);
    redirect(new moodle_url('/theme/academi/moodle_courses/manage_courses.php'));
}
echo $OUTPUT->footer();

/**
 * Initializes the page context and resources.
 */
function initialize_page() {
    global $PAGE;

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/theme/academi/moodle_courses/assign_school.php'));
    $PAGE->set_title(get_string('courses', 'theme_academi'));
}

function display_page($courseid) {
    include(__DIR__ . '/templates/assign_to_school.php');
}
?>
