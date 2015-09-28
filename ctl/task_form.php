<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/users.php";

$tasko = new Tasks();
$causo = new Causes();
$issue_id = (int)$params[1];

/*casue*/
$cause_id = '';
if (isset($params[3])) {
	$cause_id = (int)$params[3];
	$template['cause'] = $causo->join($causo->getone($cause_id));
}

/*edycja*/
if (isset($params[7])) {
	$action = $params[5];
	$tid = (int)$params[7];

	if ($action == 'edit') 
		$value = $tasko->getone($tid);
	else if ($action == 'update') {
		$tasko->update($_POST, array(), $tid);
		if (count($errors) == 0)
			header("Location: ?id=bez:issue_task:id:$issue_id:tid:$tid");
	}
	$template['task_button'] = $bezlang['change_task_button'];
	$template['task_action'] = $this->id('task_form', 'id', $issue_id,
										 'cause', $cause_id, 'action', 'update', 'tid', $tid);
/*dodawania*/
} else {
	if (count($_POST) > 0) {
		$data = array('reporter' => $INFO['client'], 'date' => time(), 'issue' => $issue_id, 'cause' => $cause_id);
		$data = $tasko->add($_POST, $data);
		if (count($errors) == 0) {
			$tid = $tasko->lastid();
			header("Location: ?id=bez:issue_task:id:$issue_id:tid:$tid");
		} else
			$value = $_POST;
	} 
	$template['task_button'] = $bezlang['add'];
	$template['task_action'] = $this->id('task_form', 'id', $issue_id, 'cause', $cause_id, 'action', 'add');
}

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);

$usro = new Users();
$template['users'] = $usro->get();


//$template['task'] = $tasko->join($tasko->getone($task_id));
