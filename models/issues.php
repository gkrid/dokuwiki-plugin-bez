<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/entities.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

class Issues extends Connect {
	private $coord_special = array('-proposal', '-rejected');
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = <<<EOM
CREATE TABLE IF NOT EXISTS issues (
	id INT(11) NOT NULL AUTO_INCREMENT,
	title CHAR(100) NOT NULL,
	description TEXT NOT NULL,
	state INT(11) NOT NULL,
	opinion TEXT NULL,
	type INT(11) NOT NULL,
	entity CHAR(100) NOT NULL,
	coordinator CHAR(100) NULL,
	reporter CHAR(100) NOT NULL,
	date INT(11) NOT NULL,
	last_mod INT(11) NOT NULL,

	PRIMARY KEY (id)
)
EOM;
	$this->errquery($q);
	}
	public function validate($post, $state='add', $issue_id=-1)
	{
		global $bezlang, $errors;

		$title_max = 100;
		$description_max = 65000;

		$isstyo = new Issuetypes();
		if ( ! array_key_exists((int)$post['type'], $isstyo->get())) {
			$errors['type'] = $bezlang['vald_type_required'];
		} 
		$data['type'] = (int)$post['type'];

		$ento = new Entities();
		if ( ! in_array($post['entity'], $ento->get_list())) {
			$errors['entity'] = $bezlang['vald_entity_required'];
		} 
		$data['entity'] = $post['entity'];

		/*Jeżeli nie jesteśmy adminem, to jeżeli chodzi o koordynatorów nie mamy nic do gadania*/
		/*Koordynator nie jest wymagany*/
		$usro = new Users();
		if ($this->helper->user_admin()) {
				if (!in_array($post['coordinator'], $usro->nicks()) && !in_array($post['coordinator'], $this->coord_special))
					$errors['coordinator'] = $bezlang['vald_coordinator_required'];

			$data['coordinator'] = $post['coordinator'];
		} else if ($state == 'update') {
			/*ta informacja jest potrzebna na potem*/
			$issue = $this->get_clean($issue_id);
			$data['coordinator'] = $issue['coordinator'];
		}

		$post['title'] = trim($post['title']);
		if (strlen($post['title']) == 0) {
			$errors['title'] = $bezlang['vald_title_required'];
		} elseif (strlen($post['title']) > $title_max) {
			$errors['title'] = str_replace('%d', $title_max, $bezlang['vald_title_too_long']);
		} elseif( ! preg_match('/^[[:alnum:] \-,.]*$/ui', $post['title'])) {
			$errors['title'] = $bezlang['vald_title_wrong_chars'];
		} 
		$data['title'] = $post['title'];

		$post['description'] = trim($post['description']);
		if (strlen($post['description']) == 0) {
			$errors['description'] = $bezlang['vald_desc_required'];
		} else if (strlen($post['description']) > $description_max) {
			$errors['description'] = str_replace('%d', $description_max, $bezlang['vald_desc_too_long']);
		} 
		$data['description'] = $post['description'];

		/*zmienamy status tylko w przypadku edycji*/
		/*oraz gdy istnieje koordynator*/
		/*oraz gdy nie ma żadnych otwartych zadań*/
		$tasko = new Tasks();
		if ($state == 'update' && array_key_exists('state', $post) && in_array($data['coordinator'], $usro->nicks())) {
			$post['state'] = (int)$post['state'];
			$stato = new States();
			if (!array_key_exists($post['state'], $stato->get()))
				$errors['state'] = $bezlang['vald_state_required'];
			elseif ($post['state'] != $stato->open() && $tasko->any_open($issue_id))
				$errors['state'] = $bezlang['vald_state_tasks_not_closed'];

			$data['state'] = $post['state'];
		}

		/*Przyczyna zamknięcia*/
		if (array_key_exists('opinion', $post) && $data['state'] == 1) {
			$opinion_max = 65000;
			if (strlen($post['opinion']) == 0) {
				$errors['opinion'] = $bezlang['vald_opinion_required'];
			} else if (strlen($post['opinion']) > $opinion_max) {
				$errors['opinion'] = str_replace('%d', $opinion_max, $bezlang['vald_opinion_too_long']);
			} 
			$data['opinion'] = $post['opinion'];
		}

		return $data;
	}

	public function add($post, $data=array()) {
		if ($this->helper->user_editor()) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$data['last_mod'] = time();
			$this->errinsert($data, 'issues');
		}
	}

	public function update($post, $data, $id) {
		global $INFO;
		$issue = $this->get_clean($id);
		if ($this->helper->user_admin() || $issue['coordinator'] == $INFO['client']) {
			$from_user = $this->validate($post, 'update', $id);
			$data = array_merge($data, $from_user);
			$data['last_mod'] = time();
			$this->errupdate($data, 'issues', $id);
		}
	}

	public function update_last_mod($id) {
		if ($this->helper->user_editor())
			$this->errupdate(array('last_mod' => time()), 'issues', $id);
	}

	public function opened($id) {
		$issue = $this->get_clean($id);
		if ($issue['state'] == 1 || $issue['coordinator'] == '-rejected')
			return false;

		return true;
	}

	public function join($a) {
		global $bezlang;
		$stao = new States();
		$a['state'] = $stao->name($a['state'], $a['coordinator']);

		$isstyo = new Issuetypes();
		$a['type'] = $isstyo->name($a['type']);

		$usro = new Users();
		$a['reporter'] = $usro->name($a['reporter']);

		if (!in_array($a['coordinator'], $this->coord_special))
			$a['coordinator'] = $usro->name($a['coordinator']);
		else if ($a['coordinator'] == '-proposal')
			$a['coordinator'] = $bezlang['none'].' ('.$bezlang['state_proposal'].')';
		else if ($a['coordinator'] == '-rejected')
			$a['coordinator'] = $bezlang['none'].' ('.$bezlang['state_rejected'].')';

		$a['date'] = (int)$a['date'];
		return $a;
	}

	public function get_clean($id) {
		global $bezlang, $errors;
		if ($this->helper->user_viewer()) {
			$id = (int) $id;

			$a = $this->fetch_assoc("SELECT * FROM issues WHERE id=$id");
			if (count($a) == 0) {
				$errors[] = $bezlang['error_issue_id_not_specifed'];
				return array();
			}
			$a = $a[0];
			return $a;
		}
	}

	public function get_by_days() {
		if (!$this->helper->user_viewer()) return false;

		$res = $this->fetch_assoc("SELECT * FROM issues ORDER BY date DESC");
		$create = $this->sort_by_days($res, 'date');
		foreach ($create as $day => $issues)
			foreach ($issues as $ik => $issue)
				$create[$day][$ik]['class'] = 'issue_created';

		$res2 = $this->fetch_assoc("SELECT * FROM issues WHERE state = 1 AND coordinator != '-rejected' AND coordinator != '-proposal' ORDER BY last_mod DESC");
		$close = $this->sort_by_days($res2, 'last_mod');
		foreach ($close as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$close[$day][$ik]['class'] = 'issue_closed';
				$close[$day][$ik]['date'] = $close[$day][$ik]['last_mod'];
			}

		$res3 = $this->fetch_assoc("SELECT * FROM issues WHERE coordinator = '-rejected 'ORDER BY last_mod DESC");
		$rejected = $this->sort_by_days($res3, 'last_mod');
		foreach ($rejected as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$rejected[$day][$ik]['class'] = 'issue_rejected';
				$rejected[$day][$ik]['date'] = $rejected[$day][$ik]['last_mod'];
			}

		return $this->helper->days_array_merge($create, $close, $rejected);
	}

	public function get($id) {
		global $bezlang, $errors;
		if ($this->helper->user_viewer()) {

			$a = $this->get_clean($id);
			if ($a == array())
				return $array;
			
			$a = $this->join($a);

			return $a;
		}
	}
	public function get_close_issue() {
		global $INFO;
		$coordinator = $INFO['client'];
		$a = $this->fetch_assoc("
			SELECT issues.id, issues.type, issues.title, issues.date, issues.last_mod, COUNT(tasks.id) AS tasks_opened
			FROM issues LEFT JOIN (SELECT * FROM tasks WHERE state = 0) AS tasks ON issues.id = tasks.issue
			WHERE issues.coordinator='$coordinator' AND issues.state=0
			GROUP BY issues.id, issues.type, issues.title, issues.date, issues.last_mod
			ORDER BY issues.last_mod DESC, issues.date DESC
			");

		foreach ($a as &$row)
			$row = $this->join($row);
		return $a;
	}

	public function get_comment_issue() {
		$a = $this->fetch_assoc("
			SELECT issues.id, issues.type, issues.title, issues.date, issues.last_mod, COUNT(tasks.id) AS tasks_opened
			FROM issues LEFT JOIN (SELECT * FROM tasks WHERE state = 0) AS tasks ON issues.id = tasks.issue
			WHERE issues.state = 0 
			GROUP BY issues.id, issues.type, issues.title, issues.date, issues.last_mod
			ORDER BY issues.last_mod DESC, issues.date DESC
			");

		foreach ($a as &$row)
			$row = $this->join($row);
		return $a;
	}

	public function get_stats() {
		$all = $this->fetch_assoc("SELECT COUNT(*) AS issues_all FROM issues;");
		$opened = $this->fetch_assoc("SELECT COUNT(*) as issues_opened FROM issues WHERE state=0;");

		$stats = array();
		$stats['all'] = $all[0]['issues_all'];
		$stats['opened'] = $opened[0]['issues_opened'];
		return $stats;
	}

	public function get_years() {
		$all = $this->fetch_assoc("SELECT FROM_UNIXTIME(date, '%Y') AS year FROM issues GROUP BY year ORDER BY year DESC;");
		$years = array();
		foreach ($all as $row)
			$years[] = $row['year'];
		return $years;
	}

	/*waliduje te pola które są brane przy filtrowaniu*/
	public function validate_filters($filters) {
		$data = array();

		$stato = new States();
		if ($filters['state'] == '-all' || array_key_exists($filters['state'], $stato->get_all()))
			$data['state'] = $filters['state'];
		else
			$data['state'] = '-all';

		$isstyo = new Issuetypes();
		if ($filters['state'] == '-all' || array_key_exists($filters['type'], $isstyo->get()))
			$data['type'] = $filters['type'];
		else
			$data['type'] = '-all';

		$ento = new Entities();
		if ($filters['entity'] == '-all' || in_array($filters['entity'], $ento->get_list()))
			$data['entity'] = $filters['entity'];
		else
			$data['entity'] = '-all';	

		$years = $this->get_years();
		if ($filters['year'] == '-all' || in_array($filters['year'], $years))
			$data['year'] = $filters['year'];
		else
			$data['year'] = '-all';

		return $data;
	}

	public function get_filtered($filters) {
		$vfilters = $this->validate_filters($filters);

		$where = array();

		$year = $vfilters['year'];
		$state = $vfilters['state'];

		unset($vfilters['year']);
		unset($vfilters['state']);

		foreach ($vfilters as $name => $value)
			if ($value != '-all')
				$where[] = "$name = '".$this->db->real_escape_string($value)."'";

		if ($year != '-all') {
			$year = (int)$year;
			$where[] = 'date >= '.mktime(0,0,0,1,1,$year);
			$where[] = 'date < '.mktime(0,0,0,1,1,$year+1);
		}
		if ($state != '-all') {
			/*-proposal or -rejected*/
			if (strstr($state, '-')) 
				$where[] = "coordinator = '$state'";
			else {
				$where[] = "state = $state";
				$where[] = "coordinator != '-proposal'";
				$where[] = "coordinator != '-rejected'";
			}
		}
		$where_q = '';
		if (count($where) > 0)
			$where_q = 'WHERE '.implode(' AND ', $where);

		$a = $this->fetch_assoc("SELECT * FROM issues $where_q ORDER BY date DESC");
		foreach ($a as &$row)
			$row = $this->join($row);
		return $a;
	}
}

