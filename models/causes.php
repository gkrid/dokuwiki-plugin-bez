<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/event.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/bezcache.php";

class Causes extends Event {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = "CREATE TABLE IF NOT EXISTS causes (
				id INTEGER PRIMARY KEY,
				potential INTEGER DEFAULT 0,
				cause TEXT NOT NULL,
				reporter INTEGER NOT NULL,
				date INTEGER NOT NULL,
				issue INTEGER NOT NULL)";
		$this->errquery($q);
	}
	public function can_modify($cause_id) {
		$cause = $this->getone($cause_id);

		if ($cause && $this->issue->opened($cause['issue']))
			if ($this->helper->user_coordinator($cause['issue']) || $this->helper->user_admin()) 
				return true;

		return false;
	}
	public function validate($post) {
		global $bezlang, $errors;

		$cause_max = 65000;

		$post['cause'] = trim($post['cause']);
		if (strlen($post['cause']) == 0) 
			$errors['cause'] = $bezlang['vald_content_required'];
		else if (strlen($post['cause']) > $cause_max)
			$errors['cause'] = str_replace('%d', $cause_max, $bezlang['vald_content_too_long']);

		$data['cause'] = $post['cause'];

		if ($post['potential'] == '1') {
			$data['potential'] = 1;
		} else {
			$data['potential'] = 0;
		}

		return $data;
	}
	public function add($post, $data=array())
	{
		if ($this->helper->user_coordinator($data['issue']) && $this->issue->opened($data['issue'])) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);

			$this->errinsert($data, 'causes');
			$this->issue->update_last_mod($data['issue']);
		}
	}
	public function update($post, $data, $id) {
		if ($this->can_modify($id)) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);

			$this->errupdate($data, 'causes', $id);
			
			$cache = new Bezcache();
			$cache->cause_toupdate($id);
			
			$cause = $this->getone($id);
			$this->issue->update_last_mod($cause['issue']);
		}
	}
	public function delete($cause_id) {
		global $errors, $bezlang;
		if ($this->can_modify($cause_id)) {
			$data = $this->getone($cause_id);
			$tasko = new Tasks();
			$causes = $tasko->get($data['issue'], $cause_id);
			if (count($causes) == 0) {
				$this->errdelete('causes', $cause_id);
				$this->issue->update_last_mod($data['issue']);
			} else {
				$errors['cause'] = $bezlang['casue_cant_remove'];
			}
		}
	}

	public function join($a) {
		$usro = new Users();
		$a['reporter_nick'] = $a['reporter'];
		$a['reporter'] = $usro->name($a['reporter']);

		$a[tasks] = array();
		$tasko = new Tasks();
		$a['tasks'] = $tasko->get_clean($a['issue'], $a['id']);

		return $a;
	}

	public function getone($id) {
		$id = (int) $id;
		$cause = $this->fetch_assoc("SELECT * FROM causes WHERE id=$id");

		return $cause[0];
	}
	public function get($issue) {
		$issue = (int) $issue;

		$a = $this->fetch_assoc("SELECT * FROM causes WHERE issue=$issue");
		return $this->join_all($a);
	}
	
	public function get_ids($issue) {
		$issue = (int) $issue;

		$a = $this->fetch_assoc("SELECT id FROM causes WHERE issue=$issue");
		return $a;
	}
	
	public function get_potential($issue) {
		$issue = (int) $issue;

		$a = $this->fetch_assoc("SELECT * FROM causes WHERE issue=$issue AND potential = 1");
		return $this->join_all($a);
	}

	public function get_by_rootcause($issue) {
		return array();
	}
	public function get_by_days($days=7) {
		if (!$this->helper->user_viewer()) return false;
		
		$border_date = time() - $days*24*60*60;
		$res = $this->fetch_assoc("SELECT * FROM causes WHERE date > $border_date");
		
		$cache = new Bezcache();
		
		$create = $this->sort_by_days($res, 'date', false);
		foreach ($create as $day => $causes)
			foreach ($causes as $k => $cause) {
				$create[$day][$k]['class'] = 'cause';
				$create[$day][$k]['cause'] = $cache->get_cause($create[$day][$k]['id']);
			}
		return $create;
	}
}

