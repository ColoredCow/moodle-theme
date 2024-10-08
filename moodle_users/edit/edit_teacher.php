<?php
// Include necessary libraries or files for form handling
require_once('../../../../config.php');
global $PAGE, $CFG;
$helper = new \theme_academi\helper();
require_login();
echo $OUTPUT->header();
if (is_sel_admin()) {
    redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php', ['tab' => 'school_admin']));
}
$id = required_param('id', PARAM_INT);
$user = $helper->get_user_by_id($id);
$existingusergrade = $helper->get_user_grade_by_user_id($id);
if($existingusergrade) {
    $usergrade = json_decode($existingusergrade->user_grade);
} else {
    $usergrade = [];
}
if (!$user) {
    redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php', ['tab' => 'teacher']));
}
initialize_page($PAGE);

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

    $usergrade = new stdClass();
    if(!$existingusergrade) {
        $usergrade->user_grade = json_encode($_POST['teachergrade']);
        $usergrade->user_id = $id;
        $helper->create_user_grade($usergrade);
    } else {
        $usergrade->id = $existingusergrade->id;
        $usergrade->user_grade = json_encode($_POST['teachergrade']);
        $helper->update_user_grade($usergrade);
    }
    redirect(new moodle_url('/theme/academi/moodle_users/manage_users.php', ['tab' => 'teacher']));
}
?>

<div class="col mb-4">
    <h2>Teachers / Edit Teacher</h2>
</div>
<form method="POST" class="needs-validation" novalidate>
    <input name="usertype" class="d-none" value="teacher"> 
    <?php require_once('../templates/edit_user_form.php') ?>

    <div class="">
        <div class="col-auto pt-1">
            <label for="grade" class="col-form-label control-label">Teacher Grade</label>
        </div>
        <div class="col-7">
            <select class="form-control grade-multiselect" name="teachergrade[]" required multiple>
                <?php
                    for ($grade = 1; $grade <= 12; $grade++) {
                        $selected = in_array($grade, $usergrade) ? 'selected' : '';
                        echo '<option value="' . $grade . '" ' . $selected . '>Grade ' . $grade . '</option>';
                    }
                ?>
            </select>
            <div class="invalid-feedback">
                - Please provide a valid input.
            </div>
        </div>
    </div>
    <div class="">
        <div class="col-auto pt-1">
            <label for="employeeid" class="col-form-label control-label"><?php echo 'Employee ID'; ?></label>
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