<?php

use \dokuwiki\plugin\bez;

if ($this->model->acl->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}


if (isset($nparams['id']) && is_numeric($nparams['id'])) {
	$thread_id = (int)$nparams['id'];
	/** @var \dokuwiki\plugin\bez\mdl\Thread $thread */
	$thread = $this->model->threadFactory->get_one($thread_id);
	
	$tpl->set('thread', $thread);
}
//else {
	//$template['thread'] = $this->model->issues->create_dummy_object();
	//$template['priority'] = 'None';
//}

//$action = '';
//if (isset($nparams['action'])) {
//	$action = $nparams['action'];
//}

try {
	if ($this->param('action') == 'edit') {
		if (!isset($thread)) {
			throw new Exception('there is now row with given id');
		}
		//$template['form_action'] = 'update';
		//$value = $thread->get_assoc();
        $this->tpl->set_values($thread->get_assoc());
	} elseif ($this->param('action') == 'update') {
		//$template['form_action'] = 'update';
        
        $prev_coordiantor = $thread->coordinator;
        
		$thread->set_data($_POST);

        $thread->add_participant($thread->coordinator);
        
        //save to get ID!!!
        $this->model->threadFactory->save($thread);

        if ($thread->coordinator !== '-proposal' &&
            $INFO['client'] !== $thread->coordinator &&
            $thread->coordinator != $prev_coordiantor) {
            //coordinator becomes subscribent automaticly
            $thread->add_subscribent($thread->coordinator);
            $this->model->threadFactory->save($thread);

            $thread->mail_inform_coordinator();
        }
        
		header('Location: ?id='.$this->id('thread', 'id', $thread->id));
	} elseif ($this->param('action') == 'add') {
		//$template['form_action'] = 'add';
		
        $defaults = array();
        if ($this->model->acl->get_level() >= BEZ_AUTH_LEADER) {
            $defaults['coordinator'] = $_POST['coordinator'];
        }
        $thread = $this->model->threadFactory->create_object($defaults);
		
        $data = array(
//            'type' => $_POST['type'],
            'title' => $_POST['title'],
            'content' => $_POST['content']
        );
        $thread->set_data($data);

        try {
            $this->model->threadFactory->beginTransaction();

            $this->model->threadFactory->save($thread);

            $thread->add_label($_POST['label']);

            $thread->set_participant_flags($thread->original_poster, array('original_poster', 'subscribent'));
            if($thread->coordinator != null) {
                $thread->set_participant_flags($thread->coordinator, array('coordinator', 'subscribent'));
            }
            $this->model->threadFactory->commitTransaction();
        } catch(Exception $exception) {
            $this->model->threadFactory->rollbackTransaction();
        }

        
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

} catch (bez\meta\ValidationException $e) {
	$errors = $e->get_errors();
	$value = $_POST;
}

//$template['issuetypes'] = $this->model->issuetypes->get_all();
$this->tpl->set('users', $this->model->userFactory->get_all());
$this->tpl->set('labels', $this->model->labelFactory->get_all());