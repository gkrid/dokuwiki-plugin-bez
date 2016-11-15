
<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$issue_id = (int)$params[1];

$isso = new Issues();
$tasko = new Tasks();
$usro = new Users();

$anytasks = $tasko->any_task($issue_id);

$template['anytasks'] = $anytasks;
$template['opentasks'] = $tasko->any_open($issue_id);
$template['issue'] = $isso->get($issue_id);
$template['close'] = true;
$template['issue_id'] = $issue_id ;

if (count($_POST) > 0) {
	$updated = $isso->close($_POST, $issue_id);
	if (count($errors) == 0 && !in_array($updated['coordinator'], $isso->coord_special)) {
		$coord = $updated['coordinator'];

		$issto = new Issuetypes();
		$types = $issto->get();
		$type = $types[$updated['type']];
		
		if ($anytasks)
			$action = $bezlang['issue_closed'];
		else
			$action = $bezlang['issue_rejected'];
		
		$to = $usro->name($coord).' <'.$usro->email($coord).'>';
		$subject = '['.$helper->get_wiki_title()."] $action: #".$isso->lastid()." $type";
		$body = $uri.$this->issue_uri($isso->lastid());
		$this->helper->mail($to, $subject, $body);
	}
	
	$value = $_POST;
	if (count($errors) == 0)
		header('Location: ?id='.$this->id('issue', 'id', $isso->lastid()));
} else {
	$value['opinion'] = $template['issue']['raw_opinion'];
}

$template['issue_object'] = $this->model->issues->get_one($issue_id);
