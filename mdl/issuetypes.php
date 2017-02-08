<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'issuetype.php';

class BEZ_mdl_Issuetypes extends BEZ_mdl_Factory {
	
	public function get_one($id) {
		if ($this->auth->get_level() < 5) {
			return false;
		}
		
		$sth = $this->model->db->prepare('SELECT *, '.$this->model->lang_code.' as type
					(SELECT COUNT(*) FROM issues WHERE issues.type=issuetypes.id) AS refs
					FROM issuetypes WHERE id = ?');
		$sth->execute(array($id));
		
		$tasktype = $sth->fetchObject("BEZ_mdl_Issuetype",
					array($this->model));
		
		return $tasktype;
	}
	
	public function get_all($additional_fields=array()) {
		if ($this->auth->get_level() < 5) {
			return false;
		}
		
		if (in_array('refs', $additional_fields)) {
			$q = 'SELECT *, '.$this->model->lang_code.' as type,
					(SELECT COUNT(*) FROM issues WHERE issues.type=issuetypes.id) AS refs
					FROM issuetypes';
		} else {
			$q = 'SELECT *, '.$this->model->lang_code.' AS type FROM issuetypes';
		}
		
		$sth = $this->model->db->prepare($q);
		$sth->setFetchMode(PDO::FETCH_CLASS, "BEZ_mdl_Issuetype",
				array($this->model));

		$sth->execute();
		return $sth;
	}
	
	public function create_object() {
		$tasktype = new BEZ_mdl_Tasktype($this->model);
		return $tasktype;
	}
	
}
