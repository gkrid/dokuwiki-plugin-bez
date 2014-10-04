<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/rootcauses.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";
include_once DOKU_PLUGIN."bez/models/taskstates.php";

$como = new Comments();
$causo = new Causes();
$usro = new Users();
$tasko = new Tasks();
$taskao = new Taskactions();
$taskso = new Taskstates();

//dwa switche do połączenia
switch ($params[1]) {
	case 'edit_comment':
		$value = $como->getone($params[2]);
		break;
	case 'edit_cause':
		$value = $causo->getone($params[2]);
		break;
	case 'edit_task':
		$value = $causo->getone($params[2]);
		break;
}

if (isset($_POST['event'])) {

	$event = $_POST['event'];

	$o = NULL;
	$data = array('reporter' => $usro->get_nick(), 'date' => time(), 'issue' => $params[0]);
	switch ($event) {
		case 'comment':
			$o = $como;
		break;
		case 'cause':
			$o = $causo;
		break;
		case 'task':
			$o = $tasko;
			$data['state'] = $taskso->id('opened');
		break;
	}

	if ($o != NULL) {
		if ($params[1] == 'edit_'.$event)
			$como->update($_POST, $data, $params[2]);
		else 
			$como->add($_POST, $data);

		if (count($errors) > 0)
			$value = $_POST;
		else
			$redirect = '?id=bez:issue_show:'.$params[0].'#bez_'.$event.'_'.$como->lastid();
	}
}

$isso = new Issues();

$template['issue'] = $isso->get($params[0]);
$template['issue']['description'] = $helper->wiki_parse($template['issue']['description']);

$template['comments'] = $como->get($params[0]);
$template['causes'] = $causo->get($params[0]);
$template['tasks'] = $tasko->get($params[0]);

$rootco = new Rootcauses();
$template['rootcauses'] = $rootco->get();


/*powinno iść przez value - w przypadku edycji*/
if ($params[1] == 'edit_comment')
	$template['comment_button'] = $bezlang['change_comment_button'];
else
	$template['comment_button'] = $bezlang['add'];

if ($params[1] == 'edit_cause')
	$template['cause_button'] = $bezlang['change_cause_button'];
else
	$template['cause_button'] = $bezlang['add'];
/*koniec*/

$template['user_is_coordinator'] = $usro->is_coordinator();
$template['users'] = $usro->get();
$template['taskactions'] = $taskao->get();

$template['closed'] = false;


