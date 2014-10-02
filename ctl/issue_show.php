<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/rootcauses.php";
include_once DOKU_PLUGIN."bez/models/users.php";

$como = new Comments();
$causo = new Causes();

switch ($params[1]) {
	case 'edit_comment':
		$value = $como->getone($params[2]);
		break;
	case 'edit_cause':
		$value = $causo->getone($params[2]);
		break;
}

if (isset($_POST['event'])) 
switch ($_POST['event']) {
	case 'comment':
		$usro = new Users();

		$como = new Comments();
		$data = array('reporter' => $usro->get_nick(), 'date' => time(), 'issue' => $params[0]);

		if ($params[1] == 'edit_comment')
			$como->update($_POST, $data, $params[2]);
		else 
			$como->add($_POST, $data);

		if (count($errors) > 0)
			$value = $_POST;
		else
			$redirect = '?id=bez:issue_show:'.$params[0].'#bez_comment_'.$como->lastid();

	break;
	case 'cause':
		$usro = new Users();

		$cauo = new Causes();
		$data = array('reporter' => $usro->get_nick(), 'date' => time(), 'issue' => $params[0]);

		if ($params[1] == 'edit_cause')
			$cauo->update($_POST, $data, $params[2]);
		else 
			$cauo->add($_POST, $data);

		if (count($errors) > 0)
			$value = $_POST;
		else
			$redirect = '?id=bez:issue_show:'.$params[0].'#bez_cause_'.$cauo->lastid();

	break;
}

$isso = new Issues();

$template['issue'] = $isso->get($params[0]);
$template['issue']['description'] = $helper->wiki_parse($template['issue']['description']);

$template['comments'] = $como->get($params[0]);
$template['causes'] = $causo->get($params[0]);

$rootco = new Rootcauses();
$template['rootcauses'] = $rootco->get();


if ($params[1] == 'edit_comment')
	$template['comment_button'] = $bezlang['change_comment_button'];
else
	$template['comment_button'] = $bezlang['add'];

if ($params[1] == 'edit_cause')
	$template['cause_button'] = $bezlang['change_cause_button'];
else
	$template['cause_button'] = $bezlang['add'];

$template['closed'] = false;


