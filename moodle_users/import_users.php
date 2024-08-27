<?php

require_once('../../../config.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_login();

initialize_page();
echo $OUTPUT->header();
$usertype = required_param('type', PARAM_TEXT);
echo display_page($usertype);
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

function display_page($usertype) {
    global $PAGE;
    $helper = new \theme_academi\helper();
    $context = context_system::instance();
    $url = new moodle_url('/theme/academi/moodle_users/import_users.php?type=student');
    switch ($usertype) {
        case 'student':
            if (is_sel_admin() && !has_capability('local/moodle_survey:create-student', $context)) {
                redirect(new moodle_url('/'));
            }
            break;
        case 'teacher':
            $url = new moodle_url('/theme/academi/moodle_users/import_users.php?type=teacher');
            if (is_sel_admin() && !has_capability('local/moodle_survey:create-teacher', $context)) {
                redirect(new moodle_url('/'));
            }
            break;
        case 'counsellor':
            $url = new moodle_url('/theme/academi/moodle_users/import_users.php?type=counsellor');
            if (is_sel_admin() && !has_capability('local/moodle_survey:create-counsellor', $context)) {
                redirect(new moodle_url('/'));
            }
            break;
        case 'principal':
            $url = new moodle_url('/theme/academi/moodle_users/import_users.php?type=principal');
            if (is_sel_admin() && !has_capability('local/moodle_survey:create-principal', $context)) {
                redirect(new moodle_url('/'));
            }
            break;
        default:
            redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php'));
    }

    $mform = new \theme_academi\import_users_form($url, ['usertype' => $usertype]);
    if ($mform->is_cancelled()) {
        redirect(new moodle_url('/local/moodle_survey/manage_survey.php'));
    } else if ($data = $mform->get_data()) {
        import_users($mform, $usertype);
    }
    $mform->display();    
}

function import_users($mform, $usertype) {
    $importid = csv_import_reader::get_new_iid('academi');
    $cir = new csv_import_reader($importid, 'academi');
    $content = $mform->get_file_content('userfile');
    $cir->load_csv_content($content, 'UTF-8', 'comma');
    $columns = $cir->get_columns();
    $cir->init();

    $iscolumnvalidated = validate_columns($columns);
    if (!$iscolumnvalidated) {
        redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php'));
    }

    while ($row = $cir->next()) {
        create_new_user($columns, $row, $usertype);
    }
    $csvloaderror = $cir->get_error();
    unset($content);

    if (!is_null($csvloaderror)) {
        redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php'));
    }
    redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php'));
}

function validate_columns($columns) {
    $validcolumns = ['username', 'firstname', 'lastname', 'email', 'password', 'idnumber'];
    $difference = array_diff($validcolumns, $columns);

    return empty($difference);
}

function create_new_user($columns, $row, $usertype) {
    global $USER;
    $helper = new \theme_academi\helper();
    $systemcontext = context_system::instance();
    $user = new stdClass();
    $user->auth = 'manual';
    $user->confirmed = 1;
    $user->mnethostid = 1;
    $user->timecreated = time();
    $user->timemodified = time();
    foreach ($columns as $index => $column) {
        $value = $row[$index];
        switch($column) {
            case 'password': 
                $user->password = hash_internal_user_password($value);
                break;
            default:
                $user->$column = $value;      
        } 
    }

    $userid = $helper->create_user($user);
    $role = $helper->get_role_id_by_name($usertype);

    $userrole = new stdClass();
    $userrole->roleid = $role->id;
    $userrole->userid = $userid;
    $userrole->contextid = $systemcontext->id;
    $userrole->timemodified = time();
    $userrole->modifierid = $USER->id;

    $usercompany = new stdClass();
    $usercompany->userid = $userid;
    $usercompany->companyid = get_user_school()->companyid;
    $usercompany->managertype = 0;
    $usercompany->departmentid = get_user_school_department()->id;
    $usercompany->suspended = 0;
    $usercompany->educator = 0;
    $helper->assign_user_to_school($usercompany);

    $result = $helper->assign_role($userrole);
}