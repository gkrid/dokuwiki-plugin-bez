<?php
 
if(!defined('DOKU_INC')) die();

/*
 * Task coordinator is taken from tasktypes
 */
require_once 'entity.php';

class BEZ_mdl_Task extends BEZ_mdl_Entity {
	//if errors = true we cannot save task
	
	//meta
	protected $reporter, $date, $close_date;
	
	//acl - set only on creation
	protected $issue;
	
	//data
	protected $cause, $executor, $task, $plan_date, $cost, $all_day_event, $start_time, $finish_time, $tasktype, $reason;
	
	//state
	protected $state;
	
	//virtual
	protected $coordinator, $action;
	
	public function get_columns() {
		return array('id', 'reporter', 'date', 'close_date', 'cause',
					'executor', 'tasktype', 'issue',
					'task', 'plan_date', 'cost', 'all_day_event',
					'start_time', 'finish_time',
					'state', 'reason', 'task_cache', 'reason_cache');
	}
	
	public function get_virtual_columns() {
		return array('coordinator', 'action', 'state_string', 'action_string');
	}
    
    private function state_string() {
		switch($this->state) {
            case '0':         return 'task_opened';
            case '-outdated': return 'task_outdated';
            case '1':         return 'task_done';
            case '2':         return 'task_rejected';
        }
	}
	
	private function action_string() {
		switch($this->action) {
			case '0': return 'correction';
			case '1': return 'corrective_action';
			case '2': return 'preventive_action';
			case '3': return 'programme';
		}
	}
    
    private function update_virtual_columns() {
		$this->state_string = $this->model->action->getLang($this->state_string());
        $this->action_string = $this->model->action->getLang($this->action_string());
	}
		
	//by defaults you can set: cause, tasktype and issue
	//tasktype is required
	public function __construct($model, $defaults=array()) {
		parent::__construct($model);

				
		//array(filter, NULL)
		$this->validator->set_rules(array(
			'reporter' => array(array('dw_user'), 'NOT NULL'),
			'date' => array(array('unix_timestamp'), 'NOT NULL'),
			'close_date' => array(array('unix_timestamp'), 'NULL'),
			'cause' => array(array('numeric'), 'NULL'),
			
			'executor' => array(array('dw_user'), 'NOT NULL'),
			
			'issue' => array(array('numeric'), 'NULL'),
			
			'task' => array(array('length', 10000), 'NOT NULL'),
			'plan_date' => array(array('iso_date'), 'NOT NULL'),
			'cost' => array(array('numeric'), 'NULL'),
			'all_day_event' => array(array('select', array('0', '1')), 'NOT NULL'), 
			'start_time' => array(array('time'), 'NULL'), 
			'finish_time' => array(array('time'), 'NULL'), 
			
			'state' => array(array('select', array('0', '1', '2')), 'NULL'),
			'reason' => array(array('length', 10000), 'NULL'),
			
			'coordinator' => array(array('dw_user', array('-none')), 'NOT NULL'),
		));
		
		//we've created empty object
		if ($this->id === NULL) {
			//meta
			$this->reporter = $this->auth->get_user();
			$this->date = time();
			
			$this->state = '0';
			$this->all_day_event = '1';
            		
			$val_data = $this->validator->validate($defaults, array('cause', 'issue', 'coordinator'));
			
			if ($val_data === false) {
				throw new Exception('error: $defaults invalid: '.print_r($this->validator->get_errors(), true));
			}
			
			$this->cause = $val_data['cause'];
			$this->issue = $val_data['issue'];
			$this->coordinator = $val_data['coordinator'];	
		}

		//takstype required	
		if ($this->issue != NULL) {
			$this->validator->set_rules(array(
				'tasktype' => array(array('numeric'), 'NULL')
			));
		} else {
			$this->validator->set_rules(array(
				'tasktype' => array(array('numeric'), 'NOT NULL')
			));
		}
		
		//we've created empty object
		if ($this->id === NULL) {
			$val_data = $this->validator->validate($defaults, array('tasktype'));
			if ($val_data === false) {
				throw new Exception('tasktype invalid: '.print_r($this->validator->get_errors(), true));
			}
			
			$this->tasktype = $val_data['tasktype'];
		}
		
		$this->auth->set_coordinator($this->coordinator);
		$this->auth->set_executor($this->executor);
	}
	
	public function set_meta($data) {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		
		$val_data = $this->validator->validate($data, array('reporter', 'date', 'close_date'));
		if ($val_data === false) {
			throw new ValidationException('tasks', $this->validator->get_errors());
		}
		
		foreach ($val_data as $k => $v) {
			$this->$k = $v;
		}
		
		return true;
	}
		
	public function update_cache() {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		$this->task_cache = $this->helper->wiki_parse($this->task);
		$this->reason_cache = $this->helper->wiki_parse($this->reason);
	}
	
	public function set_data($data) {
		if ($this->auth->get_level() >= 15) {
			$val_data = $this->validator->validate($data, array('executor',
				'cause', 'task', 'plan_date', 'cost', 'all_day_event',
				'start_time', 'finish_time', 'tasktype', 'reason'));
		//reporters can modify their own records if there is no coordinator
		} else if (	$this->coordinator === '-none' &&
					$this->reporter === $this->auth->get_user()) {
			$val_data = $this->validator->validate($data, array('executor',
			'task', 'plan_date', 'cost', 'all_day_event',
			'start_time', 'finish_time', 'tasktype', 'reason'));
			if ($val_data['executor'] !== $this->auth->get_user()) {
				$this->validator->set_error('executor', 'not_equal');
				return false;	
			}
		} else {
			throw new PermissionDeniedException();
		}
						
		if ($val_data === false) {
			throw new ValidationException('tasks', $this->validator->get_errors());
		}

		foreach ($val_data as $k => $v) {
				$this->$k = $v;
		}
		
		//specjalne reguły
		if ($this->issue == NULL) {
			$this->cause = NULL;
		}
		
		$this->auth->set_executor($this->executor);
		
		//set parsed
		$this->task_cache = $this->helper->wiki_parse($this->task);
		$this->reason_cache = $this->helper->wiki_parse($this->reason);
        
        //update virtuals
        $this->update_virtual_columns();
			
		return true;
	}
	
	public function set_state($data) {
		if ($this->auth->get_level() < 10) {
			return false;
		}
		//reason is required while changing state
		if ($data['state'] == '1' || $data['state'] == '2') {
			$this->validator->set_rules(array(
				'reason' => array(array('length', 10000), 'NOT NULL')
			));
		}
		
		$val_data = $this->validator->validate($data, array('state', 'reason'));
		if ($val_data === false) {
			throw new ValidationException('tasks', $this->validator->get_errors());
		}
		
		//if state is changed
		if ($this->state != $data['state']) {
			$this->close_date = time();
		}

		foreach ($val_data as $k => $v) {
			$this->$k = $v;
		}
		$this->reason_cache = $this->helper->wiki_parse($this->reason);
		
        //update virtuals
        $this->update_virtual_columns();
		
		return true;
	}
	
//	public function get_states() {
//		return array(	
//				'0' => 'task_opened',
//				'-outdated' => 'task_outdated',
//				'1' => 'task_done',
//				'2' => 'task_rejected'
//			);
//	}
//	
//	public function state_string($state='') {
//		if ($state === '') {
//			$state = $this->state;
//		}
//		
//		$states = $this->get_states();
//		return $states[$state];
//	}
//	
//	public function action_string($action) {
//		switch($action) {
//			case '0': return 'correction'; break;
//			case '1': return 'corrective_action'; break;
//			case '2': return 'preventive_action'; break;
//			case '3': return 'programme'; break;
//		}
//	}
    
    private function mail_notify($replacements=array(), $emails=false) {
        $plain = io_readFile($this->model->action->localFN('task-notification'));
        $html = io_readFile($this->model->action->localFN('task-notification', 'html'));
                
        $wiki_name = $this->model->conf['title'];
        $reps = array(  'wiki_name' => $wiki_name,
                        'who' => $this->reporter
                     );
        
        //$replacements can override $reps
        $rep = array_merge($reps, $replacements);

        if (!isset($rep['who_full_name'])) {
            $rep['who_full_name'] =
                $this->model->users->get_user_full_name($rep['who']);
        }
        
        //auto title
        if (!isset($rep['subject'])) {
            if (isset($rep['content'])) {
                $rep['subject'] =  $rep['who_full_name'].' '.$rep['action'];
            }
        }
       
        //we must do it manually becouse Mailer uses htmlspecialchars()
        $html = str_replace('@TASK_TABLE@', $rep['task_table'], $html);
        
        $mailer = new Mailer();
        $mailer->setBody($plain, $rep, $rep, $html, false);
        if ($emails === FALSE) {
            $emails = array_map(function($user) {
                return $this->model->users->get_user_email($user);
            }, array($this->executor));
        }
        $mailer->to($emails);
        $mailer->subject('[' . $wiki_name . '][BEZ] ' . $rep['subject']);

        $send = $mailer->send();
        if ($send === false) {
            throw new Exception("can't send email");
        }
    }
    
    public function mail_notify_add($issue_obj=NULL) {
        if ($issue_obj !== NULL && $issue_obj->id !== $this->issue) {
            throw new Exception('issue object id and task->issue does not match');
        }
        
       $top_row = array(
            '<strong>'.$this->model->action->getLang('executor').': </strong>' . 
            $this->model->users->get_user_full_name($this->executor),

            '<strong>'.$this->model->action->getLang('reporter').': </strong>' . 
            $this->model->users->get_user_full_name($this->reporter)
        );

        if ($this->tasktype_string != '') {
            $top_row[] =
                '<strong>'.$this->model->action->getLang('task_type').': </strong>' . 
                $this->tasktype_string;
        }

        if ($this->cost != '') {
            $top_row[] =
                '<strong>'.$this->model->action->getLang('cost').': </strong>' . 
                $this->cost;
        }

        //BOTTOM ROW
        $bottom_row = array(
            '<strong>'.$this->model->action->getLang('plan_date').': </strong>' . 
            $this->plan_date
        );			

        if ($this->all_day_event == '0') {
            $bottom_row[] =
                '<strong>'.$this->model->action->getLang('start_time').': </strong>' . 
                $this->start_time;
            $bottom_row[] =
                '<strong>'.$this->model->action->getLang('finish_time').': </strong>' . 
                $this->finish_time;
        }
        
        $rep = array(
            'content' => $this->task,
            'content_html' =>
                '<h2 style="font-size: 1.2em;">'.
	               '<a href="'.DOKU_URL.'doku.php?id='.$this->model->action->id('task', 'tid', $this->id).'">' .
		              '#z'.$this->id . 
	               '</a> ' . 
	lcfirst($this->action_string) . ' ' .
    '(' . 
        lcfirst($this->state_string) .
    ')' .      
                '</h2>' . 
                bez_html_irrtable(array(
                    'table' => array(
                        'border-collapse' => 'collapse',
                        'font-size' => '0.8em',
                        'width' => '100%'
                    ),
                    'td' => array(
                        'border-top' => '1px solid #8bbcbc',
                        'border-bottom' => '1px solid #8bbcbc',
                        'padding' => '.3em .5em'
                    )
                ), $top_row, $bottom_row) . $this->task_cache,
            'who' => $this->reporter,
            'when' => date('c', (int)$this->date),
            'custom_content' => true
        );
        
        $rep['action'] = $this->model->action->getLang('mail_task_added');
        $rep['action_color'] = '#e4f4f4';
        $rep['action_border_color'] = '#8bbcbc';
        
        if ($issue_obj === NULL) {
            $rep['action'] = $this->model->action->getLang('mail_task_added_programme');
            $this->mail_notify($rep);
        } else {
            $issue_obj->mail_notify($rep);
        }
    }
}
