<?php

require_once('../../../config.php');
require_once('components/modal.php');
require_login();

$context = context_system::instance();
$category = required_param('category', PARAM_TEXT);
$pagetype = "create_category";

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/moodle_survey/create_category.php'));
$PAGE->set_title(get_string('createcoursecategory', 'theme_academi'));
$filters = get_filters($category);
echo $OUTPUT->header();

$helper = new \theme_academi\helper();
$categories = $helper->get_all_course_categories();
if (strlen($filters['createcategory']) || $filters['categoryid']) {
    redirect(new moodle_url('/local/moodle_survey/create_category.php', ['category' => $category]));
}
echo generate_page_header($category, $filters);
echo generate_filter_form($filters);
echo generate_category_table($filters, $category, $categories);
echo add_dynamic_form_script();
echo $OUTPUT->footer();

/**
 * Generates the page header including the create survey button and heading.
 *
 * @return string HTML content for the page header.
 */
function generate_page_header($category, $filters) {
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
    $modalurl = new moodle_url($PAGE->url, ['category' => $category]);
    $modaldescription = html_writer::start_tag('form', ['method' => 'get', 'action' => $modalurl, 'id' => 'filter-form']) . html_writer::div(
        html_writer::empty_tag('input', [
            'type' => 'text',
            'name' => 'createcategory',
            'placeholder' => $addcategorytitle,
            'class' => 'add-category-field'
        ]) . 
        html_writer::empty_tag('input', [
            'type' => 'submit',
            'value' => 'Add',
            'class' => 'custom-action-btn add-category-btn'
        ])
        , 'add-category-form'
    );
    $modal = generate_modal($modallabel, $modaldescription);
    return html_writer::tag('div', $content . $modal, ['class' => 'survey-header']);
}

/**
 * Generates the filter form for searching and filtering survey categories.
 *
 * @return string HTML content for the filter form.
 */
function generate_filter_form($filters) {
    global $PAGE;

    return html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'id' => 'filter-form']) .
            html_writer::start_div('filter-form d-flex justify-content-around') .
            html_writer::empty_tag('input', ['type' => 'date', 'name' => 'createdon', 'placeholder' => get_string('createdat', 'local_moodle_survey'), 'class' => 'date-input category-filter']) .
            html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'category', 'value' => $filters['category']]) .
            html_writer::empty_tag('input', ['type' => 'text', 'name' => 'search', 'value' => $filters['search'], 'placeholder' => get_string('search', 'local_moodle_survey'), 'class' => 'search-input category-filter']) .
            html_writer::end_div() .
            html_writer::end_tag('form');
}

/**
 * Generates the survey table with categories and actions.
 *
 * @return string HTML content for the survey table.
 */
function generate_category_table($filters, $category, $categories) {
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
            $deleteurl = new moodle_url($PAGE->url, ['categoryid' => $category->id, 'category' => $category]);
            $table->data[] = [
                html_writer::link('#category-' . $category->id, $category->label),
                date('Y-m-d', strtotime($category->created_at)),
                html_writer::link($deleteurl, 
                    html_writer::tag('img', '', ['src' => $deleteicon, 'alt' => 'Icon', 'class' => 'plus-icon'])
                ),
            ];
        }
    } else {
        echo html_writer::tag('div', get_string('nocategoryfound', 'local_moodle_survey'), ['class' => 'alert alert-info']);
    }

    return html_writer::table($table);
}

/**
 * Retrieves the filter parameters from the request.
 *
 * @return array
 */
function get_filters($category) {
    $search = optional_param('search', '', PARAM_RAW_TRIMMED);
    $createdon = optional_param('createdon', '', PARAM_RAW_TRIMMED);
    $createcategory = optional_param('createcategory', '', PARAM_RAW_TRIMMED);
    $categoryid = optional_param('categoryid', '', PARAM_INT);

    return [
        'search' => $search,
        'createdon' => $createdon,
        'category' => $category,
        'createcategory' => $createcategory,
        'categoryid' => $categoryid
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
