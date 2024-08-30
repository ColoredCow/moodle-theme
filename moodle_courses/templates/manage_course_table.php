
<?php
$table = new html_table();
$helper = new \theme_academi\helper();
$headers =  [
    'Name',
    'Category',
    'Created On'
];
if (has_capability('local/moodle_survey:assign-course-to-school', context_system::instance())) {
    $headers[] = 'Schools';
}
$headers = array_merge($headers, [
    'Students assigned',
    'Status',
    'Action'
]);

$table->head = $headers;

foreach ($courses as $course) {
    $editsettingurl = new moodle_url('/course/edit.php', ['id' => $course->id]);
    $editcourseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
    $assignurl = new moodle_url('/theme/academi/moodle_courses/assign_school.php', ['course' => $course->id]);
    $coursename = $course->fullname;
    $assignbutton = '';
    if (has_capability('local/moodle_survey:assign-course-to-school', context_system::instance())) {
        $coursename = html_writer::link($editsettingurl, $course->fullname);
        $assignbutton = html_writer::div(
            html_writer::link(
                $editcourseurl,
                'Edit',
                array('class' => 'mr-2 px-3 assign-school-button')
            )
            . ' ' . 
            html_writer::link(
                $assignurl,
                'Assign Schools',
                array('class' => 'mr-2 assign-school-button')
            ) 
        );
    } else if (has_capability('local/moodle_survey:assign-course-to-user', context_system::instance())) {
        $assignurl = new moodle_url('/theme/academi/moodle_courses/assign_student.php', ['course' => $course->id]);
        $assignbutton = html_writer::div(
            html_writer::link(
                $assignurl,
                'Assign to Students',
                array('class' => 'mr-2 assign-school-button')
            ).
            html_writer::link(
                new moodle_url('/course/view.php', ['id' => $course->id]),
                'View',
                array('class' => 'mr-2 assign-school-button')
            )
        );
    }
    else if(is_student()) {
        $coursename = html_writer::span($course->fullname);
        $assignbutton = html_writer::div(
            html_writer::link(
                new moodle_url('/course/view.php', ['id' => $course->id]),
                'Take Course',
                array('class' => 'mr-2 assign-school-button')
            )
        );
    }
    $tabledata = [
        $coursename,
        format_string($helper->get_category_of_course($course)->name),
        format_string(date('Y-m-d', $course->timecreated))
    ];
    if (has_capability('local/moodle_survey:assign-course-to-school', context_system::instance())) {
        $tabledata[] = format_string($helper->get_schools_count_for_course($course));
    }

    $tabledata = array_merge($tabledata, [
        format_string($helper->get_assignees_count_for_course($course)),
        html_writer::span('Live', "badge badge-pill badge-color survey-status survey-live"),
        $assignbutton
    ]);
    $table->data[] = $tabledata;
}

echo html_writer::table($table);
?>
