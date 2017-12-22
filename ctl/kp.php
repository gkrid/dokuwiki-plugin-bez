<?php
//if we don't have a token, generate a new one and redirect
if (!isset($_GET['t'])) {
    $token = $this->model->authentication_tokenFactory->get_token($this->id());
    header('Location: ' . $this->url() . '&t=' . $token);
}

/** @var bez\mdl\Thread $thread */
$thread = $this->model->threadFactory->get_one($this->get_param('id'));
$this->tpl->set('thread', $thread);

$tasks = $this->model->taskFactory->get_all(array('thread_id' => $thread->id))->fetchAll();
$this->tpl->set('tasks', $tasks);