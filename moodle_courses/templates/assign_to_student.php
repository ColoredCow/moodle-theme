<?php 
    $helper = new \theme_academi\helper();
    $school = get_school();
    $course = $helper->get_course_by_id($courseid);
    $coursegrademapping = $helper->get_assigned_school_grades_for_course($courseid, $school->id);
    $coursegrademapping = $coursegrademapping ? json_decode($coursegrademapping->user_grade) : [];
    if (!$course || !$school) {
        redirect(new moodle_url('/theme/academi/moodle_courses/manage_courses.php'));
    }
?>

<div class="col mb-4">
    <h2>Assign <?php echo $course->fullname; ?> to Students</h2>
</div>

<form method="POST" class="col needs-validation" novalidate>
    <input class="d-none" name="schoolid" value="<?php echo($school->id); ?>" required>
    <div class="">
        <div class="col-auto pt-1">
            <label for="grade" class="col-form-label control-label">Select student grades for which this course is: </label>
        </div>
        <div class="col-7">
            <select class="form-control grade-multiselect-lg" name="studentgrades[]" multiple required>
                <?php
                    for ($grade = 1; $grade <= 12; $grade++) {
                        $selected = in_array($grade, $coursegrademapping) ? 'selected' : '';
                        echo '<option value="' . $grade . '" ' . $selected . '>Grade ' . $grade . '</option>';
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