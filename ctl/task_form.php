<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/tasktypes.php";
include_once DOKU_PLUGIN."bez/models/bezcache.php";

$tasko = new Tasks();
$causo = new Causes();
$usro = new Users();
$tasktypeso = new Tasktypes();
$bezcache = new Bezcache();

$issue_id = (int)$nparams['id'];

/*casue*/
$cause_id = '';
if (isset($nparams['cid']) && $nparams['cid'] != '') {
	$cause_id = (int)$nparams['cid'];
	$template['cause'] = $causo->join($causo->getone($cause_id));
}

/*edycja*/
if (isset($nparams['tid'])) {
	$action = $nparams['action'];
	$tid = (int)$nparams['tid'];
	
	$task = $this->model->task($tid);
	
	$taskso = new Taskstates();
	$task_states = $taskso->get();

	//~ $template['raw_state'] = $task['state'];
	//~ $template['state']  = $task_states[$task['state']];
	if (isset($nparams['id']))
		$template['causes'] = $causo->get($issue_id);
	
	if (!$action)
		$action = 'edit';

	if ($action == 'edit') {
		$value = $task->get_assoc();
		//~ $value['cause_id'] = $task['cause'];
	} else if ($action == 'update') {
		//~ $cause_id = $_POST['cause_id'];
		//~ if ($cause_id != '')
			//~ $cause_id = (int)$cause_id;
			
		//~ $data = $tasko->update($_POST, array('cause' => $cause_id), $tid);
		//~ if (count($errors) == 0) {

			//~ $title = 'Zmiana w zadaniu';
			//~ $exec = $data['executor'];
			//~ $subject = "[$conf[title]] $title: #$issue_id #z$tid";
			//~ $to = $usro->name($exec).' <'.$usro->email($exec).'>';
			//~ if (isset($nparams['id']))
				//~ $body = "$uri?id=".$this->id('issue_task', 'id', $issue_id, 'tid', $tid);
			//~ else
				//~ $body = "$uri?id=".$this->id('show_task', 'tid', $tid);
				
			//~ $this->helper->mail($to, $subject, $body);
			
			//~ $cause_id = $_POST['cause_id'];
			//~ if (!isset($nparams['id']))
				//~ header("Location: ?id=bez:show_task:tid:$tid");
			//~ elseif ($cause_id == '')
				//~ header("Location: ?id=bez:issue_task:id:$issue_id:tid:$tid");
			//~ else
				//~ header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$tid");
		//~ }
		//~ $value = $_POST;
		try {
			$task->set_data($_POST);
			if ($task->any_errors()) {
				$errors = $this->validator->get_errors();
				$value = $_POST;
			} else {
				$this->model->save_task($task);
				$bezcache->task_toupdate($task->id);
								
								
				$cause_id = $_POST['cause_id'];
				if (!isset($nparams['id'])) {
					header("Location: ?id=bez:show_task:tid:$tid");
				} elseif ($cause_id == '') {
					header("Location: ?id=bez:issue_task:id:$issue_id:tid:$tid");
				} else {
					header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$tid");
				}
			} 
		} catch (Exception $e) {
			echo nl2br($e);
		}
	}
	$template['task_button'] = $bezlang['change_task_button'];
	if (isset($nparams['id']))
		$template['task_action'] = $this->id('task_form', 'id', $issue_id, 'cid', $cause_id, 'tid', $tid, 'action', 'update');
	else
		$template['task_action'] = $this->id('task_form_plan', 'tid', $tid, 'action', 'update');
		
/*dodawania*/
} else {
	if (count($_POST) > 0) {			
		//~ $data = array('reporter' => $INFO['client'], 'date' => time(), 'issue' => $issue_id, 'cause' => $cause_id);
		//~ $data = $tasko->add($_POST, $data);
		//~ if (count($errors) == 0) {
			//~ $tid = $tasko->lastid();
			//~ $title = 'Dodano zadanie';
			//~ $exec = $data['executor'];
			//~ $subject = "[$conf[title]] $title: #$issue_id #z$tid";
			//~ $to = $usro->name($exec).' <'.$usro->email($exec).'>';
			//~ $body = "$uri?id=".$this->id('issue_task', 'id', $issue_id, 'tid', $tid);
			//~ $this->helper->mail($to, $subject, $body);
			
			//~ if ($cause_id == '')
				//~ header("Location: ?id=bez:issue_task:id:$issue_id:tid:$tid");
			//~ else
				//~ header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$tid");
		//~ } 
		//~ $value = $_POST;
		try {
			$task = $this->model->create_task_object('0', $issue_id);
			$task->set_data($_POST);
			if ($task->any_errors()) {
				$errors = $this->validator->get_errors();
				$value = $_POST;
			} else {
				$this->model->save_task($task);			
				if ($cause_id == '') {
					header("Location: ?id=bez:issue_task:id:$issue_id:tid:$tid");
				} else {
					header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$tid");
				}
			} 
		} catch (Exception $e) {
			echo nl2br($e);
		}
	} else {
		$value['all_day_event'] = '1';
	}
	$template['task_button'] = $bezlang['add'];
	$template['task_action'] = $this->id('task_form', 'id', $issue_id, 'cid', $cause_id, 'action', 'add');
}

if (isset($nparams['id'])) {
	$isso = new Issues();
	$template['issue'] = $isso->get($issue_id);

	//~ $template['anytasks'] = $tasko->any_task($issue_id);
	//~ $template['opentasks'] = $tasko->any_open($issue_id);
	//~ $template['cause_without_task'] = $isso->cause_without_task($issue_id);
}

$template['users'] = $usro->get();

$tasktypes = $tasktypeso->get();
$template['tasktypes'] = $tasktypes;
