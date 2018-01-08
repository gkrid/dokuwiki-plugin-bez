<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

//if we don't have a token, generate a new one and redirect
if (!isset($_GET['t']) && $this->model->authentication_tokenFactory->can_create_token()) {
    $token = $this->model->authentication_tokenFactory->create_token($this->id());
    header('Location: ' . $this->url() . '&t=' . $token);
}

if ($this->model->get_level() < BEZ_AUTH_VIEWER) {
    throw new bez\meta\PermissionDeniedException();
}

/** @var bez\mdl\Thread $thread */
$thread = $this->model->threadFactory->get_one($this->get_param('id'));
$this->tpl->set('thread', $thread);

$tasks = $this->model->taskFactory->get_all(array('thread_id' => $thread->id))->fetchAll();
$this->tpl->set('tasks', $tasks);