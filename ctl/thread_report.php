<?php

use \dokuwiki\plugin\bez;

if ($this->model->acl->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}


$thread_id = $this->get_param('id');
if ($thread_id != '') {
	/** @var \dokuwiki\plugin\bez\mdl\Thread $thread */
	$thread = $this->model->threadFactory->get_one($thread_id);
	$this->tpl->set('thread', $thread);
}

if ($this->get_param('action') == 'edit') {
    if (!isset($thread)) {
        throw new Exception('there is now row with given id');
    }
    $this->tpl->set_values($thread->get_assoc());
} elseif ($this->get_param('action') == 'update') {

    $prev_coordiantor = $thread->coordinator;
    $this->model->threadFactory->update_save($thread, $_POST);

    header('Location: ?id='.$this->id('thread', 'id', $thread->id));
} elseif ($this->get_param('action') == 'add') {
    //$template['form_action'] = 'add';

    $defaults = array();
    if ($this->model->acl->get_level() >= BEZ_AUTH_LEADER) {
        $defaults['coordinator'] = $_POST['coordinator'];
    }
    unset($_POST['coordinator']);
    $thread = $this->model->threadFactory->create_object($defaults);

    $this->model->threadFactory->initial_save($thread, $_POST);

    header('Location: ?id='.$this->id('thread', 'id', $thread->id));

}

$this->tpl->set('labels', $this->model->labelFactory->get_all());