<?php

if	($this->model->tasktypes->get_level() < 20) {
	$errors[] = $bezlang['error_no_permission'];
	$controller->preventDefault();
} 

$action = $nparams['action'];

$template['edit'] = -1;
$types = $this->model->tasktypes->get_all(array('refs'));
if (count($_POST) > 0) {
	if ($action == 'add') {		
		$tasktype = $this->model->tasktypes->create_object();
	} else if ($action == 'update') {
		$tasktype = $this->model->tasktypes->get_one($nparams['id']);
	}
	try {
		$tasktype->set($_POST);
		
		if ($tasktype->any_errors()) {
			$errors = $this->validator->get_errors();
			$value = $_POST;
		} else {
			$this->model->tasktypes->save($tasktype);
			header('Location: ?id=bez:task_types');
		} 
	} catch (Exception $e) {
		echo nl2br($e);
	}
} else if ($action == 'edit') {
	$id = (int) $nparams['id'];
	$template['edit'] = $id;
	
	$tasktype = $this->model->tasktypes->get_one($nparams['id']);
	$value = $tasktype->get_assoc();
} else if ($action == 'clean') {
	$typo->clean_empty();
	header('Location: ?id=bez:task_types');
}

$template['types'] = $types;
$template['uri'] = $uri;

