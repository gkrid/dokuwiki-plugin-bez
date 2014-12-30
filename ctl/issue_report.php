<?php
include_once DOKU_PLUGIN."bez/models/entities.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/users.php";

if (!$helper->user_editor()) {
	$errors[] = $bezlang['error_issue_report'];
	$controller->preventDefault();
}

$usro = new Users();
if (count($_POST) > 0) {
	include_once DOKU_PLUGIN."bez/models/issues.php";
	include_once DOKU_PLUGIN."bez/models/states.php";

	$stao = new States();
	if ($_POST['coordinator'] == NULL)
		$state = $stao->id('proposal');
	else
		$state = $stao->id('opened');

	$data = array('state' => $state, 'reporter' => $usro->get_nick(), 'date' => time());

	$isso = new Issues();
	$isso->add($_POST, $data);
	$value = $_POST;
	if (count($errors) == 0)
		header('Location: ?id=bez:issue_show:'.$isso->lastid());
}

$ento = new Entities();
$template['entities'] = $ento->get_list();
$isstyo = new Issuetypes();
$template['issue_types'] = $isstyo->get();

$template['user_admin'] = $helper->user_admin();
$template['nicks'] = $usro->get();
