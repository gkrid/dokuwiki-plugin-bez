<?php

if ($this->model->acl->get_level() < BEZ_AUTH_USER) {
    throw new PermissionDeniedException();
}


if (isset($nparams['id']) && is_numeric($nparams['id'])) {
	$issue_id = (int)$nparams['id'];
	$issue = $this->model->issues->get_one($issue_id);
	
	$template['issue'] = $issue;
	$template['priority'] = $issue->priority;
} else {
	$template['issue'] = $this->model->issues->create_dummy_object();
	$template['priority'] = 'None';
}

$action = '';
if (isset($nparams['action'])) {
	$action = $nparams['action'];
}

try {
	if ($action === 'edit') {
		if (!isset($issue)) {
			throw new Exception('there is now row with given id');
		}
		$template['form_action'] = 'update';
		$value = $issue->get_assoc();
	} elseif ($action === 'update') {
		$template['form_action'] = 'update';
        
        $prev_coordiantor = $issue->coordinator;
        
		$issue->set_data($_POST);
        
		$issue->add_participant($issue->coordinator);
        
        //save to get ID!!!
        $this->model->issues->save($issue);

        if ($issue->coordinator !== '-proposal' &&
            $INFO['client'] !== $issue->coordinator &&
            $issue->coordinator != $prev_coordiantor) {
            //coordinator becomes subscribent automaticly
            $issue->add_subscribent($issue->coordinator);
            $this->model->issues->save($issue);
            
            $issue->mail_inform_coordinator();
        }
        
		header('Location: ?id='.$this->id('issue', 'id', $issue->id));
	} elseif ($action === 'add') {
		$template['form_action'] = 'add';
		
        $defaults = array();
        if ($this->model->acl->get_level() >= BEZ_AUTH_LEADER) {
            $defaults['coordinator'] = $_POST['coordinator'];
        }
		$issue = $this->model->issues->create_object($defaults);
		
        $data = array(
            'type' => $_POST['type'],
            'title' => $_POST['title'],
            'description' => $_POST['description']
        );
        $issue->set_data($data);
		
        //save to get ID!!!
        $this->model->issues->save($issue);
        
        if ($issue->coordinator !== '-proposal' &&
            $INFO['client'] !== $issue->coordinator) {
            //coordinator becomes subscribent automaticly
            $issue->add_subscribent($issue->coordinator);
            $this->model->issues->save($issue);
            
            $issue->mail_inform_coordinator();
        }
        
        
		header('Location: ?id='.$this->id('issue', 'id', $issue->id));

	} else {
		$template['form_action'] = 'add';
	}

} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
}

$template['issuetypes'] = $this->model->issuetypes->get_all();
$template['nicks'] = $this->model->users->get_all();
