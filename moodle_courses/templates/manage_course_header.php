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
            array('class' => 'create-button', 'id' => 'open-modal')
        ),
        'create-button-container'
    );
    $heading = html_writer::tag('span', 'Courses', ['class' => 'page-title']);
    $content = $heading . ' ' . $createbutton;
    echo html_writer::tag('div', $content, ['class' => 'survey-header']);

    echo generate_filter_form($status, $moocscategory, $search);
    echo choose_categories_modal($createcoursecategoryurl, $iconurl);

    echo generate_form_submission_script();

    function generate_form_submission_script() {
        return html_writer::script("
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
    }

    function generate_filter_form($status, $moocscategory, $search) {
        global $PAGE;
        $categoryoptions['all'] = 'Select Category';
        $moocsstatusoptions['all']  = 'Select Status';
        $html = html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'id' => 'filter-form']);
        $html .= html_writer::start_div('filter-form d-flex justify-content-between');
        $html .= html_writer::select($moocsstatusoptions, 'status', $status, null, ['class' => 'status-select', 'id' => 'status-select']);
        $html .= html_writer::empty_tag('input', ['type' => 'date', 'name' => 'createdon', 'placeholder' => get_string('createdat', 'local_moodle_survey'), 'class' => 'date-input']);
        $html .= html_writer::select($categoryoptions, 'category', $moocscategory, null, ['class' => 'status-select', 'id' => 'category-select']);

        $html .= html_writer::empty_tag('input', ['type' => 'text', 'name' => 'search', 'value' => $search, 'placeholder' => get_string('search', 'local_moodle_survey'), 'class' => 'search-input']);

        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('form');
        return $html;
    }

    function choose_categories_modal($createcoursecategoryurl, $plusicon) {
        $helper = new \theme_academi\helper();
        $categoryid = $helper->get_top_level_category_by_name('Courses')->id;
        $categories = $helper->get_categories_by_parent_id($categoryid);
        $modallabel = get_string('choosecoursecategories', 'theme_academi');
        if (sizeof($categories) > 0) {
            foreach ($categories as $category) {
                $categoryurl = new \moodle_url('/course/edit.php', ['category'=> $category->id]);
                $modaldescription .= html_writer::link(
                    $categoryurl,
                    $category->name,
                    array('class' => 'create-button d-flex justify-content-center categories-link')
                );
            }
        
            $modaldescription = html_writer::div($modaldescription, 'modal-description');
        } else {
            $modaldescription = html_writer::tag('div', get_string('createcoursecategorycontent', 'theme_academi'), ['class' => 'alert alert-info']);
            $modaldescription .= html_writer::div(
                html_writer::link(
                    $createcoursecategoryurl,
                    html_writer::tag('img', '', array('src' => $plusicon, 'alt' => 'Icon', 'class' => 'plus-icon')) . ' ' . 'Create Course Categories',
                    array('class' => 'create-button text-align-center')
                ),
                'd-flex justify-content-center'
            );
        }
        echo generate_modal($modallabel, $modaldescription);
    }
?>
