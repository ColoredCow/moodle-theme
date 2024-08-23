<?php
    global $PAGE;
    $context = context_system::instance();
?>

<div id="tabs">
    <ul>
        <?php 
            if (has_capability('local/moodle_survey:view-student', $context)) {
                echo '<li class="' . ($tab === 'student' ? 'active' : '') . '">';
                echo html_writer::link(new moodle_url($PAGE->url, ['tab' => 'student']), get_string('student', 'theme_academi'));
                echo '</li>';
            }
            if (has_capability('local/moodle_survey:view-teacher', $context)) {
                echo '<li class="' . ($tab === 'teacher' ? 'active' : '') . '">';
                echo html_writer::link(new moodle_url($PAGE->url, ['tab' => 'teacher']), get_string('teacher', 'theme_academi'));
                echo '</li>';
            }
            if (has_capability('local/moodle_survey:view-counsellor', $context)) {
                echo '<li class="' . ($tab === 'counsellor' ? 'active' : '') . '">';
                echo html_writer::link(new moodle_url($PAGE->url, ['tab' => 'counsellor']), get_string('counsellor', 'theme_academi'));
                echo '</li>';
            }
            if (has_capability('local/moodle_survey:view-principal', $context)) {
                echo '<li class="' . ($tab === 'principal' ? 'active' : '') . '">';
                echo html_writer::link(new moodle_url($PAGE->url, ['tab' => 'principal']), get_string('principal', 'theme_academi'));
                echo '</li>';
            }
        ?>
    </ul>
</div>
