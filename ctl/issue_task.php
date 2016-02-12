<?php

include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";

$tasko = new Tasks();
$causo = new Causes();

$issue_id = (int)$nparams[id];
$task_id = (int)$nparams[tid];

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);

$template['task'] = $tasko->join($tasko->getone($task_id));

$task = $template['task'];
if ($task['cause'] != '') {
	if ($this->action == 'issue_task')
		header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$task[cause]:tid:$task_id");
	$cause_id = (int)$task['cause'];
	$template['cause'] = $causo->join($causo->getone($cause_id));
}

$template['anytasks'] = $tasko->any_task($issue_id);
$template['opentasks'] = $tasko->any_open($issue_id);
