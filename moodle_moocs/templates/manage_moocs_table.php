<?php
$table = new html_table();
$dbhelper = new \local_moodle_survey\model\survey();
$table->head = [
    'Name',
    'Category',
    "LEVEL",
    'Created ON',
    'Schools',
    'Teachers assigned',
    'Status'
];

    $table->data[] = [
        'SEL 101',
        'Social Impact',
        'Advanced',
        '2024-06-07',
        '50',
        '200',
        'Live'
    ];

echo html_writer::table($table);
?>
