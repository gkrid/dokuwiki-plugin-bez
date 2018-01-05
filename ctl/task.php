<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}

if ($this->get_param('tid') == '') {
    header('Location: ' . $this->url('tasks'));
}

/** @var bez\mdl\Task $task */
$task = $this->model->taskFactory->get_one($this->get_param('tid'));
$this->tpl->set('task', $task);
$this->tpl->set('task_comments', $this->model->task_commentFactory->get_from_task($task));
$this->tpl->set('task_programs',  $this->model->task_programFactory->get_all());

if ($this->get_param('action') == 'comment_add') {

    /** @var bez\mdl\Thread_comment $thread_comment */
    $task_comment = $this->model->task_commentFactory->create_object(array('task' => $task));
    $this->model->task_commentFactory->initial_save($task_comment, $_POST);

    $anchor = 'zk'.$task_comment->id;
    $redirect = true;

} elseif ($this->get_param('action') == 'subscribe') {

    $task->set_participant_flags($this->model->user_nick, array('subscribent'));
    $redirect = true;

} elseif ($this->get_param('action') == 'unsubscribe') {

    $task->remove_participant_flags($this->model->user_nick, array('subscribent'));

    $this->add_notification($this->getLang('unsubscribed_com'));
    $redirect = true;

} elseif ($this->get_param('action') == 'invite') {
     $client = $_POST['client'];

    $task->invite($client);
    $this->add_notification($this->model->userFactory->get_user_email($client), $this->getLang('invitation_has_been_send'));

    $redirect = true;
} elseif ($this->get_param('action') == 'comment_delete') {
    /** @var bez\mdl\Task_comment $task_comment */
    $task_comment = $this->model->task_commentFactory->get_one($this->get_param('zkid'), array('task' => $task));
    $this->model->task_commentFactory->delete($task_comment);

    $redirect = true;
} elseif ($this->get_param('action') == 'comment_edit') {
    /** @var bez\mdl\Task_comment $task_comment */
    $task_comment = $this->model->task_commentFactory->get_one($this->get_param('zkid'), array('thread' => $thread));

    if(count($_POST) === 0) {
        $this->tpl->set_values($task_comment->get_assoc());
    } else {
        $this->model->task_commentFactory->update_save($task_comment, $_POST);

        $anchor   = 'zk' . $task_comment->id;
        $redirect = true;
    }
} elseif ($this->get_param('action') == 'task_edit') {
    //save
    if (count($_POST) === 0) {
        $this->tpl->set_values($task->get_assoc());
    } else {
        $this->model->taskFactory->update_save($task, $_POST);
        $redirect = true;
    }
}

if (isset($redirect) && $redirect == true) {
    if (isset($anchor)) {
        $anchor = '#'.$anchor;
    } else {
        $anchor = '';
    }
    header("Location: " . $this->url('task', 'tid', $task->id) . $anchor);
}