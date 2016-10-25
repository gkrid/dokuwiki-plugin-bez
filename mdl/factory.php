<?php
 
if(!defined('DOKU_INC')) die();

require_once 'auth.php';
require_once 'validator.php';

require_once 'issue.php';
require_once 'task.php';

class BEZ_mdl_Factory {
	private $auth, $validator, $db;
	public function __construct($auth, $validator) {
		$this->auth = $auth;
		$this->validator = $validator;
		$this->db =  new PDO('sqlite:/' . DOKU_INC . 'data/bez.sqlite');
	}
	
	public function issue($id) {
		$sth = $this->db->prepare('SELECT * FROM issues WHERE id = ?');
		$sth->execute(array($id));
		
		$sth->setFetchMode(PDO::FETCH_CLASS, "BEZ_mdl_Issue");
		$issue = $sth->fetch(PDO::FETCH_CLASS);
		
		return $issue;
	}
	
	public function task($id) {
		$sth = $this->db->prepare('SELECT * FROM tasks WHERE id = ?');
		$sth->execute(array($id));
		
		$task = $sth->fetchObject("BEZ_mdl_Task",
					array($this->auth, $this->validator, $this));
		
		return $task;
	}
	
	public function create_task_object($type, $defaults) {
		$task = new BEZ_mdl_Task($this->auth, $this->validator, $this, $defaults);
		return $task;
	}
	
	public function save_task($task) {
		if ($task->any_errors()) {
			return false;
		}
		
		$set = array();
		$execute = array();
		foreach ($task->get_columns() as $column) {
			$set[] = ":$column";
			$execute[':'.$column] = $task->$column;
		}
		
		$query = 'REPLACE INTO tasks ('.implode(',', $task->get_columns()).')
									VALUES ('.implode(',', $set).')';
		$sth = $this->db->prepare($query);
		$sth->execute($execute);
	}
	
	
	public function tasktype($id) {
		$sth = $this->db->prepare('SELECT * FROM tasktypes WHERE id = ?');
		$sth->execute(array($id));
		
		$tasktype = $sth->fetchObject("BEZ_mdl_Tasktype",
					array($this->validator));
		
		return $tasktype;
	}
	
	public function create_tasktype_object() {
		$tasktype = new BEZ_mdl_Tasktype($this->validator);
		return $tasktype;
	}
	
	public function save_tasktype($tasktype) {
		if ($task->any_errors()) {
			return false;
		}
		
		$set = array();
		$execute = array();
		foreach ($task->get_columns() as $column) {
			$set[] = ":$column";
			$execute[':'.$column] = $task->$column;
		}
		
		$query = 'REPLACE INTO tasks ('.implode(',', $task->get_columns()).')
									VALUES ('.implode(',', $set).')';
		$sth = $this->db->prepare($query);
		$sth->execute($execute);
	} 
}
