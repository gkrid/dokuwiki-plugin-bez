<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'task.php';

class BEZ_mdl_Tasks extends BEZ_mdl_Factory {
	
	public function get_one($id) {
		if ($this->auth->get_level() < 5) {
			return false;
		}
		
		$q = "SELECT tasks.*,
						tasktypes.".$this->model->lang_code." AS tasktype_string,
						(CASE	WHEN tasks.issue IS NULL THEN '3'
								WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
								WHEN causes.potential = 0 THEN '1'
								ELSE '2' END) AS action,
						(CASE WHEN tasks.issue IS NULL THEN tasktypes.coordinator 
							  ELSE issues.coordinator END) AS coordinator,
						tasktypes.coordinator AS program_coordinator
						FROM tasks
							LEFT JOIN tasktypes ON tasks.tasktype = tasktypes.id
							LEFT JOIN causes ON tasks.cause = causes.id
							LEFT JOIN issues ON tasks.issue = issues.id
						WHERE tasks.id = ?";
			
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
		
		$task = $sth->fetchObject("BEZ_mdl_Task",
					array($this->model));
				
		return $task;
	}
	
	public function create_object($defaults) {
		if (isset($defaults['issue'])) {
			$defaults['coordinator'] =
				$this->model->issues->get_one($defaults['issue'])->coordinator;
			if (isset($defaults['tasktype'])) {
				$defaults['program_coordinator'] =
					$this->model->tasktypes->get_one($defaults['tasktype'])->coordinator;
			} else {
				$defaults['program_coordinator'] = $defaults['coordinator'];
			}
		} elseif (isset($defaults['tasktype'])) {
			$defaults['coordinator'] = $defaults['program_coordinator'] = 
					$this->model->tasktypes->get_one($defaults['tasktype'])->coordinator;
		}

		$task = new BEZ_mdl_Task($this->model, $defaults);
		return $task;
	}
	
	public function save($task) {
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
		$sth = $this->model->db->prepare($query);
		$sth->execute($execute);
		
		return $this->model->db->lastInsertId();
	}
}
