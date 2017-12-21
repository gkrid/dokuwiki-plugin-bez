<?php

//if we don't have a token, generate a new one and redirect
if (!isset($_GET['t'])) {
    $token = $this->model->authentication_tokenFactory->get_token($this->id());
    header('Location: ' . $this->url() . '&t=' . $token);
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
