<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'task.php';

class BEZ_mdl_Issues extends BEZ_mdl_Factory {
	public function get_one($id) {
		if ($this->auth->get_level() < 5) {
			return false;
		}
	
		$sth = $this->model->db->prepare('SELECT * FROM issues WHERE id = ?');
		$sth->execute(array($id));
		
		$task = $sth->fetchObject("BEZ_mdl_Issue",
					array($this->model));
		
		return $task;
	}
}
