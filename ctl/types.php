<?php
include_once DOKU_PLUGIN."bez/models/issuetypes.php";

if	( ! $helper->user_admin() ) {
	$errors[] = $bezlang['error_no_permission'];
	$controller->preventDefault();
} 

$action = $params[1];
$typo = new Issuetypes();

$types = $typo->get_clean();
if (count($_POST) > 0) {
	if ($action == 'add') {
		$typo->add($_POST, $data);
		if (count($errors) == 0)
			header('Location: ?id=bez:types');

	} else if ($action == 'update') {
		$id = (int) $params[2];
		$row = $typo->get_one($id);
		if (count($row) > 0) {
			$typo->update($_POST, $id);

			if (count($errors) == 0)
				header('Location: ?id=bez:types');
		}
	}
	$value = $_POST;
} else if ($action == 'edit') {
	$id = (int) $params[2];
	$template['edit'] = $id;
	$row = $typo->get_one($id);
	if (count($row) > 0) {
		$value['pl'] = $row['pl'];
		$value['en'] = $row['en'];
	} else 
		unset($template['edit']);
} else if ($action == 'clean') {
	$typo->clean_empty();
	header('Location: ?id=bez:types');
}

$template['types'] = $types;
$template['uri'] = $uri;

