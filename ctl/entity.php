<?php
include_once DOKU_PLUGIN."bez/models/entities.php";

if (!$helper->user_admin()) {
	$errors[] = $bezlang['error_entity'];
	$controller->preventDefault();
}

$ento = new Entities();
if (count($_POST) > 0) {
	$ento->save($_POST);
	if (count($errors) == 0)
		$template['confirm'] = true;
} else {
	$value['entities'] = $ento->get_string();
}

$template['uri'] = $uri . '?id=bez:entity';
