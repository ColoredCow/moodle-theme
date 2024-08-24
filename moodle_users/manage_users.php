<?php

require_once('../../../config.php');
require_login();

initialize_page();
echo $OUTPUT->header();
$helper = new \theme_academi\helper();
if (is_sel_admin()) {
    redirect(new moodle_url('/'));
}
$users = $helper->get_users_list_by_role_for_school();
echo display_page($users);
echo html_writer::end_div();
echo $OUTPUT->footer();

/**
 * Initializes the page context and resources.
 */
function initialize_page() {
    global $PAGE;

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/theme/academi/moodle_users/manage_users.php'));
    $PAGE->set_title(get_string('users', 'theme_academi'));
    $PAGE->requires->js(new moodle_url('/theme/academi/moodle_users/js/tabs.js'));
}

function display_page($users) {
    global $OUTPUT, $SESSION;

    $tab = $_GET['tab'];
    
    if (!isset($tab)) {
        $tab = 'student';
    }
    $context = context_system::instance();
    include(__DIR__ . '/templates/manage_users_header.php');

    switch ($tab) {
        case 'student':
            if (!has_capability('local/moodle_survey:view-student', $context)) {
                redirect(new moodle_url('/'));
            }
            get_students_data($tab, $users);
            break;
        case 'teacher':
            if (!has_capability('local/moodle_survey:view-teacher', $context)) {
                redirect(new moodle_url('/'));
            }
            get_teachers_data($tab, $users);
            break;
        case 'counsellor':
            if (!has_capability('local/moodle_survey:view-counsellor', $context)) {
                redirect(new moodle_url('/'));
            }
            get_counsellors_data($tab, $users);
            break;
        case 'principal':
            if (!has_capability('local/moodle_survey:view-principal', $context)) {
                redirect(new moodle_url('/'));
            }
            get_principals_data($tab, $users);
            break;
    }
}

function get_students_data($tab, $users) {
    $students = [];
    $tabledata = [];
    echo html_writer::start_div($tab === 'student' ? 'active' : '', ['id' => 'student']);
        foreach ($users as $user) {
            if ($user->rolename === 'student') {
                $students[] = $user;
            }
        }
        foreach($students as $student) {
            $tabledata[] = [
                $student->firstname . ' ' . $student->lastname,
                $student->idnumber,
                '-',
                '-',
                '-'
            ];
        }
        if(!empty($students)){
            $tablehead = get_string('studenttablehead', 'theme_academi');
            include(__DIR__ . '/templates/manage_users_table.php');
        } else {
            echo html_writer::tag('div', 'No Data Found.', ['class' => 'alert alert-info']);
        }
    echo html_writer::end_div();
}

function get_teachers_data($tab, $users) {
    $teachers = [];
    $tabledata = [];
    echo html_writer::start_div($tab === 'teacher' ? 'active' : '', ['id' => 'teacher']);
        foreach ($users as $user) {
            if ($user->rolename === 'teacher') {
                $teachers[] = $user;
            }
        }
        foreach($teachers as $teacher) {
            $tabledata[] = [
                $teacher->firstname . ' ' . $teacher->lastname,
                $teacher->idnumber,
                '-',
                '-',
                '-'
            ];
        }
        if(!empty($teachers)){
            $tablehead = get_string('teachertablehead', 'theme_academi');
            include(__DIR__ . '/templates/manage_users_table.php');
        } else {
            echo html_writer::tag('div', 'No Data Found.', ['class' => 'alert alert-info']);
        }
    echo html_writer::end_div();
}

function get_counsellors_data($tab, $users) {
    $counsellors = [];
    $tabledata = [];
    echo html_writer::start_div($tab === 'teacher' ? 'active' : '', ['id' => 'teacher']);
    foreach ($users as $user) {
        if ($user->rolename === 'counsellor') {
            $counsellors[] = $user;
        }
    }
    foreach($counsellors as $counsellor) {
        $tabledata[] = [
            $counsellor->firstname . ' ' . $counsellor->lastname,
            $counsellor->idnumber,
            '-'
        ];
    }
    if(!empty($counsellors)){
        $tablehead = get_string('counsellortablehead', 'theme_academi');
        include(__DIR__ . '/templates/manage_users_table.php');
    } else {
        echo html_writer::tag('div', 'No Data Found.', ['class' => 'alert alert-info']);
    }
    echo html_writer::end_div();
}

function get_principals_data($tab, $users) {
    $counsellors = [];
    $tabledata = [];
    echo html_writer::start_div($tab === 'teacher' ? 'active' : '', ['id' => 'teacher']);
    foreach ($users as $user) {
        if ($user->rolename === 'principal') {
            $counsellors[] = $user;
        }
    }
    foreach($counsellors as $counsellor) {
        $tabledata[] = [
            $counsellor->firstname . ' ' . $counsellor->lastname,
            $counsellor->idnumber,
        ];
    }
    if(!empty($counsellors)){
        $tablehead = get_string('principaltablehead', 'theme_academi');
        include(__DIR__ . '/templates/manage_users_table.php');
    } else {
        echo html_writer::tag('div', 'No Data Found.', ['class' => 'alert alert-info']);
    }
    echo html_writer::end_div();
}