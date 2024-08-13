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

foreach ($moocs as $mooc) {
    $editurl = new moodle_url('/course/edit.php', ['id' => $mooc->id]);
    $moocname = html_writer::link($editurl, $mooc->fullname);
    $table->data[] = [
        $moocname,
        format_string($helper->get_category_of_course($mooc)->name),
        format_string('-'),
        format_string(date('Y-m-d', $mooc->timecreated)),
        format_string($helper->get_schools_count_for_course($mooc)),
        format_string($helper->get_assignees_count_for_course($mooc)),
        html_writer::span('Live', "badge badge-pill badge-color survey-status survey-live")
    ];
}

echo html_writer::table($table);
?>
