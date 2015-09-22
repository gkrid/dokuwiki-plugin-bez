<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$tasko = new Tasks();
$issue_id = (int)$params[1];
$task_id = (int)$params[3];

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);
$template['task'] = $tasko->join($tasko->getone($task_id));
