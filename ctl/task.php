<?php
/** @var action_plugin_bez_default $this */

use \dokuwiki\plugin\bez;

if ($this->get_param('tid') == '') {
    header('Location: ' . $this->url('tasks'));
}

/** @var bez\mdl\Task $task */
$task = $this->model->taskFactory->get_one($this->get_param('tid'));
$this->tpl->set('task', $task);
$this->tpl->set('task_comments', $this->model->task_commentFactory->get_from_task($task));

if ($this->get_param('action') == 'comment_add') {

    /** @var bez\mdl\Thread_comment $thread_comment */
    $task_comment = $this->model->task_commentFactory->create_object(array('task' => $task));
    $this->model->task_commentFactory->initial_save($task_comment, $_POST);

    $anchor = 'k'.$task_comment->id;
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

//$template['tid'] = $nparams['tid'];
//$template['action'] = isset($nparams['action']) ? $nparams['action'] : '-default';
//$template['state'] = isset($nparams['state']) ? $nparams['state'] : '-1';
//try {
//    $task = $this->model->tasks->get_one($template['tid']);
//    $template['task'] = $task;
//
//    if ($task->issue == '') {
//        //remove userts that are subscribents already
//        $template['users_to_invite'] = array_diff_key($this->model->users->get_all(), $task->get_subscribents());
//    }
//
//
//    if ($task->cause !== NULL && $task->cause !== '') {
//        $template['commcause'] = $this->model->commcauses->get_one($task->cause);
//    }
//
//    if ($task->issue !== NULL && $task->issue !== '') {
//        $template['issue'] = $this->model->issues->get_one($task->issue);
//    }
//
//
//    if ($template['action'] === 'task_change_state') {
//        if (count($_POST) > 0) {
//            if (isset($_POST['no_evaluation'])) {
//                $_POST['reason'] = '';
//            }
//
//            $task->set_state(array(
//                        'state' => $nparams['state'],
//                        'reason' => $_POST['reason'])
//                    );
//            $this->model->tasks->save($task);
//
//            if (isset($template['issue'])) {
//                $template['issue']->update_last_activity();
//                $this->model->issues->save($template['issue']);
//            }
//
//            $task->mail_notify_subscribents($template['issue'],
//                        array('action' => $bezlang['mail_task_change_state']));
//
//            $redirect = true;
//        } else {
//            $value = $task->get_assoc();
//        }
//    } elseif ($template['action'] === 'task_reopen') {
//        $task->set_state(array('state' => '0'));
//        $this->model->tasks->save($task);
//
//        if (isset($template['issue'])) {
//            $template['issue']->update_last_activity();
//            $this->model->issues->save($template['issue']);
//        }
//
//        $task->mail_notify_subscribents($template['issue'],
//                        array('action' => $bezlang['mail_task_reopened']));
//
//
//        $redirect = true;
//    } elseif($template['action'] === 'task_edit') {
//
//        $template['users'] = $this->model->users->get_all();
//        $template['tasktypes'] = $this->model->tasktypes->get_all();
//
//        if (isset($template['issue'])) {
//            $template['causes'] = $this->model->commcauses->get_all(array(
//                'issue' => $template['issue']->id,
//                'type' => array('!=', '0'),
//            ));
//        }
//
//        if (count($_POST) > 0) {
//            //checkboxes
//            if (!isset($_POST['all_day_event'])) {
//                $_POST['all_day_event'] = '0';
//            }
//            $task->set_data($_POST);
//            //for reason
//            //$task->set_state($_POST);
//
//            $this->model->tasks->save($task);
//
//            $redirect = true;
//        } else {
//            $value = $task->get_assoc();
//        }
//
//    } elseif ($template['action'] === 'subscribe') {
//			$task->add_subscribent($INFO['client']);
//			$this->model->tasks->save($task);
//
//            header("Location: ?id=bez:task:tid:".$task->id);
//
//    } elseif ($template['action'] === 'unsubscribe') {
//			$task->remove_subscribent($INFO['client']);
//			$this->model->tasks->save($task);
//
//            $this->add_notification($bezlang['unsubscribed_task_com']);
//
//            $redirect = true;
//
//    } elseif ($template['action'] === 'invite') {
//            $client = $_POST['client'];
//
//			$state = $task->add_subscribent($client);
//            //user wasn't subscribent
//            if ($state === true) {
//                $this->model->tasks->save($task);
//                $task->mail_notify_invite($client);
//
//                $this->add_notification($this->model->users->get_user_email($client), $bezlang['invitation_has_been_send']);
//
//                $redirect = true;
//            }
//
//    } elseif($template['action'] === 'task_edit_metadata') {
//        $template['users'] = $this->model->users->get_all();
//
//        if (count($_POST) > 0) {
//            $task->set_meta($_POST);
//            $this->model->tasks->save($task);
//
//            $redirect = true;
//        } else {
//            $value = $task->get_assoc();
//            $value['date'] = date('Y-m-d', (int)$value['date']);
//            $value['close_date'] = date('Y-m-d', (int)$value['close_date']);
//        }
//    }
//
//    if ($redirect) {
//        header("Location: ?id=bez:task:tid:".$template['tid']);
//    }
//} catch (ValidationException $e) {
//	$errors = $e->get_errors();
//	$value = $_POST;
//} catch (DBException $e) {
//    echo nl2br($e);
//}

