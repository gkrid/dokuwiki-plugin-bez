<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$causo = new Causes();
$tasko = new Tasks();
$issue_id = (int)$params[1];
$cause_id = (int)$params[3];

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);
$template['cause'] = $causo->join($causo->getone($cause_id));
$template['tasks'] = $tasko->get($issue_id, $cause_id);

$template['anytasks'] = $tasko->any_task($issue_id);
$template['opentasks'] = $tasko->any_open($issue_id);
$template['cause_without_task'] = $isso->cause_without_task($issue_id);
