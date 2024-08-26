<?php

require_once('../../../config.php');
require_login();

initialize_page();
echo $OUTPUT->header();
$helper = new \theme_academi\helper();
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
    global $PAGE;
    $helper = new \theme_academi\helper();
    $tab = $_GET['tab'];
    $availabletabs = ['student', 'teacher', 'counsellor', 'school_admin', 'principal'];
    if (!in_array($tab, $availabletabs)) {
        $tab = is_sel_admin() ? 'school_admin' : 'student';
    }
    $context = context_system::instance();
    include(__DIR__ . '/templates/manage_users_header.php');

    switch ($tab) {
        case 'student':
            if (is_sel_admin() && !has_capability('local/moodle_survey:view-student', $context)) {
                redirect(new moodle_url('/'));
            }
            get_students_data($tab, $users);
            break;
        case 'teacher':
            if (is_sel_admin() &&!has_capability('local/moodle_survey:view-teacher', $context)) {
                redirect(new moodle_url($PAGE->url, ['tab' => 'student']));
            }
            get_teachers_data($tab, $users);
            break;
        case 'counsellor':
            if (is_sel_admin() &&!has_capability('local/moodle_survey:view-counsellor', $context)) {
                redirect(new moodle_url($PAGE->url, ['tab' => 'student']));
            }
            get_counsellors_data($tab, $users);
            break;
        case 'principal':
            if (is_sel_admin() &&!has_capability('local/moodle_survey:view-principal', $context)) {
                redirect(new moodle_url($PAGE->url, ['tab' => 'student']));
            }
            get_principals_data($tab, $users);
            break;
        case 'school_admin':
            if (!has_capability('local/moodle_survey:view-school-admin', $context)) {
                redirect(new moodle_url($PAGE->url, ['tab' => 'student']));
            }
            get_school_admins_data($tab, $helper);
            break;
    }
}

function get_students_data($tab, $users) {
    $context = context_system::instance();
    $students = [];
    $tabledata = [];
    echo html_writer::start_div($tab === 'student' ? 'active' : '', ['id' => 'student']);
        foreach ($users as $user) {
            if ($user->rolename === 'student') {
                $students[] = $user;
            }
        }
        foreach($students as $student) {
            $name = $student->firstname . ' ' . $student->lastname;
            if (has_capability('local/moodle_survey:create-student', $context)) {
                $editurl = new moodle_url('/theme/academi/moodle_users/edit/edit_student.php', ['id' => $student->id]);
                $name = html_writer::link($editurl, $name);
            }
            $tabledata[] = [
                $name,
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
    $context = context_system::instance();
    $teachers = [];
    $tabledata = [];
    echo html_writer::start_div($tab === 'teacher' ? 'active' : '', ['id' => 'teacher']);
        foreach ($users as $user) {
            if ($user->rolename === 'teacher') {
                $teachers[] = $user;
            }
        }
        foreach($teachers as $teacher) {
            $name = $teacher->firstname . ' ' . $teacher->lastname;
            if (has_capability('local/moodle_survey:create-teacher', $context)) {
                $editurl = new moodle_url('/theme/academi/moodle_users/edit/edit_teacher.php', ['id' => $teacher->id]);
                $name = html_writer::link($editurl, $name);
            }
            $tabledata[] = [
                $name,
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
    $context = context_system::instance();
    $counsellors = [];
    $tabledata = [];
    echo html_writer::start_div($tab === 'teacher' ? 'active' : '', ['id' => 'teacher']);
    foreach ($users as $user) {
        if ($user->rolename === 'counsellor') {
            $counsellors[] = $user;
        }
    }
    foreach($counsellors as $counsellor) {
        $name = $counsellor->firstname . ' ' . $counsellor->lastname;
        if (has_capability('local/moodle_survey:create-counsellor', $context)) {
            $editurl = new moodle_url('/theme/academi/moodle_users/edit/edit_counsellor.php', ['id' => $counsellor->id]);
            $name = html_writer::link($editurl, $name);
        }
        $tabledata[] = [
            $name,
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
    $context = context_system::instance();
    $principals = [];
    $tabledata = [];
    echo html_writer::start_div($tab === 'teacher' ? 'active' : '', ['id' => 'teacher']);
    foreach ($users as $user) {
        if ($user->rolename === 'principal') {
            $principals[] = $user;
        }
    }
    foreach($principals as $principal) {
        $name = $principal->firstname . ' ' . $principal->lastname;
        if (has_capability('local/moodle_survey:create-principal', $context)) {
            $editurl = new moodle_url('/theme/academi/moodle_users/edit/edit_principal.php', ['id' => $principal->id]);
            $name = html_writer::link($editurl, $name);
        }
        $tabledata[] = [
            $name,
            $principal->idnumber,
        ];
    }
    if(!empty($principals)){
        $tablehead = get_string('principaltablehead', 'theme_academi');
        include(__DIR__ . '/templates/manage_users_table.php');
    } else {
        echo html_writer::tag('div', 'No Data Found.', ['class' => 'alert alert-info']);
    }
    echo html_writer::end_div();
}

function get_school_admins_data($tab, $helper) {
    $context = context_system::instance();
    $tabledata = [];
    echo html_writer::start_div($tab === 'school_admin' ? 'active' : '', ['id' => 'school_admin']);
    $schooladmins = $helper->get_school_admins();
    foreach($schooladmins as $schooladmin) {
        $name = $schooladmin->firstname . ' ' . $schooladmin->lastname; {
            if (has_capability('local/moodle_survey:create-school-admin', $context)) {
                $editurl = new moodle_url('/theme/academi/moodle_users/edit/edit_school_admin.php', ['id' => $schooladmin->userid]);
                $name = html_writer::link($editurl, $name);
            }
        }
        $tabledata[] = [
            $name,
            $schooladmin->schoolname,
            $schooladmin->idnumber,
        ];
    }
    if(!empty($schooladmins)){
        $tablehead = get_string('schooladmintablehead', 'theme_academi');
        include(__DIR__ . '/templates/manage_users_table.php');
    } else {
        echo html_writer::tag('div', 'No Data Found.', ['class' => 'alert alert-info']);
    }
    echo html_writer::end_div();
}