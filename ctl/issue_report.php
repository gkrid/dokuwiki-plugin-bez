<?php
include_once DOKU_PLUGIN."bez/models/entities.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include DOKU_PLUGIN."bez/models/users.php";

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
		$redirect = '?id=bez:issue_show:'.$isso->lastid();
}

$ento = new Entities();
$template['entities'] = $ento->get();
$isstyo = new Issuetypes();
$template['issue_types'] = $isstyo->get();

$template['user_is_coordinator'] = $usro->is_coordinator();
$template['coordinators'] = $usro->coordinators();
