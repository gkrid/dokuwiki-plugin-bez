<?php

include_once DOKU_PLUGIN."bez/models/issues.php";
//~ include_once DOKU_PLUGIN."bez/models/tasks.php";
//~ include_once DOKU_PLUGIN."bez/models/causes.php";
//~ include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/bezcache.php";

$tasko = new Tasks();
$causo = new Causes();
$usro = new Users();
$bezcache = new Bezcache();

$issue_id = (int)$nparams['id'];
$task_id = (int)$nparams['tid'];

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);

$task = $this->model->tasks->get_one($task_id);
$template['task'] = $this->model->tasks->get_one($task_id);
//~ $template['task'] = $tasko->join($tasko->getone($task_id));

if ($task->cause != '') {
	if ($this->action == 'issue_task') {
		header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:".$task->cause.":tid:$task_id");
	}
	$cause_id = $task->cause;
	$template['cause'] = $causo->join($causo->getone($cause_id));
}

//reopen task
if (isset($nparams['state']) && $nparams['state'] === '0') {
	try {
		$task->set_state(array('state' => '0'));
		$this->model->save_task($task);
						
		if ($cause_id == '') {
			header("Location: ?id=bez:issue_task:id:$issue_id:tid:$task_id");
		} else {
			header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$task_id");
		}
	} catch (Exception $e) {
		echo nl2br($e);
	}
}

if (isset($nparams['state'])) {
	$value = $task->get_assoc();
}

if (isset($nparams['action'])) {
	//~ if ($nparams['action'] == 'update') {
		//~ $data = $tasko->update_state($nparams['state'], $_POST['reason'], $task_id);
	//~ } else if ($nparams['action'] == 'save_plan') {
		//~ $data = $tasko->save_plan($_POST, $task_id);
	//~ }
	//~ if (count($errors) == 0) {
		//~ $title = 'Zmiana w zadaniu';
		//~ $exec = $data['executor'];
		//~ $subject = "[$conf[title]] $title: #$issue_id #z$tid";
		//~ $to = $usro->name($exec).' <'.$usro->email($exec).'>';
		//~ $body = "$uri?id=".$this->id('issue_task', 'id', $issue_id, 'tid', $tid);
		//~ $this->helper->mail($to, $subject, $body);
		
		//~ if ($cause_id == '')
			//~ header("Location: ?id=bez:issue_task:id:$issue_id:tid:$task_id");
		//~ else
			//~ header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$task_id");
	//~ }
	//~ $value = $_POST;
	
	if ($nparams['action'] == 'update') {
		try {
			$task->set_state(array('state' => $nparams['state'], 'reason' => $_POST['reason']));
		
			if ($task->any_errors()) {
				$errors = $task->get_errors();
				$value = $_POST;
			} else {
				$this->model->tasks->save($task);
				$bezcache->task_toupdate($task->id);
								
				if ($cause_id == '') {
					header("Location: ?id=bez:issue_task:id:$issue_id:tid:$task_id");
				} else {
					header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$task_id");
				}
			}
		} catch (Exception $e) {
			echo nl2br($e);
		}
	}
}


$template['anytasks'] = $tasko->any_task($issue_id);
$template['opentasks'] = $tasko->any_open($issue_id);
$template['cause_without_task'] = $isso->cause_without_task($issue_id);
