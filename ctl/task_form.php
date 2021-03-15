<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}

/** @var bez\mdl\Task $task */
$task = $this->model->taskFactory->create_object();

$this->tpl->set('task', $task);
$this->tpl->set('task_programs',  $this->model->task_programFactory->get_all([], 'name'));

if ($this->get_param('action') == 'add') {
    $this->model->taskFactory->initial_save($task, $_POST);
    $redirect = true;
} elseif($this->get_param('duplicate') != '') {
    $task_dup = $this->model->taskFactory->get_one($this->get_param('duplicate'));
    $this->tpl->set_values($task_dup->get_assoc());
}

if (isset($redirect) && $redirect == true) {
    header("Location: " . $this->url('task', 'tid', $task->id));
}
