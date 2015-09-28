<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";

$tasko = new Tasks();
$causo = new Causes();

$issue_id = (int)$params[1];
$task_id = (int)$params[3];

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);

$template['task'] = $tasko->join($tasko->getone($task_id));

$task = $template['task'];
if ($task['cause'] != '') {
	$cause_id = (int)$task['cause'];
	$template['cause'] = $causo->join($causo->getone($cause_id));
}
