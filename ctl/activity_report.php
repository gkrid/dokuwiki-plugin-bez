<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}

$range = array();
if(count($_POST) > 0) {
    $this->tpl->set_values($_POST);
    if ($_POST['from'] != '' && $_POST['to'] != '') {
        $range = array($_POST['from'], $_POST['to']);
    }
}

$this->tpl->set('thread_involvement', $this->model->threadFactory->users_involvement($range));
$this->tpl->set('task_involvement', $this->model->taskFactory->users_involvement($range));
