<?php
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/tasktypes.php";
include_once DOKU_PLUGIN."bez/models/users.php";

$tasktypeso = new Tasktypes();
$usro = new Users();
$tasko = new Tasks();

/*edycja*/
if (isset($nparams['tid'])) {
	$action = $nparams['action'];
	$tid = (int)$nparams['tid'];
	$task = $tasko->getone($tid);
	$taskso = new Taskstates();
	$task_states = $taskso->get();

	$template['raw_state'] = $task['state'];
	$template['state']  = $task_states[$task['state']];
	$template['causes'] = $causo->get($issue_id);
	
	
	if (!$action)
		$action = 'edit';

	if ($action == 'edit') {
		$value = $task;
		$value['cause_id'] = $task['cause'];
	} else if ($action == 'update') {
		$cause_id = $_POST['cause_id'];
		if ($cause_id != '')
			$cause_id = (int)$cause_id;
		$data = $tasko->update($_POST, array('cause' => $cause_id), $tid);
		if (count($errors) == 0) {

			$title = 'Zmiana w zadaniu';
			$exec = $data['executor'];
			$subject = "[$conf[title]] $title: #$issue_id #z$tid";
			$to = $usro->name($exec).' <'.$usro->email($exec).'>';
			$body = "$uri?id=".$this->id('issue_task', 'id', $issue_id, 'tid', $tid);
			$this->helper->mail($to, $subject, $body);
			
			$cause_id = $_POST['cause_id'];
			if ($cause_id == '')
				header("Location: ?id=bez:issue_task:id:$issue_id:tid:$tid");
			else
				header("Location: ?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$tid");
		}
		$value = $_POST;
	}
	$template['task_button'] = $bezlang['change_task_button'];
	$template['task_action'] = $this->id('task_form', 'id', $issue_id, 'cid', $cause_id, 'tid', $tid, 'action', 'update');
/*dodawania*/
} else {
	if (count($_POST) > 0) {
		$data = array('reporter' => $INFO['client'], 'date' => time());
		$data = $tasko->add($_POST, $data);
		if (count($errors) == 0) {
			$tid = $tasko->lastid();
			
			$title = 'Dodano zadanie';
			$exec = $data['executor'];
			$subject = "[$conf[title]] $title: #$issue_id #z$tid";
			$to = $usro->name($exec).' <'.$usro->email($exec).'>';
			$body = "$uri?id=".$this->id('task', 'tid', $tid);
			$this->helper->mail($to, $subject, $body);
			
			header("Location: ?id=bez:show_task:tid:$tid");
		} 
		$value = $_POST;
	}
	if (isset($nparams['duplicate'])) {
		$tid = (int)$nparams['duplicate'];
		$task = $tasko->getone($tid);
		$value = $task;
	}
	$template['task_button'] = $bezlang['add'];
	$template['task_action'] = $this->id('task_report', 'action', 'add');
}


$tasktypes = $tasktypeso->get();
$template['tasktypes'] = $tasktypes;

$template['users'] = $usro->get();
