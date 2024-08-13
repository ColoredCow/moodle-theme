
<?php
$table = new html_table();
$helper = new \theme_academi\helper();

$table->head = [
    'Name',
    'Category',
    "LEVEL",
    'Created ON',
    'Schools',
    'Teachers assigned',
    'Status'
];

foreach ($courses as $course) {
    $editurl = new moodle_url('/course/edit.php', ['id' => $course->id]);
    $coursename = html_writer::link($editurl, $course->fullname);
    $table->data[] = [
        $coursename,
        format_string($helper->get_category_of_course($course)->name),
        format_string('-'),
        format_string(date('Y-m-d', $course->timecreated)),
        format_string($helper->get_schools_count_for_course($course)),
        format_string($helper->get_assignees_count_for_course($course)),
        html_writer::span('Live', "badge badge-pill badge-color survey-status survey-live")
    ];
}

echo html_writer::table($table);
?>
