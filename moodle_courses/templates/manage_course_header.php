<?php
    $categories = [];
    $moocsstatusoptions = [];
    $helper = new \theme_academi\helper();
    $createurl = new \moodle_url('/course/edit.php', ['category'=>$coursescategory->id]);
    $coursecategory = $helper->get_top_level_category_by_name('Courses');
    $createcoursecategoryurl = new moodle_url('/theme/academi/moodle_courses/create_category.php', ['categoryid' => $coursecategory->id]);
    $createmoocscategoryurl = '#';
    $iconurl = new \moodle_url('/local/moodle_survey/pix/plus-icon.svg');
    $createbutton = html_writer::div(
        html_writer::link(
            $createcoursecategoryurl,
            'Course categories',
            array('class' => 'create-button')
        ) .
        html_writer::link(
            $createurl,
            html_writer::tag('img', '', array('src' => $iconurl, 'alt' => 'Icon', 'class' => 'plus-icon')) . ' ' . 'Create new Course',
            array('class' => 'create-button')
        ),
        'create-button-container'
    );
    $heading = html_writer::tag('span', 'Courses', ['class' => 'page-title']);
    $content = $heading . ' ' . $createbutton;
    echo html_writer::tag('div', $content, ['class' => 'survey-header']);

    // Filter form
    $categoryoptions['all'] = 'Select Category';
    $moocsstatusoptions['all']  = 'Select Status';

    echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'id' => 'filter-form']);
    echo html_writer::start_div('filter-form d-flex justify-content-between');
    echo html_writer::select($moocsstatusoptions, 'status', $status, null, ['class' => 'status-select', 'id' => 'status-select']);
    echo html_writer::select($categoryoptions, 'category', $moocscategory, null, ['class' => 'status-select', 'id' => 'category-select']);

    echo html_writer::empty_tag('input', ['type' => 'text', 'name' => 'search', 'value' => $search, 'placeholder' => get_string('search', 'local_moodle_survey'), 'class' => 'search-input', 'id' => 'search-input']);

    echo html_writer::end_div();
    echo html_writer::end_tag('form');

    // JavaScript for automatic form submission
    echo html_writer::script("
        document.addEventListener('DOMContentLoaded', function() {
            var statusSelect = document.getElementById('status-select');
            var categorySelect = document.getElementById('category-select');
            var searchElement = document.getElementById('search-input');
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

            if (searchElement) {
                searchElement.addEventListener('change', submitForm);
            }
        });
    ");
?>
