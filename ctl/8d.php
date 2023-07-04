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
$this->tpl->set('causes',
        $this->model->thread_commentFactory->
        get_from_thread($thread, array('type' => 'cause'))->fetchAll());
$this->tpl->set('risks',
    $this->model->thread_commentFactory->
    get_from_thread($thread, array('type' => 'risk'))->fetchAll());
$this->tpl->set('opportunities',
                $this->model->thread_commentFactory->
                get_from_thread($thread, array('type' => 'opportunity'))->fetchAll());
$tasks = $this->model->taskFactory->get_by_type($thread);
$this->tpl->set('8d_tasks', $tasks);

$all_preventive_done = true;
$max_preventive_close_date = null;
foreach ($tasks['preventive'] as $preventive_action) {
    if ($preventive_action->state != 'done') {
        $all_preventive_done = false;
        break;
    }
    $max_preventive_close_date = max($max_preventive_close_date, $preventive_action->close_date);
}

if ($all_preventive_done && $max_preventive_close_date != null) {
    $this->tpl->set('preventive_close_date', date('Y-m-d', strtotime($max_preventive_close_date)));
}