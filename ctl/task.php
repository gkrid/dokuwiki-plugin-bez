<?php

if (!isset($nparams['tid'])) {
    header('Location: ?id=bez:tasks');
}
$template['tid'] = $nparams['tid'];

try {
    $task = $this->model->tasks->get_one($template['tid']);  
    $template['task'] = $task;
    

    if ($action === 'task_change_state') {
        $task = $this->model->tasks->get_one($template['tid']);

        $task->set_state(array(
                    'state' => $nparams['state'],
                    'reason' => $_POST['reason'])
                );
        $this->model->tasks->save($task);

        $issue->update_last_activity();
        $this->model->issues->save($issue);

        $anchor = 'z'.$task->id;
        $redirect = true;
    }

    if (!empty($task->cause)) {
        $template['commcause'] = $this->model->commcauses->get_one($task->cause);
    }
    
    if (!empty($task->issue)) {
        $template['issue'] = $this->model->issues->get_one($task->issue);
    }
    
} catch (Exception $e) {
    header('Location: ?id=bez:tasks');
}

