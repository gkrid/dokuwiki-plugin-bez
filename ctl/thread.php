<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}

if ($this->get_param('id') == '') {
    header('Location: ' . $this->url('threads'));
}

/** @var bez\mdl\Thread $thread */
$thread = $this->model->threadFactory->get_one($this->get_param('id'));

if ($thread->acl_of('id') < BEZ_PERMISSION_VIEW) {
    throw new bez\meta\PermissionDeniedException();
}

$this->tpl->set('thread', $thread);
if ($thread->type == 'project') {
    $this->tpl->set('lang_suffix', '_project');
}

$thread_comments = iterator_to_array($this->model->thread_commentFactory->get_from_thread($thread));
$tasks = $this->model->taskFactory->get_from_thread($thread);

$timeline = array_merge($thread_comments, $tasks['corrections']);
usort($timeline, function($a, $b) {
    if ($a->create_date == $b->create_date) {
        return 0;
    }
    return ($a->create_date < $b->create_date) ? -1 : 1;
});

$this->tpl->set('timeline', $timeline);
$this->tpl->set('tasks', $tasks);
$this->tpl->set('task_programs',  $this->model->task_programFactory->get_all());

/** @var bez\mdl\Thread_comment $thread_comment */
$thread_comment = $this->model->thread_commentFactory->create_object(array('thread' => $thread));
$this->tpl->set('thread_comment', $thread_comment);

if ($this->get_param('action') == 'commcause_add') {

    $this->model->thread_commentFactory->initial_save($thread_comment, $_POST);

    $anchor = 'k'.$thread_comment->id;
    $redirect = true;

} elseif ($this->get_param('action') == 'subscribe') {

    $thread->set_participant_flags($this->model->user_nick, array('subscribent'));
    $redirect = true;

} elseif ($this->get_param('action') == 'unsubscribe') {

    $thread->remove_participant_flags($this->model->user_nick, array('subscribent'));
    $this->add_notification($this->getLang('unsubscribed_com'));
    $redirect = true;

} elseif ($this->get_param('action') == 'invite') {
    $client = $_POST['client'];

    $thread->invite($client);

    $this->add_notification($this->model->userFactory->get_user_email($client), $this->getLang('invitation_has_been_send'));

    $redirect = true;
} elseif ($this->get_param('action') == 'commcause_delete') {
    /** @var bez\mdl\Thread_comment $thread_comment */
    $thread_comment = $this->model->thread_commentFactory->get_one($this->get_param('kid'), array('thread' => $thread));
    $this->model->thread_commentFactory->delete($thread_comment);

    $redirect = true;
} elseif ($this->get_param('action') == 'commcause_edit') {
    /** @var bez\mdl\Thread_comment $thread_comment */
    $thread_comment = $this->model->thread_commentFactory->get_one($this->get_param('kid'), array('thread' => $thread));

    if(count($_POST) === 0) {
        $this->tpl->set_values($thread_comment->get_assoc());
    } else {
        $this->model->thread_commentFactory->update_save($thread_comment, $_POST);

        $anchor   = 'k' . $thread_comment->id;
        $redirect = true;
    }
} elseif ($this->get_param('action') == 'task_add') {

    $defaults = array('thread' => $thread);

    if ($this->get_param('kid') != '') {
        $thread_comment = $this->model->thread_commentFactory->get_one($this->get_param('kid'), array('thread' => $thread));
        $defaults['thread_comment'] = $thread_comment;
    }
    /** @var bez\mdl\Task $task */
    $task = $this->model->taskFactory->create_object($defaults);
    $this->tpl->set('task', $task);

    //save
    if (count($_POST) > 0) {
        $this->model->taskFactory->initial_save($task, $_POST);

        $anchor   = 'z' . $task->id;
        $redirect = true;
    }
} elseif ($this->get_param('action') == 'task_edit') {
    /** @var bez\mdl\Task $task */
    $task = $this->model->taskFactory->get_one($this->get_param('tid'), array('thread' => $thread));
    $this->tpl->set('task', $task);

    //save
    if (count($_POST) === 0) {
        $this->tpl->set_values($task->get_assoc());
    } else {
        $this->model->taskFactory->update_save($task, $_POST);

        $anchor   = 'z' . $task->id;
        $redirect = true;
    }
}

if (isset($redirect) && $redirect == true) {
    if (isset($anchor)) {
        $anchor = '#'.$anchor;
    } else {
        $anchor = '';
    }
    header('Location: ' . $this->url('thread', 'id', $thread->id) . $anchor);
}
