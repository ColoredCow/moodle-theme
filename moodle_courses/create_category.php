<?php

require_once('../../../config.php');
require_once('components/modal.php');
require_login();
initialize_page();

echo $OUTPUT->header();

$categoryid = required_param('categoryid', PARAM_INT);
$pagetype = "create_category";

$helper = new \theme_academi\helper();
$filters = get_filters($filters);
$categories = $helper->get_all_course_categories($filters);
$coursecategoryurl = new moodle_url('/theme/academi/moodle_courses/create_category.php', ['categoryid' => $categoryid]);

if (strlen($filters['categorytype']) > 0) {
    redirect($coursecategoryurl);
}
echo generate_page_header($categoryid, $coursecategoryurl);
echo generate_filter_form($filters, $coursecategoryurl, $categoryid);
echo generate_category_table($categoryid, $categories);
echo add_dynamic_form_script();
echo $OUTPUT->footer();

function initialize_page() {
    global $PAGE;
    $context = context_system::instance();

    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/local/moodle_survey/create_category.php'));
    $PAGE->set_title(get_string('createcoursecategory', 'theme_academi'));
}

function generate_page_header($categoryid, $coursecategoryurl) {
    global $PAGE;
    $plusicon = new moodle_url('/local/moodle_survey/pix/plus-icon.svg');
    $createbutton = html_writer::div(
        html_writer::link(
            '#',
            html_writer::tag('img', '', ['src' => $plusicon, 'alt' => 'Icon', 'class' => 'plus-icon']) . ' ' . get_string('createcategory', 'local_moodle_survey'),
            ['class' => 'create-button', 'id' => 'open-modal']
        ),
        'create-button-container'
    );
    $addcategorytitle = get_string('coursecategory', 'theme_academi');
    $categoryheading = 'Courses / '. get_string('coursecategory', 'theme_academi');
    $heading = html_writer::tag('span', $categoryheading, ['class' => 'page-title']);
    $content = $heading . ' ' . $createbutton;
    $modallabel = $addcategorytitle;
    $modaldescription = html_writer::start_tag('form', ['method' => 'get', 'action' => $coursecategoryurl]) .
    html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'categoryid', 'value'=> $categoryid]) .
    html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'categorytype', 'value'=> 'create']) .
    html_writer::div(
        html_writer::empty_tag('input', [
            'type' => 'text',
            'name' => 'categoryname',
            'placeholder' => $addcategorytitle,
            'class' => 'add-category-field'
        ]) . 
        html_writer::empty_tag('input', [
            'type' => 'submit',
            'value' => 'Add',
            'class' => 'custom-action-btn add-category-btn'
        ])
        , 'add-category-form'
    ) . html_writer::end_tag('form');
    $modal = generate_modal($modallabel, $modaldescription);
    return html_writer::tag('div', $content . $modal, ['class' => 'survey-header']);
}

function generate_filter_form($filters, $coursecategoryurl, $categoryid) {
    global $PAGE;

    return html_writer::start_tag('form', ['method' => 'get', 'action' => $coursecategoryurl, 'id' => 'filter-form']) .
            html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'categoryid', 'value'=> $categoryid]) .
            html_writer::start_div('filter-form d-flex') .
            html_writer::empty_tag('input', ['type' => 'text', 'name' => 'search', 'value' => $filters['search'], 'placeholder' => get_string('search', 'local_moodle_survey'), 'class' => 'search-input category-filter']) .
            html_writer::end_div() .
            html_writer::end_tag('form');
}

function generate_category_table($categoryid, $categories) {
    global $PAGE;
    $table = new html_table();
    $deleteicon = new moodle_url('/local/moodle_survey/pix/delete-icon.svg');
    
    if(sizeof($categories) > 0) {
        $table->head = [
            get_string('category', 'local_moodle_survey'),
            get_string('createdon', 'local_moodle_survey'),
            get_string('action', 'local_moodle_survey'),
        ];
        
        foreach ($categories as $category) {
            $coursecategorydeleteurl = new moodle_url('/theme/academi/moodle_courses/create_category.php', ['categoryid' => $categoryid, 'categorytype'=> 'delete', 'coursecategoryid'=> $category->id]);
            $createddate = date('Y/m/d', $category->timemodified);
            $table->data[] = [
                html_writer::link('#category-' . $category->id, $category->name),
                $createddate,
                html_writer::link($coursecategorydeleteurl, 
                    html_writer::tag('img', '', ['src' => $deleteicon, 'alt' => 'Icon', 'class' => 'plus-icon'])
                ),
            ];
        }
    } else {
        echo html_writer::tag('div', get_string('nocategoryfound', 'local_moodle_survey'), ['class' => 'alert alert-info']);
    }

    return html_writer::table($table);
}

function get_filters() {
    $categoryname = optional_param('categoryname', '', PARAM_RAW_TRIMMED);
    $categoryid = optional_param('categoryid', '', PARAM_INT);
    $categorytype = optional_param('categorytype', '', PARAM_RAW_TRIMMED);
    $coursecategoryid = optional_param('coursecategoryid', '', PARAM_INT);
    $search = optional_param('search', '', PARAM_RAW_TRIMMED);

    return [
        'categoryname' => $categoryname,
        'categoryid' => $categoryid,
        'categorytype' => $categorytype,
        'coursecategoryid' => $coursecategoryid,
        'search' => $search,
    ];
}

function add_dynamic_form_script() {
    return html_writer::script("
        document.addEventListener('DOMContentLoaded', function() {
            var dateInput = document.querySelector('.date-input');
            var form = document.getElementById('filter-form');

            function submitForm() {
                form.submit();
            }

            if (dateInput) {
                dateInput.addEventListener('change', submitForm);
            }
        });
    ");
}