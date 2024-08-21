<?php

require_once('../../../config.php');
require_login();

initialize_page();
echo $OUTPUT->header();
$helper = new \theme_academi\helper();
$users = $helper->get_users_list_by_student_teacher();
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

    if (!isset($tab)) {
        $tab = 'student';
    }

    include(__DIR__ . '/templates/manage_users_header.php');
    
    get_students_data($tab, $users);
    get_teachers_data($tab, $users);
}

function get_students_data($tab, $users) {
    $students = [];
    $tabledata = [];
    foreach ($users as $user) {
        if ($user->rolename === 'student') {
            $students[] = $user;
        }
    }
    foreach($students as $student) {
        $tabledata[] = [
            $student->firstname,
            $student->id,
            7,
            'Mrs. Ajitha Kaur',
            5
        ];
    }
    echo html_writer::start_div($tab === 'student' ? 'active' : '', ['id' => 'student']);
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
    foreach ($users as $user) {
        if ($user->rolename === 'teacher') {
            $teachers[] = $user;
        }
    }
    foreach($teachers as $teacher) {
        $tabledata[] = [
            $teacher->firstname,
            $teacher->id,
            7,
            'Physics',
            5
        ];
    }
    echo html_writer::start_div($tab === 'teacher' ? 'active' : '', ['id' => 'teacher']);
    if(!empty($teachers)){
        $tablehead = get_string('teachertablehead', 'theme_academi');
        include(__DIR__ . '/templates/manage_users_table.php');
    } else {
        echo html_writer::tag('div', 'No Data Found.', ['class' => 'alert alert-info']);
    }
    echo html_writer::end_div();
}