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
