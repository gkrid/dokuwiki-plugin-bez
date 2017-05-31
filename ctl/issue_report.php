<?php
//~ include_once DOKU_PLUGIN."bez/models/issuetypes.php";
//~ include_once DOKU_PLUGIN."bez/models/users.php";
//~ include_once DOKU_PLUGIN."bez/models/issues.php";
//~ include_once DOKU_PLUGIN."bez/models/states.php";
//~ include_once DOKU_PLUGIN."bez/models/tasks.php";


if	(!$helper->user_viewer()) {
//	$errors[] = $bezlang['error_issues'];
//	$controller->preventDefault();
    throw new PermissionDeniedException();
} 

if (isset($nparams['id']) && is_numeric($nparams['id'])) {
	$issue_id = (int)$nparams['id'];
	$issue = $this->model->issues->get_one($issue_id);
	
	$template['issue'] = $issue;
	$template['issue_id'] = $issue->id;
	$template['priority'] = $issue->priority;
	$template['user_level'] = $issue->get_level();
} else {
	$template['issue'] = NULL;
	$template['issue_id'] = '';
	$template['priority'] = 'None';
	$template['user_level'] = $this->model->issues->get_level();
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
		
		$issue = $this->model->issues->create_object($_POST);
		//update tasktype for admins
		
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
} catch (DBException $e) {
	echo nl2br($e);
}

$template['issuetypes'] = $this->model->issuetypes->get_all();
$template['nicks'] = $this->model->users->get_all();
