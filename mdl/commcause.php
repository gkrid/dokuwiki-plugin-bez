<?php
 
if(!defined('DOKU_INC')) die();

require_once 'entity.php';

class BEZ_mdl_Commcause extends BEZ_mdl_Entity {

	//real
	protected $id, $issue, $datetime, $reporter, $type, $content, $content_cache;
	
	//virtual
	protected $coordinator, $tasks_count;
	
    protected $parse_int = array('tasks_count');
	public function get_columns() {
		return array('id', 'issue', 'datetime', 'reporter', 'type', 'content', 'content_cache');
	}
	
	public function get_virtual_columns() {
		return array('coordinator', 'tasks_count');
	}
	
	public function get_table_name() {
		return 'commcauses';
	}
			
	public function __construct($model, $defaults=array()) {
		parent::__construct($model);
		
		$this->validator->set_rules(array(
			'issue' => array(array('numeric'), 'NOT NULL'),
			'datetime'	=> array(array('sqlite_datetime'), 'NOT NULL'),
			'reporter' => array(array('dw_user'), 'NOT NULL'),
			'type' => array(array('select', array('0', '1', '2')), 'NOT NULL'),
			'content' => array(array('length', 10000), 'NOT NULL'),
			'content_cache' => array(array('length', 10000), 'NOT NULL'),
			
			'coordinator' => array(array('dw_user', array('-proposal')), 'NOT NULL')
		));
		
		//new object
		if ($this->id === NULL) {
			$val_data = $this->validator->validate($defaults,
				array('issue', 'coordinator')); 
			if ($val_data === false) {
				throw new ValidationException('commcauses',	$this->validator->get_errors());
			}
			
			foreach ($val_data as $k => $v) {
				$this->$k = $v;
			}
			
			$this->type = '0';
			$this->reporter = $this->auth->get_user();
			$this->datetime = $this->sqlite_date();
		}
		
		if ($this->coordinator === NULL) {
			throw new Exception('commcause coordinator not specified.');
		}
		
		$this->auth->set_coordinator($this->coordinator);
	}
    
    public function update_cache() {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		$this->content_cache = $this->helper->wiki_parse($this->content);
	}
	
	public function set_data($data) {
		//only coordinator can add causes
		$input = array('content');
        //only coordinator chang change type and only 
		if ($this->auth->get_level() >= 15) {
			$input[] = 'type';
            if (isset($data['type']) && $data['type'] === '0'
                    && $this->tasks_count > 0) {
                throw new Exception('cannot change commcause to comment when it have any tasks assigned');
            }
		}

		
		//Możemy poprawiać tylko swoje komentarze i tylko jeżeli nie są przyczynami.
		if (! (	$this->auth->get_level() >= 15 ||
				($this->type === '0' && $this->reporter === $this->auth->get_user()))
			) {
			throw new PermissionDeniedException();
		}

			
		$val_data = $this->validator->validate($data, $input); 
		if ($val_data === false) {
			throw new ValidationException('commcauses',	$this->validator->get_errors());
		}
		
		foreach ($val_data as $k => $v) {
			$this->$k = $v;
		}
		
		$this->content_cache = $this->helper->wiki_parse($this->content);
    }
    
    public function mail_notify_add($issue_obj) {
        if ($issue_obj->id !== $this->issue) {
            throw new Exception('issue object id and commcause->issue does not match');
        }
        
        $rep = array(
            'content' => $this->content,
            'content_html' => $this->content_cache,
            'who' => $this->reporter,
            'when' => $this->datetime
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
        
        $issue_obj->mail_notify($rep);
    }
}
