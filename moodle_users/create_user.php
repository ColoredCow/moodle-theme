<?php
// Include necessary libraries or files for form handling
require_once('../../../config.php');
require_login();

initialize_page();
echo $OUTPUT->header();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = '';
    if ($result) {
        echo '<div class="alert alert-success">User created successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">There was an error creating the user.</div>';
    }
}
?>

<form action="" method="POST" class="form-horizontal">
    <!-- Username Field -->
    <div class="form-group">
        <label for="username" class="col-sm-2 control-label">Username</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="username" id="username" required>
        </div>
    </div>

    <!-- Email Field -->
    <div class="form-group">
        <label for="email" class="col-sm-2 control-label">Email</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" name="email" id="email" required>
        </div>
    </div>

    <!-- Password Field -->
    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">Password</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="password" id="password" required>
        </div>
    </div>

    <!-- First Name Field -->
    <div class="form-group">
        <label for="firstname" class="col-sm-2 control-label">First Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="firstname" id="firstname" required>
        </div>
    </div>

    <!-- Last Name Field -->
    <div class="form-group">
        <label for="lastname" class="col-sm-2 control-label">Last Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="lastname" id="lastname" required>
        </div>
    </div>

    <div class="form-group">
        <label for="usertype" class="col-sm-2 control-label">User Type</label>
        <div class="col-sm-10">
            <select name="category" id="category" class="form-control" required>
                <option value="1">Principal</option>
                <option value="2">Teacher</option>
                <option value="3">Student</option>
            </select>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">Create User</button>
        </div>
    </div>
</form>

<?php 
echo $OUTPUT->footer();
?>