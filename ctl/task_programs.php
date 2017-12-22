<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->acl->get_level() < BEZ_AUTH_ADMIN) {
    throw new bez\meta\PermissionDeniedException();
}

$task_programs = $this->model->task_programFactory->get_all();

if ($this->get_param('id') === '') {
    $task_program = $this->model->task_programFactory->create_object();
} else {
    $task_program = $this->model->task_programFactory->get_one($this->get_param('id'));
}

$this->tpl->set('task_programs', $task_programs);
$this->tpl->set('task_program', $task_program);


if ($this->get_param('action') === 'edit') {

    $this->tpl->set_values($task_program->get_assoc());

} else if ($this->get_param('action') === 'remove') {

    $this->model->task_programFactory->delete($task_program);

    header('Location: '.$this->url('task_programs'));

} elseif (count($_POST) > 0) {
    $task_program->set_data($_POST);
    $this->model->task_programFactory->save($task_program);

    header('Location: '.$this->url('task_programs'));
}
