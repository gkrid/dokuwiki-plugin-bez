<?php

/** @var action_plugin_bez_default $this */

use \dokuwiki\plugin\bez;

$task_id = $this->get_param('tid');
if ($task_id != '') {
    /** @var bez\mdl\Task $task */
    $task = $this->model->taskFactory->get_one($this->get_param('tid'));
} else {
    /** @var bez\mdl\Task $task */
    $task = $this->model->taskFactory->create_object();
}
$this->tpl->set('task', $task);
$this->tpl->set('task_programs',  $this->model->task_programFactory->get_all());

if ($this->get_param('action') == 'add') {

    $this->model->taskFactory->initial_save($task, $_POST);

    $redirect = true;
}

if (isset($redirect) && $redirect == true) {
    header("Location: " . $this->url('task', 'tid', $task->id));
}

//
//$template['tid'] = isset($nparams['tid']) ? $nparams['tid'] : '-1';
//$template['action'] = isset($nparams['action']) ? $nparams['action'] : '-default';
//
//if (isset($_POST['tasktype'])) {
//    $tasktype = $_POST['tasktype'];
//} else {
//    $tasktype = $nparams['tasktype'];
//}
//$template['tasktype'] = $tasktype;
//
//try {
//
//    $task = $this->model->tasks->create_object(
//                            array('tasktype' => $tasktype));
//    $template['task'] = $task;
//
//    //$template['auth_level'] = $task->get_level();
//    //$template['user'] = $task->get_user();
//    $value['tasktype'] = $tasktype;
//    $value['executor'] = $this->model->user_nick;
//
//    if (count($_POST) > 0) {
//        //checkboxes
//        if (!isset($_POST['all_day_event'])) {
//            $_POST['all_day_event'] = '0';
//        }
//
//        $task->set_data($_POST);
//
//        if ($task->issue == '') {
//            $task->add_subscribent($task->reporter);
//            $task->add_subscribent($task->executor);
//        }
//
//        $tid = $this->model->tasks->save($task);
//
//
//        //don't send notification when user binds himself to the task.
//        if ($task->reporter !== $task->executor) {
//            $task->mail_notify_add(NULL, array($task->executor),
//                       array('action' => $bezlang['mail_task_added_programme']));
//        }
//        header("Location: ?id=bez:task:tid:$tid");
//    } else {
//        if (isset($nparams['duplicate'])) {
//            $tid = (int)$nparams['duplicate'];
//            $task = $this->model->tasks->get_one($tid);
//            $value = $task->get_assoc();
//        } else {
//            $value['all_day_event'] = '1';
//        }
//    }
//
//    $template['users'] = $this->model->users->get_all();
//    $template['tasktypes'] = $this->model->tasktypes->get_all();
//
//} catch (ValidationException $e) {
//	$errors = $e->get_errors();
//	$value = $_POST;
//} catch (DBException $e) {
//    echo nl2br($e);
////	header("Location: ?id=bez:tasks");
//}

