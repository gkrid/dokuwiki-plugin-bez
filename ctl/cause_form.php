<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/rootcauses.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$causo = new Causes();
$issue_id = (int)$params[1];

$isso = new Issues();
$template['issue'] = $isso->get($issue_id);

/*edycja*/
if (isset($params[3])) {
	$action = $params[3];
	$cause_id = (int)$params[5];
	if ($action == 'edit') 
		$value = $causo->getone($cause_id);
	else if ($action == 'update') {
		$causo->update($_POST, array(), $cause_id);
		if (count($errors) == 0)
			header("Location: ?id=bez:issue_cause:id:$issue_id:cid:$cause_id");
	}
	$template['cause_button'] = $bezlang['change_cause_button'];
	$template['cause_action'] = $this->id('cause_form', 'id', $issue_id, 'action', 'update', 'cid', $cause_id);
/*dodawania*/
} else {
	if (count($_POST) > 0) {
		$data = array('reporter' => $INFO['client'], 'date' => time(), 'issue' => $issue_id);
		$data = $causo->add($_POST, $data);
		$cause_id = $causo->lastid();
		if (count($errors) == 0)
			header("Location: ?id=bez:issue_cause:id:$issue_id:cid:$cause_id");
		else
			$value = $_POST;
	} else {
		$value['potential'] = 0;
	}
	$template['cause_button'] = $bezlang['add'];
	$template['cause_action'] = $this->id('cause_form', 'id', $issue_id);
}

$rootco = new Rootcauses();
$template['rootcauses'] = $rootco->get();

$tasko = new Tasks();
$template['anytasks'] = $tasko->any_task($issue_id);
$template['opentasks'] = $tasko->any_open($issue_id);
$template['issue_object'] = $this->model->issues->get_one($issue_id);
