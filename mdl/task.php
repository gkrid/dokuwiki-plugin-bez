<?php
 
if(!defined('DOKU_INC')) die();

/*
 * Task coordinator is taken from tasktypes
 */

class BEZ_mdl_Task {
	//if errors = true we cannot save task
	private $auth, $model, $validator, $errors = false;
	
	//meta
	private $reporter, $date, $close_date, $cause;
	
	//acl
	//coordinator is defined by issue or tasktype
	private $tasktype, $issue, $coordinator;
	
	//data
	private $executor, $task, $plan_date, $cost, $all_day_event, $start_time, $finish_time;
	
	//state
	private $state, $reason;
	
	public function __get($property) {
		$columns = $this->get_columns();
		if (property_exists($this, $property) && in_array($property, $columns)) {
			return $this->$property;
		}
	}
	
	public function get_assoc() {
		$assoc = array();
		$columns = $this->get_columns();
		foreach ($columns as $col) {
			$assoc[$col] = $this->$col;
		}
		return $assoc;
	}
	
	public function any_errors() {
		return $this->errors;
	}
	
	private function set_defaults($defaults) {
		//meta
		$this->reporter = $this->auth->get_user();
		$this->date = time();
		if (isset($defaults['cause'])) {
			$this->cause = $defaults['cause'];
		}
		if (isset($defaults['tasktype'])) {
			$this->tasktype = $defaults['tasktype'];
		}
		if (isset($defaults['cause'])) {
			$this->cause = $defaults['cause'];
		}
		
		
		$this->all_day_event = '1';
		$this->state = '0';

	}
	
	private function set_coordinator() {
		if ($this->issue !== NULL) {
			$issue = $this->model->issue($this->issue);
			$this->coordinator = $issue->coordinator;
		} else if ($this->tasktype !== NULL) {
			$tasktype = $this->model->tasktype($this->tasktype);
			$this->coordinator = $tasktype->coordinator;
		}
		$this->auth->set_coordinator($this->coordinator);
	}
	
	public function get_columns() {
		return array('id', 'reporter', 'date', 'close_date', 'cause',
					'executor', 'tasktype', 'issue',
					'task', 'plan_date', 'cost', 'all_day_event',
					'start_time', 'finish_time',
					'state', 'reason');
	}
	
	//by defaults you can set: cause, tasktype and cause
	public function __construct($auth, $validator, $model, $defaults=array()) {	
		$this->auth = $auth;
		$this->model = $model;
		$this->validator = $validator;
		//array(filter, NULL)
		$this->validator->set_rules(array(
			'reporter' => array(array('dw_user'), 'NOT NULL'),
			'date' => array(array('unix_timestamp'), 'NOT NULL'),
			'close_date' => array(array('unix_timestamp'), 'NULL'),
			'cause' => array(array('numeric'), 'NULL'),
			
			'executor' => array(array('dw_user'), 'NOT NULL'),
			'tasktype' => array(array('numeric'), 'NOT NULL'),
			'issue' => array(array('numeric'), 'NULL'),
			
			'task' => array(array('length', 1000), 'NOT NULL'),
			'plan_date' => array(array('iso_date'), 'NOT NULL'),
			'cost' => array(array('numeric'), 'NULL'),
			'all_day_event' => array(array('select', array('0', '1')), 'NOT NULL'), 
			'start_time' => array(array('time'), 'NULL'), 
			'finish_time' => array(array('time'), 'NULL'), 
			
			'state' => array(array('select', array('0', '1', '2')), 'NULL'),
			'reason' => array(array('length', 1000), 'NULL'),
		));
		
		//we've created empty object
		if ($this->id === NULL) {
			$this->set_defaults($defaults);
		}
		$this->set_coordinator();
		$this->auth->set_executor($this->executor);
	}
	
	public function set_meta($data) {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		
		$val_data = $this->validator->validate($data);
		if ($val_data === false) {
			$this->errors = true;
			return false;
		}
		$this->errors = false;
		
		$this->reporter = $val_data['reporter'];
		$this->date = $val_data['date'];
		$this->close_date = $val_data['close_date'];
		
		return true;
	}
	
	public function set_acl($data) {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		
		$val_data = $this->validator->validate($data);
		if ($val_data === false) {
			$this->errors = true;
			return false;
		}
		$this->errors = false;
		
		$this->tasktype = $val_data['tasktype'];
		$this->issue = $val_data['issue'];
		
		$this->set_coordinator();
		
		return true;
	}
	
	public function set_data($data) {
		if ($this->auth->get_level() < 15) {
			return false;
		}
		
		$val_data = $this->validator->validate($data);
		if ($val_data === false) {
			$this->errors = true;
			return false;
		}
		$this->errors = false;
		
		$this->executor = $val_data['executor'];
		$this->auth->set_executor($this->executor);
		
		$this->task = $val_data['task'];
		$this->plan_date = $val_data['plan_date'];
		$this->cost = $val_data['cost'];
		$this->all_day_event = $val_data['all_day_event'];
		$this->start_time = $val_data['start_time'];
		$this->finish_time = $val_data['finish_time'];
		
		return true;
	}
	
	public function set_state($data) {
		if ($this->auth->get_level() < 10) {
			return false;
		}
		
		$val_data = $this->validator->validate($data);
		if ($val_data === false) {
			$this->errors = true;
			return false;
		}
		$this->errors = false;
		
		$this->state = $val_data['state'];
		$this->reason = $val_data['reason'];
		
		return true;
	}
}
