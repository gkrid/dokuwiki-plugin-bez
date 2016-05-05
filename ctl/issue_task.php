<?php

include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/users.php";

$tasko = new Tasks();
$causo = new Causes();
$usro = new Users();

$issue_id = (int)$nparams['id'];
$task_id = (int)$nparams['tid'];

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

if (isset($nparams['plan'])) {
	$value = $template['task'];
}

if (isset($nparams['action'])) {
	if ($nparams['action'] == 'update') {
		$data = $tasko->update_state($nparams['state'], $_POST['reason'], $task_id);
	} else if ($nparams['action'] == 'save_plan') {
		$data = $tasko->save_plan($_POST, $task_id);
	}
	if (count($errors) == 0) {
		$title = 'Zmiana w zadaniu';
		$exec = $data['executor'];
		$subject = "[$conf[title]] $title: #$issue_id #z$tid";
		$to = $usro->name($exec).' <'.$usro->email($exec).'>';
		$body = "$uri?id=".$this->id('issue_task', 'id', $issue_id, 'tid', $tid);
		$this->helper->mail($to, $subject, $body);
		
		if ($cause_id == '')
			header("Location: ?id=bez:issue_task:id:$issue_id:tid:$task_id");
		else
			header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$task_id");
	}
	$value = $_POST;
}


$template['anytasks'] = $tasko->any_task($issue_id);
$template['opentasks'] = $tasko->any_open($issue_id);
$template['cause_without_task'] = $isso->cause_without_task($issue_id);
