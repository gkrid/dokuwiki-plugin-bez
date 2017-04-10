<?php
//~ include_once DOKU_PLUGIN."bez/models/issuetypes.php";
//~ include_once DOKU_PLUGIN."bez/models/users.php";
//~ include_once DOKU_PLUGIN."bez/models/issues.php";
//~ include_once DOKU_PLUGIN."bez/models/states.php";
//~ include_once DOKU_PLUGIN."bez/models/tasks.php";



//~ if	(!$helper->user_editor() ||
	//~ ($issue_id != NULL && !$helper->user_coordinator($issue_id))) {
	//~ $errors[] = $bezlang['error_issue_report'];
	//~ $controller->preventDefault();
//~ } 

//~ $isso = new Issues();
//~ $usro = new Users();
//~ $tasko = new Tasks();

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

		//~ $updated = $isso->update($_POST, array(), $issue_id);
		//~ if (count($errors) == 0 && !in_array($updated['coordinator'], $isso->coord_special)) {			
			//~ $coord = $updated['coordinator'];
						
			//~ $issue = $this->model->issues->get_one($issue_id);
			//~ $issue->add_participant($coord);
			//~ //Don't update last activiti on issue change
			//~ //$issue->update_last_activity();
			//~ $this->model->issues->save($issue);

			//~ $issto = new Issuetypes();
			//~ $types = $issto->get();
			//~ $type = $types[$updated['type']];

			//~ $to = $usro->name($coord).' <'.$usro->email($coord).'>';
			//~ $subject = "[$conf[title]] #".$isso->lastid()." $type";
			//~ $body = "Zmiana w problemie: $uri".$this->issue_uri($isso->lastid());
			//~ $this->helper->mail($to, $subject, $body);
		//~ }
	} elseif ($action === 'add') {
		$template['form_action'] = 'add';
		
		//~ $data = array('reporter' => $usro->get_nick(), 'date' => time());

		//~ $stao = new States();
		//~ if ($_POST['coordinator'] == NULL) {
			//~ $data['coordinator'] = '-proposal';
		//~ }
		//~ $data['state'] = $stao->id('opened');

		//~ $inserted = $isso->add($_POST, $data);
		//~ if (count($errors) == 0 && !in_array($inserted['coordinator'], $isso->coord_special)) {
			//~ $coord = $updated['coordinator'];
						
			//~ $issto = new Issuetypes();
			//~ $types = $issto->get();
			//~ $type = $types[$inserted['type']];

			//~ $to = $usro->name($coord).' <'.$usro->email($coord).'>';
			//~ $subject = "[$conf[title]] #".$isso->lastid()." $type";
			//~ $body = "Zostałeś przypisany do problemu: $uri".$this->issue_uri($isso->lastid());
			//~ $this->helper->mail($to, $subject, $body);
		//~ }
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

	//~ if (count($errors) == 0) {
		//~ header('Location: ?id='.$this->id('issue', 'id', $isso->lastid()));

} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
} catch (Exception $e) {
	echo nl2br($e);
}

$template['issuetypes'] = $this->model->issuetypes->get_all();
$template['nicks'] = $this->model->users->get_all();

//~ if ($issue_id != NULL) {
	//~ $state = $isso->get_state($clean);
	//~ $priority = $clean['priority'];
	//~ if ($priority == NULL) {
		//~ $priority = 'None';
	//~ }
	//~ $template['priority'] = $priority;
	//~ $template['state'] = $state['state'];
	//~ $template['raw_state'] = $state['raw_state'];

//~ } else {
	//~ $template['priority'] = 'None';
//~ }

