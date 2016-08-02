<?php

include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/users.php";

$tasko = new Tasks();
$usro = new Users();

$task_id = (int)$nparams['tid'];

$task_clean = $tasko->getone($task_id);

if ($task_clean === NULL) {
	header("Location: ?id=bez:tasks");
} else if ($task_clean['issue'] !== NULL) {
	header("Location: ?id=bez:issue_task:id:".$task_clean['issue'].":tid:".$task_id);
}



	
$template['task'] = $tasko->join($task_clean);
$task = $template['task'];

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
		$body = "$uri?id=".$this->id('show_task', 'tid', $tid);
		$this->helper->mail($to, $subject, $body);
		
		header("Location: ?id=bez:show_task:tid:$task_id");
	}
	$value = $_POST;
}

