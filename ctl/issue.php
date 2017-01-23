<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";


$isso = new Issues();
//~ $como = new Comments();
$issue_id = (int)$params[1];

$issue = $this->model->issues->get_one($issue_id);

try {
	if (isset($nparams['action'])) {
		$action = $nparams['action'];
		$redirect = false;
		if ($action == 'commcause_add') {			
			$commcause = $this->model->commcauses->create_object(array(
				'issue' => $issue_id
			));
			
			$commcause->set_data($_POST);
			$this->model->commcauses->save($commcause);
			
			$issue->add_participant($INFO['client']);
			$issue->update_last_activity();
			$this->model->issues->save($issue);
			
			$redirect = true;
		} else if ($action == 'subscribe') {
			$issue->add_subscribent($INFO['client']);
			$this->model->issues->save($issue);
			
		} else if ($action == 'unsubscribe') {
			$issue->remove_subscribent($INFO['client']);
			$this->model->issues->save($issue);
			
		} else if ($action == 'commcause_delete') {
			$kid = (int)$nparams['kid'];
			$commcause = $this->model->commcauses->get_one($kid);
			
			$this->model->commcauses->delete($commcause);
			
			$issue->update_last_activity();
			$this->model->issues->save($issue);
			
			//~ $redirect = true;
		} else if ($action == 'comment_edit') {
			$kid = (int)$params[5];
			$template['comment_button'] = $bezlang['change_comment_button'];
			$template['comment_action'] = "comment_update:kid:$kid";
			$template['comment_id'] = $kid;

			$value = $como->getone($kid);
			if (is_null($value))
				$redirect = true;
		} else if ($action == 'comment_update') {
			$kid = (int)$params[5];
			$data = $como->update($_POST, array(), $kid);
			$template['comment_button'] = $bezlang['change_comment_button'];
			$template['comment_action'] = "comment_update:kid:$kid";
			$template['comment_id'] = $kid;
			
			$issue->update_last_activity();
			$this->model->issues->save($issue);

			$redirect = true;
		} else if ($action == 'reopen') {
			$issue->update_last_activity();
			$this->model->issues->save($issue);
			
			$isso->reopen($issue_id);
		}
		if ($redirect && count($errors) == 0)
			header("Location: ?id=bez:issue:id:$issue_id");

		if ($action == 'comment_edit' || $action == 'comment_update') {
		}
	}
} catch (Exception $e) {
	echo nl2br($e);
}

if (!isset($template[comment_action])) {
	$template['comment_button'] = $bezlang['add'];
	$template['comment_action'] = 'comment_add';
}

$tasko = new Tasks();
$template['anytasks'] = $tasko->any_task($issue_id);
$template['opentasks'] = $tasko->any_open($issue_id);
$template['cause_without_task'] = $isso->cause_without_task($issue_id);

$template['issue'] = $isso->get($issue_id);
//~ $template['comments'] = $como->get($issue_id);

//new way
$template['issue_object'] = $this->model->issues->get_one($issue_id);
$template['commcauses'] = $this->model->commcauses->get_all($issue_id);
$template['corrections'] = $this->model->tasks->get_all(array(
	'issue' => $issue_id,
	'action' => 0,
));


