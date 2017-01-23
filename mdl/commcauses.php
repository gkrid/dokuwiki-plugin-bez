<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'commcause.php';

class BEZ_mdl_Commcauses extends BEZ_mdl_Factory {
	
	public function get_one($id) {
		if ($this->auth->get_level() < 5) {
			return false;
		}
		
		$q = 'SELECT	commcauses.id, commcauses.issue, commcauses.datetime,
						commcauses.reporter, commcauses.type, commcauses.content,
						commcauses.content_cache, issues.coordinator
				FROM commcauses JOIN issues ON commcauses.issue = issues.id
				WHERE commcauses.id = ?';
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
		
		$commcause = $sth->fetchObject("BEZ_mdl_Commcause",
					array($this->model));
		
		return $commcause;
	}
	
	public function get_all($issue) {
		if ($this->auth->get_level() < 5) {
			return false;
		}
		
		$q = 'SELECT	commcauses.id, commcauses.issue, commcauses.datetime,
						commcauses.reporter, commcauses.type, commcauses.content,
						commcauses.content_cache, issues.coordinator
				FROM commcauses JOIN issues ON commcauses.issue = issues.id
				WHERE issue = ?';
		
		$sth = $this->model->db->prepare($q);
		$sth->setFetchMode(PDO::FETCH_CLASS, "BEZ_mdl_Commcause",
				array($this->model));

		$sth->execute(array($issue));
		return $sth;
	}
	
	public function create_object($defaults) {
		$issue_id = $defaults['issue'];
		$issue = $this->model->issues->get_one($issue_id);
		
		$coordinator = $issue->coordinator;
		$defaults['coordinator'] = $coordinator;

		$commcause = new BEZ_mdl_Commcause($this->model, $defaults);
		return $commcause;
	}
	
	public function delete($obj) {
		if ($this->auth->get_level() >= 15 ||
			$obj->reporter === $this->auth->get_user()) {
			$this->delete_from_db($obj->id);
		} else {
			throw new Exception('no permission');
		}
		
	}
}
