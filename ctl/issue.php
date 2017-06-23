<?php
//~ include_once DOKU_PLUGIN."bez/models/issues.php";
//~ include_once DOKU_PLUGIN."bez/models/comments.php";
//~ include_once DOKU_PLUGIN."bez/models/tasks.php";


//~ $isso = new Issues();
//~ $como = new Comments();
if (!isset($nparams['id'])) {
    header('Location: ?id=bez:issues');
}

try {
    $issue_id = (int)$nparams['id'];
    
    //new way
    $issue = $this->model->issues->get_one($issue_id);
    $template['issue'] = $issue;
    $template['commcauses'] = $this->model->commcauses->get_all(
        array('issue' => $issue_id)
    );
    
    $template['commcause'] = $this->model->commcauses->
                            create_dummy_object(array('issue' => $issue->id));
    
    $template['corrections'] = $this->model->tasks->get_all(array(
        'issue' => $issue_id,
        'action' => 0,
    ));

    $template['commcauses_tasks'] = array();
    foreach ($this->model->commcauses->get_causes_ids($issue_id) as $kid) {
        $template['commcauses_tasks'][$kid] = $this->model->tasks->get_all(array(
            'cause' => $kid,
        ));
    }


    $template['users'] = $this->model->users->get_all();

    //remove userts that are subscribents already
    $template['users_to_invite'] = array_diff_key($template['users'], $issue->get_subscribents());
    
    //$issue = $this->model->issues->get_one($issue_id);
    //~ $template['commcause_action'] = 'commcause_add';
    //placeholder for adding new records
    //~ $template['commcause_id'] = '-1';

    //~ $template['task_action'] = 'task_correction_add';
    $template['tid'] = isset($nparams['tid']) ? $nparams['tid'] : '-1';
    $template['kid'] = isset($nparams['kid']) ? $nparams['kid'] : '-1';
    $template['state'] = isset($nparams['state']) ? $nparams['state'] : '-1';
    $template['action'] = isset($nparams['action']) ? $nparams['action'] : '-default';
    //$template['auth_level'] = $issue->get_level();


	$action = '';
	if (isset($nparams['action'])) {
		$action = $nparams['action'];
		$redirect = false;
		$anchor = '';
		
		if ($action === 'commcause_add') {
            
            $defaults = array('issue' => (string)$issue_id);
            if ($issue->user_is_coordinator()) {
                $defaults['type'] = $_POST['type'];
            }
             
			$commcause = $this->model->commcauses->create_object($defaults);
            
            $data = array('content' => $_POST['content']);
			$commcause->set_data($data);
            
			$id = $this->model->commcauses->save($commcause);
			
			$issue->add_participant($INFO['client']);
			$issue->add_subscribent($INFO['client']);
			
			$issue->update_last_activity();
			$this->model->issues->save($issue);
            
            $commcause->mail_notify_add($issue);
            
			$anchor = 'k'.$id;
			$redirect = true;
		} elseif ($action === 'subscribe') {
			$issue->add_subscribent($INFO['client']);
			$this->model->issues->save($issue);
			
			$redirect = true;
		} elseif ($action === 'unsubscribe') {
			$issue->remove_subscribent($INFO['client']);
			$this->model->issues->save($issue);
            
        } elseif ($action === 'invite') {
            $client = $this->model->users->get_user_nick($_POST['client']);
			$state = $issue->add_subscribent($client);
            //user wasn't subscribent
            if ($state === true) {
                $this->model->issues->save($issue);
                $issue->mail_notify_invite($client);
                $template['invited_email'] = $this->model->users->get_user_email($client);
            } 
			
		} elseif ($action === 'commcause_delete') {
			$commcause = $this->model->commcauses->get_one($template['kid']);
			
			$this->model->commcauses->delete($commcause);
			
			$issue->update_last_activity();
			$this->model->issues->save($issue);
			
			$redirect = true;
		} elseif ($action === 'commcause_edit') {
			if (count($_POST) === 0) {
				$commcause = $this->model->commcauses->get_one($template['kid']);
				$template['kid'] = $commcause->id;
				$value = $commcause->get_assoc();
			} else {
				$commcause = $this->model->commcauses->get_one($template['kid']);
                
                $data = array('content' => $_POST['content']);
                if ($issue->user_is_coordinator()) {
                    $data['type'] = $_POST['type'];
                }
                
				$commcause->set_data($data);
				$this->model->commcauses->save($commcause);
				
				$issue->update_last_activity();
				$this->model->issues->save($issue);
				
				$anchor = 'k'.$commcause->id;
				$redirect = true;
			}			
		} elseif ($action === 'issue_close') {
			$value['opinion'] = $template['issue']['raw_opinion'];
		} elseif ($action == 'issue_close_confirm') {
			$issue->set_state($_POST);
			$this->model->issues->save($issue);
            
            $issue->mail_notify_change_state();
            
			$redirect = true;
		} elseif ($action === 'reopen') {
			$issue->set_state(array('state' => '0'));
			$this->model->issues->save($issue);
            
            $issue->mail_notify_change_state();
            
            $redirect = true;
 		} elseif (strpos($action, 'task') === 0) {
            $template['task'] = $this->model->tasks->
                    create_dummy_object(array('issue' => $issue->id));
			$template['users'] = $this->model->users->get_all();
			$template['tasktypes'] = $this->model->tasktypes->get_all();
			
			if (count($_POST) > 0) {
				if (!isset($_POST['all_day_event'])) {
					$_POST['all_day_event'] = '0';
				}
			}
						
			if ($action === 'task_reopen') {
				$task = $this->model->tasks->get_one($nparams['tid']);
				$task->set_state(array('state' => '0'));
				$this->model->tasks->save($task);
				
				$issue->update_last_activity();
				$this->model->issues->save($issue);
                
                $task->mail_notify_add($issue, false,
                                array('action' => $bezlang['mail_task_reopened']));
					
				$redirect = true;
				$anchor = 'z'.$task->id;
				
			} elseif ($action === 'task_edit') {
				$template['tid'] = $nparams['tid'];
				
				$template['causes'] = $this->model->commcauses->get_all(array(
					'issue' => $issue_id,
					'type' => array('!=', '0'),
				));
				
				$task = $this->model->tasks->get_one($template['tid']);
				$value = $task->get_assoc();
                
            } elseif ($action === 'task_change_state') {
                $template['tid'] = $nparams['tid'];				
				$task = $this->model->tasks->get_one($template['tid']);
				$value = array('reason' => $task->reason);
			}
			
			if (count($_POST) > 0) {				
				//ends with
				if (substr($action, -strlen('add')) === 'add') {
					$defaults = array('issue' => (string)$issue_id);
					if ($template['kid'] !== '-1') {
						$defaults['cause'] = $template['kid'];
					}
					$task = $this->model->tasks->create_object($defaults);
					
					$task->set_data($_POST);
					$id = $this->model->tasks->save($task);
					
					$issue->add_participant($task->executor);
					$issue->add_subscribent($task->executor);
			
					$issue->update_last_activity();
					$this->model->issues->save($issue);
                    
                    $task->mail_notify_add($issue);
					
					$anchor = 'z'.$id;
					$redirect = true;
				} elseif ($action === 'task_change_state') {
					$task = $this->model->tasks->get_one($template['tid']);
                    
                    if (isset($_POST['no_evaluation'])) {
                        $_POST['reason'] = '';
                    }
					
					$task->set_state(array(
								'state' => $nparams['state'],
								'reason' => $_POST['reason'])
							);
					$this->model->tasks->save($task);
					
					$issue->update_last_activity();
					$this->model->issues->save($issue);
					
					$anchor = 'z'.$task->id;
					$redirect = true;
				} elseif ($action === 'task_edit') {
					$task = $this->model->tasks->get_one($template['tid']);
					$task->set_data($_POST);
					$this->model->tasks->save($task);
					
					$issue->add_participant($task->executor);
					$issue->add_subscribent($task->executor);
					
					//don't upgrade last activity!!!
					$anchor = 'z'.$task->id;
					$redirect = true;
				}
			}	
		}
		
		if ($redirect) {
			if ($anchor !== '') {
				$anchor = '#'.$anchor;
			}
			header("Location: ?id=bez:issue:id:$issue_id$anchor");
		}
	}
} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
} catch (DBException $e) {
	echo nl2br($e);
//	header("Location: ?id=bez:issue:id:$issue_id");
}

//~ if (!isset($template[comment_action])) {
	//~ $template['comment_button'] = $bezlang['add'];
	//~ $template['comment_action'] = 'comment_add';
//~ }


//~ $tasko = new Tasks();
//~ $template['anytasks'] = $tasko->any_task($issue_id);
//~ $template['opentasks'] = $tasko->any_open($issue_id);
//~ $template['cause_without_task'] = $isso->cause_without_task($issue_id);

//~ $template['issue'] = $isso->get($issue_id);
//~ $template['comments'] = $como->get($issue_id);

