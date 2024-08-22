<?php
// Include necessary libraries or files for form handling
require_once('../../../../config.php');
global $PAGE, $CFG;
$helper = new \theme_academi\helper();
require_login();

initialize_page($PAGE);
echo $OUTPUT->header();

/**
 * Initializes the page context and resources.
 */
function initialize_page($PAGE) {

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/theme/academi/moodle_users/manage_users.php'));
    $PAGE->set_title(get_string('users', 'theme_academi'));
    $PAGE->requires->js(new moodle_url('/theme/academi/moodle_users/js/forms.js'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $systemcontext = context_system::instance();
    $user = new stdClass();
    $user->auth = 'manual';
    $user->confirmed = 1;
    $user->username = $_POST['username'];
    $user->username = $_POST['username'];
    $user->mnethostid = 1;
    $user->password = hash_internal_user_password($_POST['password']);
    $user->firstname = $_POST['firstname'];
    $user->lastname = $_POST['lastname'];
    $user->email = $_POST['email'];
    $user->timecreated = time();
    $user->timemodified = time();

    $userid = $helper->create_user($user);
    $role = $helper->get_role_id_by_name($_POST['usertype']);

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
    $result = true;
    if ($result) {
        redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php', ['tab' => 'student']));
    } else {
        echo '<div class="alert alert-danger">There was an error creating the user.</div>';
    }
}
?>

<div class="col mb-4">
<h2>Students / Add Student</h2>
</div>

<form method="POST" class="col needs-validation" novalidate>
    <input name="usertype" class="d-none" value="student"> 
    <?php require_once('../templates/create_user_form.php') ?>
    <div class="">
        <div class="col-auto pt-1">
            <label for="employeeid" class="col-form-label control-label"><?php echo 'Student ID'; ?></label>
        </div>
        <div class="col-3">
            <input type="text" class="form-control" name="employeeid" id="employeeid" required>
            <div class="invalid-feedback">
                - Please provide a valid input.
            </div>
        </div>
    </div>

    <div class="pl-3 mt-4">
        <button class="btn btn-primary" type="submit">Create</button>
    </div>
</form>


<?php 
echo $OUTPUT->footer();
?>