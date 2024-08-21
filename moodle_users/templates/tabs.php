<?php
    if (!isset($tab)) {
        $tab = 'student';
    }
?>


<div id="tabs">
    <ul>
        <li class="<?php echo $tab === 'student' ? 'active' : '' ?>">
            <?php echo html_writer::link('#student', get_string('student', 'theme_academi')) ?>
        </li>
        <li class="<?php echo $tab === 'questions' ? 'active' : '' ?>">
            <?php echo html_writer::link('#teacher', get_string('teacher', 'theme_academi')) ?>
        </li>
    </ul>
