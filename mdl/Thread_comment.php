<?php

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\PermissionDeniedException;
use dokuwiki\plugin\bez\meta\ValidationException;

class Thread_comment extends Entity {

	//real
	protected $id, $thread_id, $type, $author, $create_date, $last_modification_date, $content, $content_html, $task_count;
	
	//virtual
	protected $coordinator;

	/** @var Thread */
	protected  $thread;
	
    //protected $parse_int = array('tasks_count');
	public static function get_columns() {
		return array('id', 'thread_id', 'type', 'author',
                     'create_date', 'last_modification_date', 'content', 'content_html', 'task_count');
	}

    public function __get($property) {
        if ($property == 'thread') {
            if ($this->thread_id == null) {
                return null;
            }
            if ($this->thread == null) {
                $this->thread = $this->model->threadFactory->get_one($this->thread_id);
            }
            return $this->thread;

        } elseif ($property == 'coordinator') {
	        return $this->$property;
        }
        return parent::__get($property);
    }
    
    //defaults: isssue, type
	public function __construct($model, $defaults=array()) {
		parent::__construct($model, $defaults);

        $this->validator->set_rules(array(
            'content' => array(array('length', 10000), 'NOT NULL'),
            'type' => array(
                array('select', array('comment', 'cause_real', 'cause_potential')),
                'NOT NULL')
        ));
		
		//new object
		if ($this->id === NULL) {

            $this->author = $this->model->user_nick;
            $this->create_date = date('c');
            $this->last_modification_date = $this->create_date;


            if (!isset($defaults['thread'])) {
                throw new \Exception('$defaults[thread] not set');
            }
            $this->thread = $defaults['thread'];
			$this->thread_id = $this->thread->id;
            $this->coordinator = $this->thread->coordinator;

            $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
            if ($this->coordinator == $this->model->user_nick) {
                $this->acl->grant('type', BEZ_PERMISSION_CHANGE);
            }

		} else {
            if (isset($defaults['thread']) && $this->thread_id == $defaults['thread']->id) {
                $this->thread = $defaults['thread'];
            }

            //we can change our own comments only when they are "comment"
            if ($this->author == $this->model->user_nick && $this->type == 'comment') {
                //we can only delete records when there is no tasks subscribed to issue
                if ($this->task_count == '0') {
                    $this->acl->grant('id', BEZ_PERMISSION_DELETE);
                }
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
            }

            if ($this->coordinator == $this->model->user_nick) {
                //we can only delete records when there is no tasks subscribed to issue
                if ($this->task_count == '0') {
                    $this->acl->grant('id', BEZ_PERMISSION_DELETE);
                }
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('type', BEZ_PERMISSION_CHANGE);
            }
        }
	}

    public function set_data($post) {
	    //no all can change type
        if ($this->acl_of('type') < BEZ_PERMISSION_CHANGE) {
            unset($post['type']);
        }
        parent::set_data($post);
        $this->purge();
    }

    protected function html_link_url() {
        $tpl = $this->model->action->get_tpl();
        return $tpl->url('thread', 'id', $this->thread_id) . '#k' . $this->id;
    }

    protected function html_link_content() {
        return '#k' . $this->id;
    }

    public function mail_notify_add() {
        $tpl = $this->model->action->get_tpl();

        $info = array();
        $html =  p_render('bez_xhtmlmail', p_get_instructions($this->content), $info);
        $tpl->set('content', $html);
        $tpl->set('who', $this->author);
        $tpl->set('when', $this->create_date);
        if ($this->type == 'comment') {
            $action = 'mail_comment_added';
        } else {
            $action = 'mail_cause_added';
            $tpl->set('action_border_color', '#ddb68d');
            $tpl->set('action_background_color', '#ffeedc');
        }
        $tpl->set('action', $action);
        $content = $this->model->action->bez_tpl_include('mail/thread_comment', true);

        $this->thread->mail_notify($content, false, $info['img']);
    }
}
