<?php
include DOKU_PLUGIN."bez/models/entities.php";
include DOKU_PLUGIN."bez/models/issuetypes.php";

if (count($_POST) > 0) {
	include DOKU_PLUGIN."bez/models/issues.php";
	$isso = new Issues();
	$isso->add($_POST);
	$value = $_POST;
	if (count($errors) == 0)
		$redirect = '?id=bez:issue_show:'.$isso->lastid();
}

$ento = new Entities();
$template['entities'] = $ento->get();
$isstyo = new Issuetypes();
$template['issue_types'] = $isstyo->get();
