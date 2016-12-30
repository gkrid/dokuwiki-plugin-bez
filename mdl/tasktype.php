<?php
 
if(!defined('DOKU_INC')) die();

require_once 'entity.php';

class BEZ_mdl_Tasktype extends BEZ_mdl_Entity {

	protected $id, $pl, $en;
	protected $refs, $type;
	
	public function get_columns() {
		return array('id', 'pl', 'en');
	}
	
	public function get_virtual_columns() {
		return array('type', 'refs');
	}
	
	public function get_table_name() {
		return 'tasktypes';
	}
		
	private function set_type() {
		$code = $this->model->lang_code;
		$this->type = $this->$code;
	}
	
	public function __construct($model) {
		parent::__construct($model);
		
		$this->validator->set_rules(array(
			'pl' => array(array('length', 100), 'NOT NULL'),
			'en' => array(array('length', 100), 'NOT NULL')
		));
		
		$this->set_type();
	}
	
	public function set($data) {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		
		$val_data = $this->validator->validate($data, array('pl', 'en')); 
		if ($val_data === false) {
			$this->errors = true;
			return false;
		}
		$this->errors = false;
		
		foreach ($val_data as $k => $v) {
			$this->$k = $v;
		}
		$this->set_type();
	}
	
	public function remove() {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		if ($this->refs > 0) {
			$this->validator->set_error('refs', 'must_be_0');
			return false;
		}
		parent::remove();
	}
}
