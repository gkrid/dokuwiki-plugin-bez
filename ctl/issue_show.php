<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/comments.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/rootcauses.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";
include_once DOKU_PLUGIN."bez/models/taskstates.php";
include_once DOKU_PLUGIN."bez/models/tokens.php";

$como = new Comments();
$causo = new Causes();
$stao = new States();
$usro = new Users();
$tasko = new Tasks();
$taskao = new Taskactions();
$taskso = new Taskstates();

$issue_id = $params[0];
$action = $params[1];
$table = $params[2];
$id = $params[3];

$objects = array(
	'comment'	=> $como,
	'cause'		=> $causo,
	'task'		=> $tasko
);
/*global: $template, $bezlang, $value, $errors, $uri*/

function post_db($obj, $errors, $table, $issue_id) {
	global $value;
	$keys = array(
		'comment'	=> 'k',
		'cause'		=> 'p',
		'task'		=> 'z'
	);
	if (count($errors) > 0) {
		$value = $_POST;
		return '';
	}

	$archon = '';
	if ($obj->lastid() >= 0)
		$archon = '#'.$keys[$table].$obj->lastid();

	return $uri . '?id=bez:issue_show:'.$issue_id.$archon;
}

/*sprawdzamy czy zapytania mają sens*/
if (array_key_exists($table, $objects)) {
	$obj = $objects[$table];
	/*pobierz dane z bazy jeżeli aktualną akcją jest któregoś z rekordów*/
	switch($action) {
		case 'edit':
			$value = $obj->getone($id);
			break;
		case 'add':
			$data = array('reporter' => $usro->get_nick(), 'date' => time(), 'issue' => $issue_id);
			$obj->add($_POST, $data);
			$redirect = post_db($obj, $errors, $table, $issue_id);
			break;
		case 'update':
			$obj->update($_POST, array(), $id);
			$redirect = post_db($obj, $errors, $table, $issue_id);
			break;
		case 'delete':
			$obj->delete($id);
			$redirect = post_db($obj, $errors, $table, $issue_id);
			break;
	}
	if ($redirect != '')
		header('Location: '.$redirect);
}


$isso = new Issues();

$template['uri'] = $uri . '?id=bez:issue_show:'.$issue_id;

$template['issue'] = $isso->get($issue_id);
$template['issue']['description'] = $helper->wiki_parse($template['issue']['description']);
$template['closed'] = !$isso->opened($issue_id);
$template['successfully_closed'] = $stao->closed($template['issue']['state']);

$template['closed_com'] = str_replace('%d', $helper->string_time_to_now($template['issue']['last_mod']), $bezlang['issue_closed_com']);

$template['comments'] = $como->get($issue_id);
$template['causes'] = $causo->get($issue_id);
$template['tasks'] = $tasko->get($issue_id);

$rootco = new Rootcauses();
$template['rootcauses'] = $rootco->get();

/*user is coordinator or admin*/
$template['user_is_coordinator'] = $helper->user_coordinator($issue_id);
$template['user_editor'] = $helper->user_editor();
$template['users'] = $usro->get();
$template['user'] = $INFO['client'];
$template['taskactions'] = $taskao->get();

$template['issue_opened'] = !$template['closed'];

/*mailto ustawienia*/
$toko = new Tokens();
$template['token_link'] = $template['uri'].'&token='.$toko->get('bez:issue_show:'.$issue_id);

/*Domyślne przyciski*/
$template['comment_button'] = $bezlang['add'];
$template['comment_action'] = 'add:comment';

$template['cause_button'] = $bezlang['add'];
$template['cause_action'] = 'add:cause';

$template['task_button'] = $bezlang['add'];
$template['task_action'] = 'add:task';

$template['task_states'] = $taskso->get();

/*Ruter*/
$router = array(
	'comment' => array(
		'edit' => function ($id) {
			global $template, $bezlang;
			$template['comment_button'] = $bezlang['change_comment_button'];
			$template['comment_action'] = 'update:comment:'.$id;
		}
	),
	'cause' => array(
		'edit' => function ($id) {
			global $template, $bezlang;
			$template['cause_button'] = $bezlang['change_cause_button'];
			$template['cause_action'] = 'update:cause:'.$id;
		}
	),
	'task' => array(
		'edit' => function ($id) {
			global $template, $bezlang;
			$template['task_button'] = $bezlang['change_task_button'];
			$template['task_action'] = 'update:task:'.$id;
		}
	)
);

if ($router[$table]) {
	$f = $router[$table][$action];
	if ($f)
		$f($id);
}
