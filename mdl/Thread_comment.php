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
	    if ($property == 'coordinator' || $property == 'thread') {
	        return $this->$property;
        }
        return parent::__get($property);
    }
	
//	public function get_virtual_columns() {
//		return array('coordinator', 'tasks_count');
//	}
//
//	public function get_table_name() {
//		return 'commcauses';
//	}
    
    //defaults: isssue, type
	public function __construct($model, $defaults=array()) {
		parent::__construct($model, $defaults);

//		$this->validator->set_rules(array(
//			'issue' => array(array('numeric'), 'NOT NULL'),
//			'datetime'	=> array(array('sqlite_datetime'), 'NOT NULL'),
//			'reporter' => array(array('dw_user'), 'NOT NULL'),
//			'type' => array(array('select', array('0', '1', '2')), 'NOT NULL'),
//			'content' => array(array('length', 10000), 'NOT NULL'),
//			'content_cache' => array(array('length', 10000), 'NOT NULL'),
//
//			'coordinator' => array(array('dw_user', array('-proposal')), 'NOT NULL')
//		));



        $this->validator->set_rules(array(
            //'type' => array(array('select', array('0', '1', '2')), 'NOT NULL'),
            'content' => array(array('length', 10000), 'NOT NULL')
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
            
//            //we are coordinator of newly created object
//            if ($issue->user_is_coordinator()) {
//                //throws ValidationException
//                $this->type =
//                    $this->validator->validate_field('type', $defaults['type']);
//            } else {
//                $this->type = '0';
//            }
			
//			$this->reporter = $this->model->user_nick;
//			$this->datetime = $this->sqlite_date();
		} else {
            if ($this->thread_id != '') {
                if (isset($defaults['thread']) && $this->thread_id == $defaults['thread']->id) {
                    $this->thread = $defaults['thread'];
                } elseif ($this->thread_id != null) {
                    $this->thread = $this->model->threadFactory->get_one($this->thread_id);
                }
            }
        }


		//set validation
        if ($this->thread->user_is_coordinator()) {
            $this->validator->set_rules(
                array(
                    'type' => array(
                        array('select', array('comment', 'cause_real', 'cause_potential', 'closing_comment')),
                        'NOT NULL')
                )
            );
        }
	}
    
//    public function update_cache() {
//		if ($this->model->acl->get_level() < BEZ_AUTH_ADMIN) {
//			return false;
//		}
//		$this->content_cache = $this->helper->wiki_parse($this->content);
//	}
//
//	public function set_data($data, $filter=NULL) {
//        $input = array('content', 'type');
//        $val_data = $this->validator->validate($data, $input);
//
//		if ($val_data === false) {
//			throw new ValidationException('issues',	$this->validator->get_errors());
//		}
//
//        $this->set_property_array($val_data);
		
//		$this->content_cache = $this->helper->wiki_parse($this->content);
//    }

    public function set_data($post) {
        parent::set_data($post);
        $this->content_html = p_render('xhtml',p_get_instructions($this->content), $ignore);
    }

//    public function get_meta_fields() {
//        return array('reporter', 'datetime');
//    }
//
//    public function set_meta($post) {
//        parent::set_data($post, $this->get_meta_fields());
//    }
    
    public function mail_notify_add() {
//        if ($thread->id !== $this->thread_id) {
//            throw new Exception('issue object id and commcause->issue does not match');
//        }
        
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
