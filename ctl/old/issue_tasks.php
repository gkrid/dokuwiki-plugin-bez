<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$tasko = new Tasks();
$issue_id = (int)$params[1];

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);
$template['tasks'] = $tasko->get($issue_id, NULL);

$template['anytasks'] = $tasko->any_task($issue_id);
$template['opentasks'] = $tasko->any_open($issue_id);
$template['cause_without_task'] = $isso->cause_without_task($issue_id);

$template['issue_object'] = $this->model->issues->get_one($issue_id);
