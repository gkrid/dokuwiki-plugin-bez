<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'tasktype.php';

class BEZ_mdl_Tasktypes extends BEZ_mdl_Factory {
	
	public function get_one($id) {
		if ($this->auth->get_level() < 5) {
			return false;
		}
		
		$sth = $this->model->db->prepare('SELECT * FROM tasktypes WHERE id = ?');
		$sth->execute(array($id));
		
		$tasktype = $sth->fetchObject("BEZ_mdl_Tasktype",
					array($this->model));
		
		return $tasktype;
	}
	
	public function get_all($additional_fields=array()) {
		if ($this->auth->get_level() < 5) {
			return false;
		}
		
		if (in_array('refs', $additional_fields)) {
			$q = 'SELECT *,
					(SELECT COUNT(*) FROM tasks WHERE tasktype=tasktypes.id) AS refs
					FROM tasktypes;';
		} else {
			$q = 'SELECT * FROM tasktypes';
		}
		
		$sth = $this->model->db->prepare($q);
		$sth->setFetchMode(PDO::FETCH_CLASS, "BEZ_mdl_Tasktype",
				array($this->model));

		$sth->execute();
		return $sth;
	}
	
	public function create_object() {
		$tasktype = new BEZ_mdl_Tasktype($this->model);
		return $tasktype;
	}
	
	public function save($tasktype) {
		if ($tasktype->any_errors()) {
			return false;
		}
		
		$set = array();
		$execute = array();
		foreach ($tasktype->get_columns() as $column) {
			$set[] = ":$column";
			$execute[':'.$column] = $tasktype->$column;
		}
		
		$query = 'REPLACE INTO tasktypes ('.implode(',', $tasktype->get_columns()).')
									VALUES ('.implode(',', $set).')';
		$sth = $this->model->db->prepare($query);
		$sth->execute($execute);
	} 
}
