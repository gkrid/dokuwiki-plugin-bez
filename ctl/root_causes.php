<?php
include_once DOKU_PLUGIN."bez/models/rootcauses.php";

if( ! $helper->user_admin() ) {
	$errors[] = $bezlang['error_no_permission'];
	$controller->preventDefault();
} 

$action = $params[1];
$rootco = new Rootcauses();

$rootcauses = $rootco->get_clean();
if (count($_POST) > 0) {
	if ($action == 'add') {
		$rootco ->add($_POST, $data);
		if (count($errors) == 0)
			header('Location: ?id=bez:root_causes');

	} else if ($action == 'update') {
		$id = (int) $params[2];
		$row = $rootco->get_one($id);
		if (count($row) > 0) {
			$rootco->update($_POST, $id);

			if (count($errors) == 0)
				header('Location: ?id=bez:root_causes');
		}
	}
	$value = $_POST;
} else if ($action == 'edit') {
	$id = (int) $params[2];
	$template['edit'] = $id;
	$row = $rootco->get_one($id);
	if (count($row) > 0) {
		$value['pl'] = $row['pl'];
		$value['en'] = $row['en'];
	} else 
		unset($template['edit']);
} else if ($action == 'clean') {
	$rootco->clean_empty();
}

$template['rootcauses'] = $rootcauses;
$template['uri'] = $uri;

