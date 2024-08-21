<?php
$table = new html_table();
$helper = new \theme_academi\helper();

$table->head = $tablehead;

if (!empty($tabledata)) {
    foreach($tabledata as $rowdata) {
        $table->data[] = $rowdata;
    }
    echo html_writer::table($table);
}
?>
