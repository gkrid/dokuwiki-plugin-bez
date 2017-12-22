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
		} else {
            if (isset($defaults['thread']) && $this->thread_id == $defaults['thread']->id) {
                $this->thread = $defaults['thread'];
            }
        }
	}

    public function set_data($post) {
	    //no all can change type
        if ($this->acl_of('type') < BEZ_PERMISSION_CHANGE) {
            unset($post['type']);
        }
        parent::set_data($post);
        $this->content_html = p_render('xhtml',p_get_instructions($this->content), $ignore);
    }

    public function mail_notify_add() {

        $rep = array(
            'content' => $this->content,
            'content_html' => $this->content_html,
            'who' => $this->author,
            'when' => $this->create_date
        );
        
        if ($this->type > 0) {
            $rep['action'] = $this->model->action->getLang('mail_cause_added');
            $rep['action_color'] = '#ffeedc';
            $rep['action_border_color'] = '#ddb68d';
        } else {
            $rep['action'] = $this->model->action->getLang('mail_comment_added');
            $rep['action_color'] = 'transparent';
            $rep['action_border_color'] = '#E5E5E5';
        }
        
        $this->thread->mail_notify($rep);
    }
}
