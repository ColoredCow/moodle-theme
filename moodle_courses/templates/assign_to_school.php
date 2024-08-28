<?php 
    $helper = new \theme_academi\helper();
    $schools = $helper->get_all_schools();
    $course = $helper->get_course_by_id($courseid);
    $alreadyassignedschoolsids = $helper->get_assigned_schools_for_course($courseid);
    if (!$course) {
        redirect(new moodle_url('/theme/academi/moodle_courses/manage_courses.php'));
    }
?>

<div class="col mb-4">
    <h2>Assign <?php echo $course->fullname; ?> to Schools</h2>
</div>

<form method="POST" class="col needs-validation" novalidate>
    <div class="">
        <div class="col-auto pt-1">
            <label for="school" class="col-form-label control-label">Select Schools</label>
        </div>
        <div class="col-7">
            <select class="form-control school-multiselect" name="schools[]" required multiple>
                <?php
                    foreach ($schools as $school) {
                        echo '<option value="' . $school->id . '" ' . (in_array($school->id, $alreadyassignedschoolsids) ? 'selected' : '') .  '>' . $school->name . '</option>';
                    }
                ?>
            </select>
            <div class="invalid-feedback">
                - Please provide a valid input.
            </div>
        </div>
    </div>

    <div class="pl-3 mt-4">
        <button class="btn btn-primary" type="submit">Assign</button>
    </div>
</form>