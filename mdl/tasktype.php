<?php
 
if(!defined('DOKU_INC')) die();

class BEZ_mdl_Tasktype {
	private $errors;
	
	public function any_errors() {
		return $this->errors;
	}
	
	public function __construct($validator) {
		$this->validator->set_rules(array(
			'pl' => => array(array('length', 100), 'NOT NULL'),
			'en' => => array(array('length', 100), 'NOT NULL'),
			'coordinator' => array(array('dw_user'), 'NOT NULL')
		);
	}
	
	public function set($data) {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		
		$val_data = $this->validator->validate($data);
		if ($val_data === false) {
			$this->errors = true;
			return false;
		}
		$this->errors = false;
	}
}
