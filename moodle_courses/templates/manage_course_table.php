
<?php
$table = new html_table();
$helper = new \theme_academi\helper();

$table->head = [
    'Name',
    'Category',
    'Created ON',
    'Schools',
    'Teachers assigned',
    'Status',
    'Action'
];

foreach ($courses as $course) {
    $editurl = new moodle_url('/course/edit.php', ['id' => $course->id]);
    $assignurl = new moodle_url('/theme/academi/moodle_courses/assign_school.php', ['course' => $course->id]);
    $coursename = html_writer::link($editurl, $course->fullname);
    $assignbutton = html_writer::div(
        html_writer::link(
            $assignurl,
            'Assign Schools',
            array('class' => 'mr-2 assign-school-button')
        )
    );
    $table->data[] = [
        $coursename,
        format_string($helper->get_category_of_course($course)->name),
        format_string(date('Y-m-d', $course->timecreated)),
        format_string($helper->get_schools_count_for_course($course)),
        format_string($helper->get_assignees_count_for_course($course)),
        html_writer::span('Live', "badge badge-pill badge-color survey-status survey-live"),
        $assignbutton
    ];
}

echo html_writer::table($table);
?>
