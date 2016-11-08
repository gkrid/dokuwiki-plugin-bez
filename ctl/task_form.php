<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/tasktypes.php";
include_once DOKU_PLUGIN."bez/models/bezcache.php";

$causo = new Causes();
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
	
	$task = $this->model->tasks->get_one($tid);
	$template['auth_level'] = $task->get_level();

	if (isset($nparams['id'])) {
		$template['causes'] = $causo->get($issue_id);
	}
	
	if (!$action)
		$action = 'edit';

	if ($action == 'edit') {
		$value = $task->get_assoc();
	} else if ($action == 'update') {
		try {
			//checkboxes 
			if (!isset($_POST['all_day_event'])) {
				$_POST['all_day_event'] = '0';
			}
			$task->set_data($_POST);
			$task->set_acl($_POST);
			
			if ($task->any_errors()) {
				$errors = $task->get_errors();
				$value = $_POST;
			} else {
				$this->model->tasks->save($task);
				$bezcache->task_toupdate($task->id);
								
								
				$cause_id = $task->cause;
				$issue_id = $task->issue;
				if ($cause_id == NULL) {
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
	
	if ($task->cause == NULL) {
		$template['task_action'] = $this->id('task_form', 'id', $task->issue, 'tid', $task->id, 'action', 'update');
	} else {
		$template['task_action'] = $this->id('task_form', 'id', $task->issue, 'cid', $task->cause, 'tid', $task->id, 'action', 'update');
	}


		
/*dodawania*/
} else {
	$defaults = array('issue' => $issue_id, 'cause' => $cause_id, 'tasktype' => $_POST['tasktype']);

	$task = $this->model->tasks->create_object($defaults);
		
	$template['auth_level'] = $task->get_level();
	


	if (count($_POST) > 0) {
		try {
			//checkboxes 
			if (!isset($_POST['all_day_event'])) {
				$_POST['all_day_event'] = '0';
			}
			$task->set_data($_POST);
			if ($task->any_errors()) {
				$errors = $task->get_errors();
				$value = $_POST;
			} else {
				$tid = $this->model->tasks->save($task);
				$issue_id = $task->issue;		
				$cause_id = $task->cause;
				
				$title = 'Dodano zadanie';
				$exec = $task->executor;
				$subject = "[$conf[title]] $title: #$issue_id #z$tid";
				$to = $this->model->users->get_user_full_name($exec).' <'.$this->model->users->get_user_email($exec).'>';
				if ($cause_id == '') {
					$body = "$uri?id=".$this->id('issue_task', 'id', $issue_id, 'tid', $tid);
				} else {
					$body = "$uri?id=".$this->id('issue_cause_task', 'id', $issue_id, 'cid', $cause_id, 'tid', $tid);
				}
				$this->helper->mail($to, $subject, $body);
				
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
	if ($task->cause == NULL) {
		$template['task_action'] = $this->id('task_form', 'id', $task->issue, 'action', 'add');
	} else {
		$template['task_action'] = $this->id('task_form', 'id', $task->issue, 'cid', $task->cause, 'action', 'add');
	}

}

if (isset($nparams['id'])) {
	$isso = new Issues();
	$template['issue'] = $isso->get($issue_id);
}

$template['users'] = $this->model->users->get_all();
$template['tasktypes'] = $this->model->tasktypes->get_all();
