<?php
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$issue_id = $nparams[id];
$action = $nparams[action];

if	(!$helper->user_editor() ||
	($issue_id != NULL && !$helper->user_coordinator($issue_id))) {
	$errors[] = $bezlang['error_issue_report'];
	$controller->preventDefault();
} 

$isso = new Issues();
$usro = new Users();
$tasko = new Tasks();

if ($issue_id != NULL) {
	$clean = $isso->get_clean($issue_id);
}

if (count($_POST) > 0) {
	if ($action == 'update') {
		$updated = $isso->update($_POST, array(), $issue_id);
		if (count($errors) == 0 && !in_array($updated['coordinator'], $isso->coord_special)) {			
			$coord = $updated['coordinator'];
						
			$issue = $this->model->issues->get_one($issue_id);
			$issue->add_participant($coord);
			//Don't update last activiti on issue change
			//$issue->update_last_activity();
			$this->model->issues->save($issue);

			$issto = new Issuetypes();
			$types = $issto->get();
			$type = $types[$updated['type']];

			$to = $usro->name($coord).' <'.$usro->email($coord).'>';
			$subject = "[$conf[title]] #".$isso->lastid()." $type";
			$body = "Zmiana w problemie: $uri".$this->issue_uri($isso->lastid());
			$this->helper->mail($to, $subject, $body);
		}
	} else {
		$data = array('reporter' => $usro->get_nick(), 'date' => time());

		$stao = new States();
		if ($_POST['coordinator'] == NULL) {
			$data['coordinator'] = '-proposal';
		}
		$data['state'] = $stao->id('opened');

		$inserted = $isso->add($_POST, $data);
		if (count($errors) == 0 && !in_array($inserted['coordinator'], $isso->coord_special)) {
			$coord = $updated['coordinator'];
						
			$issto = new Issuetypes();
			$types = $issto->get();
			$type = $types[$inserted['type']];

			$to = $usro->name($coord).' <'.$usro->email($coord).'>';
			$subject = "[$conf[title]] #".$isso->lastid()." $type";
			$body = "Zostałeś przypisany do problemu: $uri".$this->issue_uri($isso->lastid());
			$this->helper->mail($to, $subject, $body);
		}
	}
	$value = $_POST;
	if (count($errors) == 0)
		header('Location: ?id='.$this->id('issue', 'id', $isso->lastid()));
} elseif ($issue_id != NULL) {
	$value = $clean;
	$template['any_task_open'] = $tasko->any_open($issue_id);
	$template['anytasks'] = $tasko->any_task($issue_id);
	$action = 'update';
} else {
	$action = 'add';
}


$template['action'] = $action;

$isstyo = new Issuetypes();
$template['issue_types'] = $isstyo->get();


$template['user_admin'] = $helper->user_admin();
if ($issue_id != NULL) 
	$template['user_coordinator'] = $helper->user_coordinator($issue_id);
$template['nicks'] = $usro->get();

$template['uri'] = $uri;
$template['issue_id'] = $issue_id;

if ($issue_id != NULL) {
	$state = $isso->get_state($clean);
	$priority = $clean['priority'];
	if ($priority == NULL) {
		$priority = 'None';
	}
	$template['priority'] = $priority;
	$template['state'] = $state['state'];
	$template['raw_state'] = $state['raw_state'];

} else {
	$template['priority'] = 'None';
}

