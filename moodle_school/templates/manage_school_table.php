<?php
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

foreach($schools as $school) {
    $table->data[] = [
        $school->name,
        $school->city,
        $school->country,
        'John Doe',
        'dps.delhi@gmail.com',
        html_writer::span('Active', "badge badge-pill badge-color survey-status survey-live")
    ];
}

echo html_writer::table($table);
?>
