<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";

$como = new Comments();
$issue_id = (int)$params[1];

if (isset($params[3])) {
	$action = $params[3];
	$redirect = false;
	if ($action == 'comment_add') {
		$data = array('reporter' => $INFO['client'], 'date' => time(), 'issue' => $issue_id);
		$data = $como->add($_POST, $data);
		$redirect = true;
	} else if ($action == 'comment_delete') {
		$kid = (int)$params[5];
		$como->delete($kid);
		$redirect = true;
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

		$redirect = true;
	}
	if ($redirect && count($errors) == 0)
		header("Location: ?id=bez:issue:id:$issue_id");

	if ($action == 'comment_edit' || $action == 'comment_update') {
	}
}

if (!isset($template[comment_action])) {
	$template['comment_button'] = $bezlang['add'];
	$template['comment_action'] = 'comment_add';
}

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);
$template['comments'] = $como->get($issue_id);
