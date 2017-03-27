<?php

//~ include_once DOKU_PLUGIN."bez/models/tasks.php";
//~ include_once DOKU_PLUGIN."bez/models/users.php";

//~ $tasko = new Tasks();
//~ $usro = new Users();

$task_id = (int)$nparams['tid'];

//~ $task_clean = $tasko->getone($task_id);
$task = $this->model->tasks->get_one($task_id);

if ($task === false) {
	header("Location: ?id=bez:tasks");
} else if ($task->issue !== NULL) {
	header("Location: ?id=bez:issue_task:id:".$task->issue.":tid:".$task_id);
}
	
//~ $template['task'] = $tasko->join($task_clean);
//~ $task = $template['task'];

//~ if (isset($nparams['plan'])) {
	//~ $value = $template['task'];
//~ }

if (isset($nparams['state']) && $nparams['state'] == '0') {
	try {
		$task->set_state(array('state' => '0'));
		$tid = $this->model->tasks->save($task);		
		header("Location: ?id=bez:show_task:tid:$task_id");
		
	} catch (Exception $e) {
		echo nl2br($e);
	}
} elseif (isset($nparams['action']) && $nparams['action'] == 'update') {
	try {
		$task->set_state(array('state' => $nparams['state'], 'reason' => $_POST['reason']));
		if ($task->any_errors()) {
			$errors = $task->get_errors();
			$value = $_POST;
		} else {
			$tid = $this->model->tasks->save($task);		
			header("Location: ?id=bez:show_task:tid:$task_id");
		} 
	} catch (Exception $e) {
		echo nl2br($e);
	}
	//~ if ($nparams['action'] == 'update') {
		//~ $data = $tasko->update_state($nparams['state'], $_POST['reason'], $task_id);
	//~ }
	//~ } else if ($nparams['action'] == 'save_plan') {
		//~ $data = $tasko->save_plan($_POST, $task_id);
	//~ }
	//~ if (count($errors) == 0) {
		//~ $title = 'Zmiana w zadaniu';
		//~ $exec = $data['executor'];
		//~ $subject = "[$conf[title]] $title: #$issue_id #z$tid";
		//~ $to = $usro->name($exec).' <'.$usro->email($exec).'>';
		//~ $body = "$uri?id=".$this->id('show_task', 'tid', $tid);
		//~ $this->helper->mail($to, $subject, $body);
		
		//~ header("Location: ?id=bez:show_task:tid:$task_id");
	//~ }
	//~ $value = $_POST;
//get old reason
} else {
	$value['reason'] = $task->reason;
}

$template['task'] = $task;


