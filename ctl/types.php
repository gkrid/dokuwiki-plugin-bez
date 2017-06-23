<?php
//include_once DOKU_PLUGIN."bez/models/issuetypes.php";

if ($this->model->acl->get_level() < BEZ_AUTH_ADMIN) {
    throw new PermissionDeniedException();
}

$action = $nparams['action'];
$template['edit'] = -1;


try {
    $types = $this->model->issuetypes->get_all(array('refs'));
    $issuetype = $this->model->issuetypes->create_object();
        
    if ($action === 'update') {
        $issuetype = $this->model->issuetypes->get_one($nparams['id']);
    }

    if (count($_POST) > 0) {

        $issuetype->set($_POST);
        $this->model->issuetypes->save($issuetype);
        header('Location: ?id=bez:types');

    } else if ($action === 'edit') {
        $id = (int) $nparams['id'];
        $template['edit'] = $id;

        $issuetype = $this->model->issuetypes->get_one($nparams['id']);
        $value = $issuetype->get_assoc();

    } else if ($action === 'remove') {
        $issuetype = $this->model->issuetypes->get_one($nparams['id']);
        $this->model->issuetypes->delete($issuetype);
    } 
} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
} catch (DBException $e) {
	echo nl2br($e);
//	header("Location: ?id=bez:issue:id:$issue_id");
}

$template['issuetype'] = $issuetype;

$template['types'] = $types;
$template['uri'] = $uri;


//$action = $params[1];
//$typo = new Issuetypes();
//
//$types = $typo->get_clean();
//if (count($_POST) > 0) {
//	if ($action == 'add') {
//		$typo->add($_POST, $data);
//		if (count($errors) == 0)
//			header('Location: ?id=bez:types');
//
//	} else if ($action == 'update') {
//		$id = (int) $params[2];
//		$row = $typo->get_one($id);
//		if (count($row) > 0) {
//			$typo->update($_POST, $id);
//
//			if (count($errors) == 0)
//				header('Location: ?id=bez:types');
//		}
//	}
//	$value = $_POST;
//} else if ($action == 'edit') {
//	$id = (int) $params[2];
//	$template['edit'] = $id;
//	$row = $typo->get_one($id);
//	if (count($row) > 0) {
//		$value['pl'] = $row['pl'];
//		$value['en'] = $row['en'];
//	} else 
//		unset($template['edit']);
//} else if ($action == 'clean') {
//	$typo->clean_empty();
//	header('Location: ?id=bez:types');
//}


