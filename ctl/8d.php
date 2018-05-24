<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

//if we don't have a token, generate a new one and redirect
if (!isset($_GET['t']) && $this->model->authentication_tokenFactory->can_create_token()) {
    $token = $this->model->authentication_tokenFactory->create_token($this->id());
    header('Location: ' .
           wl($this->id('8d', 'id', $this->get_param('id')), array('t' => $token), false, '&'));
}

if ($this->model->get_level() < BEZ_AUTH_VIEWER) {
    throw new bez\meta\PermissionDeniedException();
}

/** @var bez\mdl\Thread $thread */
$thread = $this->model->threadFactory->get_one($this->get_param('id'));
$this->tpl->set('thread', $thread);
$this->tpl->set('causes_real',
        $this->model->thread_commentFactory->get_from_thread($thread, array('type' => 'cause_real'))->fetchAll());
$this->tpl->set('causes_potential',
    $this->model->thread_commentFactory->get_from_thread($thread, array('type' => 'cause_potential'))->fetchAll());
$tasks = $this->model->taskFactory->get_by_type($thread);
$this->tpl->set('8d_tasks', $tasks);
