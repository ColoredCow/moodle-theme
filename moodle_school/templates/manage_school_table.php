<?php
global $SESSION;
$table = new html_table();
$helper = new \theme_academi\helper();
$schools = $helper->get_school_list();
$table->head = [
    'Name',
    'ADDRESS',
    "STATE",
    'SCHOOL ADMIN',
    'CONTACT',
    'STATUS',
];

$schoolid = optional_param('schoolid', null, PARAM_INT);
if(isset($schoolid)) {
    $SESSION->currenteditingcompany = $schoolid;
    $editschoolurl = new moodle_url('/blocks/iomad_company_admin/company_edit_form.php');
    redirect($editschoolurl);
}

foreach($schools as $school) {
    $schooladmin = $helper->get_school_admin($school->id) ?? null;
    $table->data[] = [
        get_school_edit_page_link($school),
        $school->address,
        $school->city,
        $schooladmin ? $schooladmin->firstname . ' ' . $schooladmin->lastname : html_writer::div(
            html_writer::link(
                new moodle_url('/theme/academi/moodle_users/create/create_school_admin.php', ['school' => $school->id]),
                'Add admin',
                array('class' => 'add-school-admin-button')
            )
        ),
        '-',
        html_writer::span('Active', "badge badge-pill badge-color survey-status survey-live")
    ];
}

function get_school_edit_page_link($school) {
    $indexpageurl = new moodle_url('/theme/academi/moodle_school/manage_school.php', ['schoolid' => $school->id]);
    return html_writer::link($indexpageurl, $school->name);
}

echo html_writer::table($table);
?>
