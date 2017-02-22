<?php
//~ include_once DOKU_PLUGIN."bez/models/bezcache.php";

$template['tid'] = isset($nparams['tid']) ? $nparams['tid'] : '-1';
$template['action'] = isset($nparams['action']) ? $nparams['action'] : '-default';

try {
    if ($template['tid'] === '-1') {

        $task = $this->model->tasks->create_object_program(
                                array('tasktype' => $_POST['tasktype']));

        $template['auth_level'] = $task->get_level();
        $value['tasktype'] = $nparams['tasktype'];

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

                header("Location: ?id=bez:task:tid:$tid");
        } else {
            $value['all_day_event'] = '1';
            if (isset($nparams['duplicate'])) {
                $tid = (int)$nparams['duplicate'];
                $task = $this->model->tasks->get_one($tid);
                $value = $task->get_assoc();
            }
        }
//        $template['task_button'] = $bezlang['add'];
//        $template['task_action'] = $this->id('task_report', 'tasktype', $nparams['tasktype']);
    /*edycja*/
    } else {
        $task = $this->model->tasks->get_one($template['tid']);

        $template['task'] = $task;
        $template['auth_level'] = $task->get_level();
            
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
} catch (ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
} catch (Exception $e) {
	header("Location: ?id=bez:issue:id:$issue_id");
}

$template['action'] = $action;

$template['user'] = $task->get_user();
$template['user_name'] = $this->model->users->get_user_full_name($template['user']);

$template['users'] = $this->model->users->get_all();
$template['tasktypes'] = $this->model->tasktypes->get_all();
$template['tasktype_name'] = $this->model->tasktypes->get_one($nparams['tasktype'])->type;
