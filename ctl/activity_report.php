<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}

$this->tpl->set('thread_involvement', $this->model->threadFactory->users_involvement());
$this->tpl->set('task_involvement', $this->model->taskFactory->users_involvement());
