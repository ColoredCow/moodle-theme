<?php
    $context = context_system::instance();
    $gradeoptions = [];
    $gradeteacheroptions = [];
    $createurl = new moodle_url('/theme/academi/moodle_users/create/create_student.php');
    $addbuttontext = 'Add New Student';
    $hascapabilitytocreate = has_capability('local/moodle_survey:create-student', $context);
    switch ($tab) {
        case 'teacher':
            $hascapabilitytocreate = has_capability('local/moodle_survey:create-teacher', $context);
            $createurl = new moodle_url('/theme/academi/moodle_users/create/create_teacher.php');
            $addbuttontext = 'Add New Teacher';
            break;
        case 'counsellor':
            $hascapabilitytocreate = has_capability('local/moodle_survey:create-counsellor', $context);
            $createurl = new moodle_url('/theme/academi/moodle_users/create/create_counsellor.php');
            $addbuttontext = 'Add New Counsellor';
            break;
        case 'principal':
            $hascapabilitytocreate = has_capability('local/moodle_survey:create-principal', $context);
            $createurl = new moodle_url('/theme/academi/moodle_users/create/create_principal.php');
            $addbuttontext = 'Add New Principal';
            break;
    }
    $iconurl = new \moodle_url('/theme/academi/pix/plus-icon.svg');
    if ($hascapabilitytocreate) {
        $createbutton = html_writer::div(
            html_writer::link(
                $createurl,
                html_writer::tag('img', '', array('src' => $iconurl, 'alt' => 'Icon', 'class' => 'plus-icon')) . ' ' . $addbuttontext,
                array('class' => 'create-button')
            ),
            'create-button-container'
        );
    }
    $heading = html_writer::tag('span', get_string('users', 'theme_academi'), ['class' => 'page-title']);
    $content = $heading . ' ' . $createbutton;
    echo html_writer::tag('div', $content, ['class' => 'survey-header']);
    require_once(__DIR__ . '/tabs.php');

    // Filter form
    $gradeoptions['all'] = 'Select Grade';
    $gradeteacheroptions['all']  = 'Select Grade Teacher';

    echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'id' => 'filter-form']);
    echo html_writer::start_div('filter-form d-flex justify-content-between');
    echo html_writer::select($gradeteacheroptions, 'status', $status, null, ['class' => 'status-select', 'id' => 'status-select']);
    echo html_writer::select($gradeoptions, 'category', $moocscategory, null, ['class' => 'status-select', 'id' => 'category-select']);

    echo html_writer::empty_tag('input', ['type' => 'text', 'name' => 'search', 'value' => $search, 'placeholder' => get_string('search', 'local_moodle_survey'), 'class' => 'search-input']);

    echo html_writer::end_div();
    echo html_writer::end_tag('form');

    // JavaScript for automatic form submission
    echo html_writer::script("
        document.addEventListener('DOMContentLoaded', function() {
            var statusSelect = document.getElementById('status-select');
            var categorySelect = document.getElementById('category-select');
            var dateInput = document.querySelector('.date-input');
            var form = document.getElementById('filter-form');

            function submitForm() {
                form.submit();
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', submitForm);
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', submitForm);
            }

            if (dateInput) {
                dateInput.addEventListener('change', submitForm);
            }
        });
    ");
?>
