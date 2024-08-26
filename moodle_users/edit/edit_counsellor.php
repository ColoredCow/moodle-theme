<?php
// Include necessary libraries or files for form handling
require_once('../../../../config.php');
global $PAGE, $CFG;
$helper = new \theme_academi\helper();
require_login();
if (is_sel_admin()) {
    redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php', ['tab' => 'school_admin']));
}
$id = required_param('id', PARAM_INT);
$user = $helper->get_user_by_id($id);
if (!$user) {
    redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php', ['tab' => 'counsellor']));
}

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
    $user->id = $user->id;
    $user->username = trim($_POST['username']);
    $user->idnumber = trim($_POST['employeeid']);
    if (trim($_POST['password']) != '') {
        $user->password = hash_internal_user_password(trim($_POST['password']));
    }
    $user->firstname = $_POST['firstname'];
    $user->lastname = $_POST['lastname'];
    $user->email = trim($_POST['email']);
    $user->timemodified = time();
    $userid = $helper->update_user($user);
    redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php', ['tab' => 'counsellor']));
}
?>

<div class="col mb-4">
    <h2>Counsellors / Edit Counsellor</h2>
</div>

<form method="POST" class="needs-validation" novalidate>
    <input name="usertype" class="d-none" value="counsellor"> 
    <?php require_once('../templates/edit_user_form.php') ?>
    <div class="">
        <div class="col-auto pt-1">
            <label for="employeeid" class="col-form-label control-label"><?php echo 'Student ID'; ?></label>
        </div>
        <div class="col-3">
            <input type="text" class="form-control" name="employeeid" id="employeeid" value="<?php echo $user->idnumber; ?>">
            <div class="invalid-feedback">
                - Please provide a valid input.
            </div>
        </div>
    </div>

    <div class="pl-3 mt-4">
        <button class="btn btn-primary" type="submit">Edit</button>
    </div>
</form>

<?php 
echo $OUTPUT->footer();
?>