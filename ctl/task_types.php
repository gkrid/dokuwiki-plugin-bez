<?php

if ($this->model->acl->get_level() < BEZ_AUTH_ADMIN) {
    throw new PermissionDeniedException();
}

$action = $nparams['action'];
$template['edit'] = -1;

try {
    $types = $this->model->tasktypes->get_all(array('refs'));

	if ($action === 'add') {		
		$tasktype = $this->model->tasktypes->create_object();
	} else if ($action === 'update') {
		$tasktype = $this->model->tasktypes->get_one($nparams['id']);
	}
    
    if (count($_POST) > 0) {
		$tasktype->set($_POST);
        $this->model->tasktypes->save($tasktype);
        
        header('Location: ?id=bez:task_types');
    } else if ($action === 'edit') {
        $id = (int) $nparams['id'];
        $template['edit'] = $id;
        
        $tasktype = $this->model->tasktypes->get_one($nparams['id']);
        $value = $tasktype->get_assoc();
        
    } else if ($action === 'remove') {
        $tasktype = $this->model->tasktypes->get_one($nparams['id']);
        $this->model->tasktypes->delete($tasktype);
    }
} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
}

$template['types'] = $types;
$template['uri'] = $uri;

