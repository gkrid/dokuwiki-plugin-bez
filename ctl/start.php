<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$isso = new Issues();
$tasko= new Tasks();

$issues = $isso->get_by_days();
$tasks = $tasko->get_by_days();

$timeline = $helper->days_array_merge($issues, $tasks);

$template['timeline'] = $timeline;
