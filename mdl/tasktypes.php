<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'tasktype.php';

class BEZ_mdl_Tasktypes extends BEZ_mdl_Factory {
	
	public function get_one($id) {
		$sth = $this->model->db->prepare('SELECT *,
                    '.$this->model->conf['lang'].' as type,
					(SELECT COUNT(*) FROM tasks WHERE tasktype=tasktypes.id) AS refs
					FROM tasktypes WHERE id = ?');
		$sth->execute(array($id));
		
		$tasktype = $sth->fetchObject("BEZ_mdl_Tasktype",
					array($this->model));
		
		return $tasktype;
	}
	
	public function get_all($additional_fields=array()) {
		if (in_array('refs', $additional_fields)) {
			$q = 'SELECT *, '.$this->model->conf['lang'].' as type,
					(SELECT COUNT(*) FROM tasks WHERE tasktype=tasktypes.id) AS refs
					FROM tasktypes';
		} else {
			$q = 'SELECT *, '.$this->model->conf['lang'].' as type FROM tasktypes';
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
    
    public function delete($obj) {
		if ($obj->refs > 0) {
			throw new Exception('you cannot delete tasktype that has any references');
		}
		parent::delete($obj);
	}
	
}
