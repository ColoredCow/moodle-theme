<?php
    $context = context_system::instance();
    $gradeoptions = [];
    $gradeteacheroptions = [];
    $createurl = new moodle_url('/theme/academi/moodle_users/create/create_student.php');
    $addbuttontext = 'Add New Student';
    $hascapabilitytocreate = has_capability('local/moodle_survey:create-student', $context);
    $importuserurl =  new moodle_url('/theme/academi/moodle_users/import_users.php', ['type' => $tab]);
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
        case 'school_admin':
            $hascapabilitytocreate = false;
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
        $importbutton = html_writer::div(
            html_writer::link(
                $importuserurl,
                ' Bulk Import',
                array('class' => 'mr-2 create-button')
            ),
            'create-button-container'
        );
    }
    $heading = html_writer::tag('span', get_string('users', 'theme_academi'), ['class' => 'page-title']);
    $content = $heading . ' <div class="d-flex">' . $importbutton . ' ' . $createbutton . '</div>';
    echo html_writer::tag('div', $content, ['class' => 'survey-header']);
    require_once(__DIR__ . '/tabs.php');

    // Filter form
    $gradeoptions['all'] = 'Select Grade';
    $gradeteacheroptions['all']  = 'Select Grade Teacher';

    echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'id' => 'filter-form']);
    echo html_writer::start_div('filter-form d-flex justify-content-between');
    // echo html_writer::select($gradeteacheroptions, 'status', $status, null, ['class' => 'status-select', 'id' => 'status-select']);
    // echo html_writer::select($gradeoptions, 'category', $moocscategory, null, ['class' => 'status-select', 'id' => 'category-select']);

    echo html_writer::empty_tag('input', ['type' => 'text', 'name' => 'tab', 'value' => $tab, 'class' => 'd-none']);
    echo html_writer::empty_tag('input', ['type' => 'text', 'name' => 'search', 'value' => $filters['name'], 'placeholder' => get_string('search', 'local_moodle_survey'), 'class' => 'search-input']);

    echo html_writer::end_div();
    echo html_writer::end_tag('form');

    // JavaScript for automatic form submission
    echo html_writer::script("
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.querySelector('.search-input');
            var form = document.getElementById('filter-form');

            function submitForm() {
                form.submit();
            }

            if (searchInput) {
                searchInput.addEventListener('change', submitForm);
            }
        });
    ");
?>
