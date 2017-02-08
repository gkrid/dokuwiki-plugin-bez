<?php
//~ include_once DOKU_PLUGIN."bez/models/issues.php";
//~ include_once DOKU_PLUGIN."bez/models/comments.php";
//~ include_once DOKU_PLUGIN."bez/models/tasks.php";


//~ $isso = new Issues();
//~ $como = new Comments();
$issue_id = (int)$nparams['id'];
$issue = $this->model->issues->get_one($issue_id);
$template['commcause_action'] = 'commcause_add';
//placeholder for adding new records
$template['commcause_id'] = '-1';

$template['task_action'] = 'task_correction_add';
$template['task_id'] = '-1';
$template['auth_level'] = $issue->get_level();

try {
	$action = '';
	if (isset($nparams['action'])) {
		$action = $nparams['action'];
		$redirect = false;
		
		if ($action === 'commcause_add') {			
			$commcause = $this->model->commcauses->create_object(array(
				'issue' => $issue_id
			));

			$commcause->set_data($_POST);
			$this->model->commcauses->save($commcause);
			
			$issue->add_participant($INFO['client']);
			$issue->add_subscribent($INFO['client']);
			
			$issue->update_last_activity();
			$this->model->issues->save($issue);
			
			$redirect = true;
		} elseif ($action === 'subscribe') {
			$issue->add_subscribent($INFO['client']);
			$this->model->issues->save($issue);
			
			$redirect = true;
		} elseif ($action === 'unsubscribe') {
			$issue->remove_subscribent($INFO['client']);
			$this->model->issues->save($issue);
			
			$redirect = true;
		} elseif ($action === 'commcause_delete') {
			$kid = (int)$nparams['kid'];
			$commcause = $this->model->commcauses->get_one($kid);
			
			$this->model->commcauses->delete($commcause);
			
			$issue->update_last_activity();
			$this->model->issues->save($issue);
			
			$redirect = true;
		} elseif ($action === 'commcause_edit') {
			$kid = (int)$nparams['kid'];
			$template['commcause_action'] = 'commcause_update';
			
			
			$commcause = $this->model->commcauses->get_one($kid);
			$template['commcause_id'] = $commcause->id;
			$value = $commcause->get_assoc();
			
		} elseif ($action === 'commcause_update') {
			$kid = (int)$nparams['kid'];
			$commcause = $this->model->commcauses->get_one($kid);
			$commcause->set_data($_POST);
			$this->model->commcauses->save($commcause);
			
			$issue->update_last_activity();
			$this->model->issues->save($issue);

			$redirect = true;
		} elseif ($action === 'issue_close') {
			$value['opinion'] = $template['issue']['raw_opinion'];
		} elseif ($action == 'issue_close_confirm') {
			$issue->set_state($_POST);
			$this->model->issues->save($issue);
			$redirect = true;
		} elseif ($action === 'reopen') {
			$issue->set_state(array('state' => '0'));
			$this->model->issues->save($issue);
 		} elseif (strpos($action, 'task') === 0) {
			$template['users'] = $this->model->users->get_all();
			$template['tasktypes'] = $this->model->tasktypes->get_all();
			
			if (count($_POST) > 0) {
				if (!isset($_POST['all_day_event'])) {
					$_POST['all_day_event'] = '0';
				}
			}
			
			if ($action === 'task_correction_add') {
				$template['task_action'] = $action;
				//~ $template['causes'] = $this->model->commcauses->get_all(array(
					//~ 'issue' => $issue_id,
					//~ 'type' => array('!=', '0'),
				//~ ));
				if (count($_POST) > 0) {
					$task = $this->model->tasks->create_object_issue(array(
						'issue' => $issue_id
					));
					$task->set_data($_POST);
					$this->model->tasks->save($task);
					
					$issue->add_participant($task->executor);
					$issue->add_subscribent($task->executor);
			
					$issue->update_last_activity();
					$this->model->issues->save($issue);
					
					$redirect = true;
				}
			} elseif ($action === 'task_commcause_add') {
				$template['task_action'] = $action;
				$template['kid'] = $nparams['kid'];
			}
		}
		
		if ($redirect) {
			header("Location: ?id=bez:issue:id:$issue_id");
		}
	}
} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
} catch (Exception $e) {
	echo nl2br($e);
	//~ header("Location: ?id=bez:issue:id:$issue_id");
}

//~ if (!isset($template[comment_action])) {
	//~ $template['comment_button'] = $bezlang['add'];
	//~ $template['comment_action'] = 'comment_add';
//~ }


//~ $tasko = new Tasks();
//~ $template['anytasks'] = $tasko->any_task($issue_id);
//~ $template['opentasks'] = $tasko->any_open($issue_id);
//~ $template['cause_without_task'] = $isso->cause_without_task($issue_id);

//~ $template['issue'] = $isso->get($issue_id);
//~ $template['comments'] = $como->get($issue_id);

//new way
$template['action'] = $action;
$template['issue'] = $this->model->issues->get_one($issue_id);
$template['commcauses'] = $this->model->commcauses->get_all($issue_id);
$template['corrections'] = $this->model->tasks->get_all(array(
	'issue' => $issue_id,
	'action' => 0,
));

