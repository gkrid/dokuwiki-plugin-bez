<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$causo = new Causes();
$issue_id = (int)$params[1];

if (isset($params[2]) && $params[2] == 'delete') {
	$cid = (int)$params[3];
	$causo->delete($cid);
	if (count($errors) == 0)
		header("Location: ?id=bez:issue_causes:id:$issue_id");
}


$isso = new Issues();
$template['issue'] = $isso->get($issue_id);
$template['causes'] = $causo->get($issue_id);

$tasko = new Tasks();
$template['anytasks'] = $tasko->any_task($issue_id);
$template['opentasks'] = $tasko->any_open($issue_id);
$template['cause_without_task'] = $isso->cause_without_task($issue_id);

$template['issue_object'] = $this->model->issues->get_one($issue_id);
