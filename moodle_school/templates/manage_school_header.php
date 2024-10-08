<?php
    $categories = [];
    $moocsstatusoptions = [];
    $createurl = new moodle_url('/blocks/iomad_company_admin/company_edit_form.php', ['createnew' => 1]);
    $iconurl = new \moodle_url('/theme/academi/pix/plus-icon.svg');
    $createbutton = html_writer::div(
        html_writer::link(
            $createurl,
            html_writer::tag('img', '', array('src' => $iconurl, 'alt' => 'Icon', 'class' => 'plus-icon')) . ' ' . 'Add New School',
            array('class' => 'create-button')
        ),
        'create-button-container'
    );
    $heading = html_writer::tag('span', get_string('school', 'theme_academi'), ['class' => 'page-title']);
    $content = $heading . ' ' . $createbutton;
    echo html_writer::tag('div', $content, ['class' => 'survey-header']);

    // Filter form
    $categoryoptions['all'] = 'Select Category';
    $moocsstatusoptions['all']  = 'Select Status';

    echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'id' => 'filter-form']);
    echo html_writer::start_div('filter-form d-flex justify-content-between');
    // echo html_writer::select($moocsstatusoptions, 'status', $status, null, ['class' => 'status-select', 'id' => 'status-select']);
    // echo html_writer::empty_tag('input', ['type' => 'date', 'name' => 'createdon', 'placeholder' => get_string('createdat', 'local_moodle_survey'), 'class' => 'date-input']);
    // echo html_writer::select($categoryoptions, 'category', $moocscategory, null, ['class' => 'status-select', 'id' => 'category-select']);

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
