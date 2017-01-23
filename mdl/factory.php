<?php
 
if(!defined('DOKU_INC')) die();

require_once 'auth.php';

class BEZ_mdl_Factory {
	protected $model, $auth;
	
	public function __construct($model) {
		$this->model = $model;
		$this->auth = new BEZ_mdl_Auth($this->model->dw_auth, $this->model->user_nick);
	}
	
	public function get_level() {
		return $this->auth->get_level();
	}
		
	public function get_table_name() {
		$class = get_class($this);
		$exp = explode('_', $class);
		$table = lcfirst($exp[2]);
		return $table;
	}
	
	public function save($obj) {
		if ($obj->any_errors()) {
			return false;
		}
		
		$set = array();
		$execute = array();
		foreach ($obj->get_columns() as $column) {
			$set[] = ":$column";
			$execute[':'.$column] = $obj->$column;
		}
				
		$query = 'REPLACE INTO '.$this->get_table_name().'
							('.implode(',', $obj->get_columns()).')
							VALUES ('.implode(',', $set).')';
									
		$sth = $this->model->db->prepare($query);
		$sth->execute($execute);
		
		return $this->model->db->lastInsertId();
	}
	
	protected function delete_from_db($id) {
		$q = 'DELETE FROM '.$this->get_table_name().' WHERE id = ?';
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
	}
	
	public function delete($obj) {
		if ($this->auth->get_level() < 20) {
			return false;
		}
		$this->delete_from_db($obj->id);
	}
}
