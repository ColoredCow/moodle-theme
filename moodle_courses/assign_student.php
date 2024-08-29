<?php

require_once('../../../config.php');
require_login();

$courseid = required_param('course', PARAM_INT);
initialize_page();

echo $OUTPUT->header();
if (!has_capability('local/moodle_survey:assign-course-to-user', context_system::instance())) {
    redirect(new moodle_url('/theme/academi/moodle_courses/manage_courses.php'));
}
echo display_page($courseid);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $USER;
    $helper = new \theme_academi\helper();
    $gradestoassign = $_POST['studentgrades'] ?? [];
    $schoolid = $_POST['schoolid'];
    $coursegrademapping = $helper->get_assigned_school_grades_for_course($courseid, $schoolid);
    
    $coursegrade = new stdClass();
    $coursegrade->user_grade = json_encode($gradestoassign);
    if(!$coursegrademapping) {
        $coursegrade->school_id = $schoolid;
        $coursegrade->course_id = $courseid;
        $coursegrade->audience_type = "student";
        $helper->create_school_course_grade($coursegrade);
    } else {
        $coursegrade->id = $coursegrademapping->id;
        $helper->update_school_course_grade($coursegrade);
    }

    $enrol = $helper->get_or_create_course_enrol($courseid);
    $existingenrollments = $helper->get_user_enrollment_ids($enrol->id);
    $eligiblestudents = $helper->get_students_eligible_for_course($courseid, $schoolid, $gradestoassign);

    foreach ($eligiblestudents as $user) {
        $existingentry = $helper->get_user_enrol($user->user_id, $enrol->id);
        if ( $existingentry) {
            $indextoremove = array_search($user->user_id, $existingenrollments);
            unset($existingenrollments[$indextoremove]);
            continue;
        }
        $userenrol = new stdClass();
        $userenrol->status = 0;
        $userenrol->enrolid = $enrol->id;
        $userenrol->timestart = time();
        $userenrol->userid = $user->user_id;
        $userenrol->modifierid = $USER->id;
        $helper->create_user_enrol($userenrol);
    }

    $helper->unenroll_users($enrol->id, $existingenrollments);
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
    $PAGE->set_url(new moodle_url('/theme/academi/moodle_courses/assign_student.php'));
    $PAGE->set_title(get_string('courses', 'theme_academi'));
}

function display_page($courseid) {
    include(__DIR__ . '/templates/assign_to_student.php');
}
?>
