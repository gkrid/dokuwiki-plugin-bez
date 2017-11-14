<?php

$template['tid'] = isset($nparams['tid']) ? $nparams['tid'] : '-1';
$template['action'] = isset($nparams['action']) ? $nparams['action'] : '-default';

if (isset($_POST['tasktype'])) {
    $tasktype = $_POST['tasktype'];
} else {
    $tasktype = $nparams['tasktype'];
}
$template['tasktype'] = $tasktype;

try {

    $task = $this->model->tasks->create_object(
                            array('tasktype' => $tasktype));
    $template['task'] = $task;

    //$template['auth_level'] = $task->get_level();
    //$template['user'] = $task->get_user();
    $value['tasktype'] = $tasktype;
    $value['executor'] = $this->model->user_nick;

    if (count($_POST) > 0) {
        //checkboxes 
        if (!isset($_POST['all_day_event'])) {
            $_POST['all_day_event'] = '0';
        }
        
        $task->set_data($_POST);
        
        if ($task->issue == '') {
            $task->add_subscribent($task->reporter);
            $task->add_subscribent($task->executor);
        }
        
        $tid = $this->model->tasks->save($task);

        
        //don't send notification when user binds himself to the task.
        if ($task->reporter !== $task->executor) {
            $task->mail_notify_add(NULL, array($task->executor),
                       array('action' => $bezlang['mail_task_added_programme']));
        }
        header("Location: ?id=bez:task:tid:$tid");
    } else {
        if (isset($nparams['duplicate'])) {
            $tid = (int)$nparams['duplicate'];
            $task = $this->model->tasks->get_one($tid);
            $value = $task->get_assoc();
            //if user is not leader, he clones a task with executor=$USER['name']
            if ($this->model->acl->get_level() < BEZ_AUTH_LEADER) {
                $value['executor'] = $this->model->user_nick;
            }
        } else {
            $value['all_day_event'] = '1';
        }
    }

    $template['users'] = $this->model->users->get_all();
    $template['tasktypes'] = $this->model->tasktypes->get_all();    

} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
} catch (DBException $e) {
    echo nl2br($e);
//	header("Location: ?id=bez:tasks");
}

