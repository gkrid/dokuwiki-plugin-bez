<?php
//~ include_once DOKU_PLUGIN."bez/models/bezcache.php";

$template['tid'] = isset($nparams['tid']) ? $nparams['tid'] : '-1';
$template['action'] = isset($nparams['action']) ? $nparams['action'] : '-default';

if (isset($_POST['tasktype'])) {
    $tasktype = $_POST['tasktype'];
} else {
    $tasktype = $nparams['tasktype'];
}
$template['tasktype'] = $tasktype;

try {

    $task = $this->model->tasks->create_object_program(
                            array('tasktype' => $tasktype));

    //$template['auth_level'] = $task->get_level();
    //$template['user'] = $task->get_user();
    $value['tasktype'] = $tasktype;

    if (count($_POST) > 0) {
        //checkboxes 
        if (!isset($_POST['all_day_event'])) {
            $_POST['all_day_event'] = '0';
        }

        $task->set_data($_POST);
        //update tasktype for admins

        $tid = $this->model->tasks->save($task);
//            $title = 'Dodano zadanie';
//            $exec = $task->executor;
//            $subject = "[$conf[title]] $title: #z$tid";
//            $to = $this->model->users->get_user_full_name($exec).' <'.$this->model->users->get_user_email($exec).'>';
//            $body = "$uri?id=".$this->id('show_task', 'tid', $tid);
//            $this->helper->mail($to, $subject, $body);
        
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
        } else {
            $value['all_day_event'] = '1';
        }
    }
//        $template['task_button'] = $bezlang['add'];
//        $template['task_action'] = $this->id('task_report', 'tasktype', $nparams['tasktype']);
    $template['users'] = $this->model->users->get_all();
    $template['tasktypes'] = $this->model->tasktypes->get_all();    

} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
} catch (DBException $e) {
    echo nl2br($e);
//	header("Location: ?id=bez:tasks");
}



//$template['user'] = $task->get_user();
//$template['user_name'] = $this->model->users->get_user_full_name($template['user']);
//
//$template['users'] = $this->model->users->get_all();
//$template['tasktypes'] = $this->model->tasktypes->get_all();
//$template['tasktype_name'] = $this->model->tasktypes->get_one($nparams['tasktype'])->type;
