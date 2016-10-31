<?php
 
if(!defined('DOKU_INC')) die();

require_once 'entity.php';

class BEZ_mdl_Tasktype extends BEZ_mdl_Entity {

	protected $id, $pl, $en, $coordinator, $type;
	
	public function get_columns() {
		return array('id', 'pl', 'en', 'coordinator');
	}
	
	public function get_virtual_columns() {
		return array('type');
	}
		
	private function set_type() {
		$code = $this->model->lang_code;
		$this->type = $this->$code;
	}
	
	public function __construct($model) {
		parent::__construct($model);
		
		$this->validator->set_rules(array(
			'pl' => array(array('length', 100), 'NOT NULL'),
			'en' => array(array('length', 100), 'NOT NULL'),
			'coordinator' => array(array('dw_user'), 'NOT NULL')
		));
		
		$this->set_type();
		$this->auth->set_coordinator($this->coordinator);
	}
	
	public function set($data) {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		
		$val_data = $this->validator->validate($data, array('pl', 'en' ,'coordinator')); 
		if ($val_data === false) {
			$this->errors = true;
			return false;
		}
		$this->errors = false;
		
		foreach ($val_data as $k => $v) {
			$this->$k = $v;
		}
		$this->auth->set_coordinator($this->coordinator);
		$this->set_type();
	}
}
