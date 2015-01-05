<?php
include_once DOKU_PLUGIN."bez/models/tasks.php";

$tasko = new Tasks();
$template['tasks'] = $tasko->get_close_task();
