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
    
	private function update_virtual_columns() {
		$code = $this->model->conf['lang'];
		$this->type = $this->$code;
	}
		
	public function __construct($model) {
		parent::__construct($model);
		
		$this->validator->set_rules(array(
			'pl' => array(array('length', 100), 'NOT NULL'),
			'en' => array(array('length', 100), 'NOT NULL')
		));
		
		//we've created empty object
		if ($this->id === NULL) {
            $this->update_virtual_columns();   
        }
	}
}
