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
//else {
	//$template['thread'] = $this->model->issues->create_dummy_object();
	//$template['priority'] = 'None';
//}

//$action = '';
//if (isset($nparams['action'])) {
//	$action = $nparams['action'];
//}

//try {
if ($this->get_param('action') == 'edit') {
    if (!isset($thread)) {
        throw new Exception('there is now row with given id');
    }
    //$template['form_action'] = 'update';
    //$value = $thread->get_assoc();
    $this->tpl->set_values($thread->get_assoc());
} elseif ($this->get_param('action') == 'update') {
    //$template['form_action'] = 'update';

    $prev_coordiantor = $thread->coordinator;
    $this->model->threadFactory->update_save($thread, $_POST);

//    if ($thread->state != 'proposal' && $this->model->user_nick != $thread->coordinator) {
//        $thread->mail_inform_coordinator();
//    }


//    $thread->add_participant($thread->coordinator);

    //save to get ID!!!
//    $this->model->threadFactory->save($thread);
//
//    if ($thread->coordinator !== '-proposal' &&
//        $INFO['client'] !== $thread->coordinator &&
//        $thread->coordinator != $prev_coordiantor) {
//        //coordinator becomes subscribent automaticly
//        $thread->add_subscribent($thread->coordinator);
//        $this->model->threadFactory->save($thread);
//
//        $thread->mail_inform_coordinator();
//    }

    header('Location: ?id='.$this->id('thread', 'id', $thread->id));
} elseif ($this->get_param('action') == 'add') {
    //$template['form_action'] = 'add';

    $defaults = array();
    if ($this->model->acl->get_level() >= BEZ_AUTH_LEADER) {
        $defaults['coordinator'] = $_POST['coordinator'];
    }
    unset($_POST['coordinator']);
    $thread = $this->model->threadFactory->create_object($defaults);

//    $data = array(
////            'type' => $_POST['type'],
//        'title' => $_POST['title'],
//        'content' => $_POST['content']
//    );
    //$thread->set_data($data);
    $this->model->threadFactory->initial_save($thread, $_POST);
//    if ($thread->state != 'proposal' && $this->model->user_nick != $thread->coordinator) {
//        $thread->mail_inform_coordinator();
//    }

//        if ($thread->coordinator !== '-proposal' &&
//            $INFO['client'] !== $thread->coordinator) {
//            //coordinator becomes subscribent automaticly
//            $thread->add_subscribent($issue->coordinator);
//            $this->model->issues->save($thread);
//
//            $thread->mail_inform_coordinator();
//        }


    header('Location: ?id='.$this->id('thread', 'id', $thread->id));

}
//	else {
//		$template['form_action'] = 'add';
//	}

//} catch (bez\meta\ValidationException $e) {
//	$errors = $e->get_errors();
//	$value = $_POST;
//}

//$template['issuetypes'] = $this->model->issuetypes->get_all();
$this->tpl->set('labels', $this->model->labelFactory->get_all());