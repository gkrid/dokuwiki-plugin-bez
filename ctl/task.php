<?php

if (!isset($nparams['tid'])) {
    header('Location: ?id=bez:tasks');
}
$template['tid'] = $nparams['tid'];
$template['action'] = isset($nparams['action']) ? $nparams['action'] : '-default';
$template['state'] = isset($nparams['state']) ? $nparams['state'] : '-1';
try {
    $task = $this->model->tasks->get_one($template['tid']);  
    $template['task'] = $task;
    
 
    if ($task->cause !== NULL && $task->cause !== '') {
        $template['commcause'] = $this->model->commcauses->get_one($task->cause);
    }
    
    if ($task->issue !== NULL && $task->issue !== '') {
        $template['issue'] = $this->model->issues->get_one($task->issue);
    }


    if ($template['action'] === 'task_change_state') {
        if (count($_POST) > 0) {
            if (isset($_POST['no_evaluation'])) {
                $_POST['reason'] = '';
            }
            
            $task->set_state(array(
                        'state' => $nparams['state'],
                        'reason' => $_POST['reason'])
                    );
            $this->model->tasks->save($task);
            
            if (isset($template['issue'])) {
                $template['issue']->update_last_activity(); 
                $this->model->issues->save($template['issue']);
            }

            $redirect = true;
        } else {
            $value = $task->get_assoc();
        }
    } elseif ($template['action'] === 'task_reopen') {
        $task->set_state(array('state' => '0'));
        $this->model->tasks->save($task);

        if (isset($template['issue'])) {
            $template['issue']->update_last_activity(); 
            $this->model->issues->save($template['issue']);
        }
        
        $notify_users = array();
        if ($task->reporter !== $this->model->user_nick) {
            //prevent duplicates
            $notify_users[$task->reporter] = $task->reporter;
        }
        if ($task->executor !== $this->model->user_nick) {
            //prevent duplicates
            $notify_users[$task->executor] = $task->executor;
        }
        
        $task->mail_notify_add($template['issue'], $notify_users,
                                array('action' => $bezlang['mail_task_reopened']));
        
        $redirect = true;
    } elseif($template['action'] === 'task_edit') {
        $template['auth_level'] = $task->get_level();
        
        $template['users'] = $this->model->users->get_all();
        $template['tasktypes'] = $this->model->tasktypes->get_all();
        
        if (isset($template['issue'])) {
            $template['causes'] = $this->model->commcauses->get_all(array(
                'issue' => $template['issue']->id,
                'type' => array('!=', '0'),
            ));
        }
            
        if (count($_POST) > 0) {
            //checkboxes 
            if (!isset($_POST['all_day_event'])) {
                $_POST['all_day_event'] = '0';
            }
            $task->set_data($_POST);
            //for reason
            $task->set_state($_POST);

            $this->model->tasks->save($task);
                //~ $bezcache = new Bezcache();	
                //~ $bezcache->task_toupdate($task->id);

            header("Location: ?id=bez:task:tid:".$task->id);
        } else {
            $value = $task->get_assoc();
        }
    }
    
    if ($redirect) {
        header("Location: ?id=bez:task:tid:".$template['tid']);
    }
    
} catch (DBException $e) {
    echo nl2br($e);
}

