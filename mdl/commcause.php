<?php
 
if(!defined('DOKU_INC')) die();

require_once 'entity.php';

class BEZ_mdl_Commcause extends BEZ_mdl_Entity {

	//real
	protected $id, $issue, $datetime, $reporter, $type, $content, $content_cache;
	
	//virtual
	protected $coordinator;
	
	public function get_columns() {
		return array('id', 'issue', 'datetime', 'reporter', 'type', 'content', 'content_cache');
	}
	
	public function get_virtual_columns() {
		return array('coordinator');
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
			'content' => array(array('length', 10000), 'NULL'),
			'content_cache' => array(array('length', 10000), 'NULL'),
			
			'coordinator' => array(array('dw_user'), 'NOT NULL')
		));
		
		//new object
		if ($this->id === NULL) {
			$val_data = $this->validator->validate($defaults,
				array('issue', 'coordinator')); 
			if ($val_data === false) {
				throw new Exception('Invalid defaults.');
			}
			
			foreach ($val_data as $k => $v) {
				$this->$k = $v;
			}
			
			$this->reporter = $this->auth->get_user();
			$this->datetime = $this->sqlite_date();
		}
		
		if ($this->coordinator == NULL) {
			throw new Exception('commcause coordinator not specified.');
		}
		
		$this->auth->set_coordinator($this->coordinator);
	}
	
	public function set_data($data) {
		if (! (	$this->auth->get_level() >= 15 ||
				$this->reporter === $this->auth->get_user())
			) {
			throw new Exception('no permission');
		}
			
		$val_data = $this->validator->validate($data, array('type', 'content')); 
		if ($val_data === false) {
			$this->errors = true;
			return false;
		}
		$this->errors = false;
		
		foreach ($val_data as $k => $v) {
			$this->$k = $v;
		}
		
		$this->content_cache = $this->helper->wiki_parse($this->content);
	}
}
