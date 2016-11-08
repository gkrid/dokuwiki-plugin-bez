<?php
include_once DOKU_PLUGIN."bez/models/bezcache.php";


if (isset($nparams['tid'])) {
	
	$action = $nparams['action'];
	$tid = (int)$nparams['tid'];
	$task = $this->model->tasks->get_one($tid);
	$template['auth_level'] = $task->get_level();
	
	if (!$action) {
		$action = 'edit';
	}

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
				$bezcache = new Bezcache();	
				$bezcache->task_toupdate($task->id);
								
				header("Location: ?id=bez:show_task:tid:".$task->id);
			} 
		} catch (Exception $e) {
			echo nl2br($e);
		}
	}

	$template['task_button'] = $bezlang['change_task_button'];
	$template['task_action'] = $this->id('task_report', 'tasktype', $nparams['tasktype'], 'tid', $task->id, 'action', 'update');
	
/*dodawania*/
} else {
	$task = $this->model->tasks->create_object(
							array('tasktype' => $nparams['tasktype']));
	$template['auth_level'] = $task->get_level();
	$value['tasktype'] = $nparams['tasktype'];
	
	if (count($_POST) > 0) {
		try {
			//checkboxes 
			if (!isset($_POST['all_day_event'])) {
				$_POST['all_day_event'] = '0';
			}
			
			$task->set_data($_POST);
			//update tasktype for admins
			$task->set_acl($_POST);
			
			if ($task->any_errors()) {
				$errors = $task->get_errors();
				$value = $_POST;
			} else {
				$tid = $this->model->tasks->save($task);
				
				$tid = $this->model->tasks->save($task);
				$title = 'Dodano zadanie';
				$exec = $task->executor;
				$subject = "[$conf[title]] $title: #z$tid";
				$to = $this->model->users->get_user_full_name($exec).' <'.$this->model->users->get_user_email($exec).'>';
				$body = "$uri?id=".$this->id('show_task', 'tid', $tid);
				$this->helper->mail($to, $subject, $body);
				
				header("Location: ?id=bez:show_task:tid:$tid");
			} 
		} catch (Exception $e) {
			echo nl2br($e);
		}
	} else {
		$value['all_day_event'] = '1';
	}
	
	if (isset($nparams['duplicate'])) {
		$tid = (int)$nparams['duplicate'];
		$task = $this->model->tasks->get_one($tid);
		$value = $task->get_assoc();
	}
	$template['task_button'] = $bezlang['add'];
	$template['task_action'] = $this->id('task_report', 'tasktype', $nparams['tasktype']);
}


$template['users'] = $this->model->users->get_all();
$template['tasktypes'] = $this->model->tasktypes->get_all();
$template['tasktype_name'] = $this->model->tasktypes->get_one($nparams['tasktype'])->type;
