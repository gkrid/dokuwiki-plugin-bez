<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/bezcache.php";

class Issues extends Connect {
	public $coord_special = array('-proposal', '-rejected');
	public function __construct() {
		global $errors;
		parent::__construct();
		$createq = "CREATE TABLE IF NOT EXISTS issues (
				id INTEGER PRIMARY KEY,
				priority INTEGER NOT NULL DEFAULT 0,
				title TEXT NOT NULL,
				description TEXT NOT NULL,
				state INTEGER NOT NULL,
				opinion TEXT NULL,
				type INTEGER NOT NULL,
				coordinator TEXT NOT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				last_mod INTEGER)";
	$this->errquery($createq);
	
	$q = "PRAGMA table_info(issues)";
	$a = $this->fetch_assoc($q);
	$entity = false;
	foreach ($a as $r) 
		if ($r['name'] == 'entity') {
			$entity = true;
			break;
		}
	if ($entity) {
		$q = "	BEGIN TRANSACTION;
		CREATE TEMPORARY TABLE issues_backup
		(
				id INTEGER PRIMARY KEY,
				priority INTEGER NOT NULL DEFAULT 0,
				title TEXT NOT NULL,
				description TEXT NOT NULL,
				state INTEGER NOT NULL,
				opinion TEXT NULL,
				type INTEGER NOT NULL,
				coordinator TEXT NOT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				last_mod INTEGER);
		INSERT INTO issues_backup SELECT
				id,
				priority,
				title,
				description,
				state,
				opinion,
				type,
				coordinator,
				reporter,
				date,
				last_mod
			FROM issues;
		DROP TABLE issues;
		$createq;
		INSERT INTO issues SELECT 
				id,
				priority,
				title,
				description,
				state,
				opinion,
				type,
				coordinator,
				reporter,
				date,
				last_mod
			FROM issues_backup;
		DROP TABLE issues_backup;
		COMMIT;
		";
		$qa = explode(';', $q);
		foreach ($qa as $e)  {
			$this->db->query($e);
		}
	}

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

		/*priorytet z przedziału 0-2, w razie co poprawiamy*/
		$post['priority'] = (int)$post['priority'];
		if ($post['priority'] > 2)
			$post['priority'] = 2;
		else if ($post['priority'] < 0)
			$post['priority'] = 0;
		$data['priority'] = $post['priority'];

		$post['title'] = trim($post['title']);
		if (strlen($post['title']) == 0) {
			$errors['title'] = $bezlang['vald_title_required'];
		} elseif (strlen($post['title']) > $title_max) {
			$errors['title'] = str_replace('%d', $title_max, $bezlang['vald_title_too_long']);
		} elseif( ! preg_match('/^[[:alnum:] \-,._]*$/ui', $post['title'])) {
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
			return $data;
		}
		return false;
	}

	public function update($post, $data, $id, $update_last_mod=true) {
		global $INFO;
		$issue = $this->get_clean($id);
		if ($this->helper->user_admin() || $issue['coordinator'] == $INFO['client']) {
			$from_user = $this->validate($post, 'update', $id);
			$data = array_merge($data, $from_user);
			if ($update_last_mod)
				$data['last_mod'] = time();
			$this->errupdate($data, 'issues', $id);

			$cache = new Bezcache();
			$cache->issue_toupdate($id);

			return $data;
		}
		return false;
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

	public function join_coordinator($coord) {
		global $bezlang;
		$usro = new Users();
		if (!in_array($coord, $this->coord_special))
			return $usro->name($coord);
		else if ($coord == '-proposal')
			return $bezlang['none'].' ('.$bezlang['state_proposal'].')';
		else if ($coord == '-rejected')
			return $bezlang['none'].' ('.$bezlang['state_rejected'].')';
	}

	public function join($a) {
		$stao = new States();
		$a['state'] = $stao->name($a['state'], $a['coordinator']);

		$usro = new Users();
		$a['reporter'] = $usro->name($a['reporter']);

		$a['coordinator_email'] = $usro->email($a['coordinator']);
		$a['coordinator'] = $this->join_coordinator($a['coordinator']);

		$a['date'] = (int)$a['date'];

		$cache = new Bezcache();
		$wiki_text = $cache->get_issue($a['id']);
		$a['description'] = $wiki_text['description'];
		$a['opinion'] = $wiki_text['opinion'];

		return $a;
	}

	public function get_clean($id) {
		global $bezlang, $errors;
		if	( ! ($this->helper->token_viewer() || $this->helper->user_viewer()))
			return false;

		$id = (int) $id;

		$a = $this->fetch_assoc("SELECT * FROM issues WHERE id=$id");
		if (count($a) == 0) {
			$errors[] = $bezlang['error_issue_id_not_specifed'];
			return array();
		}
		$a = $a[0];
		return $a;
	}

	public function get_ids() {
		$a = $this->fetch_assoc("SELECT id FROM issues");
		$data = array();
		foreach ($a as $v)
			$data[] = $v['id'];
		return $data;
	}

	public function get_by_days() {
		global $bezlang, $errors, $conf;
		if (!$this->helper->user_viewer()) return false;

		$lang = 'pl';
		if ($conf['lang'] != 'pl')
			$lang = 'en';

		$res = $this->fetch_assoc("SELECT issues.id, issuetypes.$lang as type, title, coordinator, date
								FROM issues LEFT JOIN issuetypes ON issues.type = issuetypes.id
								ORDER BY date DESC");
		$create = $this->sort_by_days($res, 'date');
		foreach ($create as $day => $issues)
			foreach ($issues as $ik => $issue)
				$create[$day][$ik]['class'] = 'issue_created';

		$res2 = $this->fetch_assoc("SELECT issues.id, issuetypes.$lang as type, title, coordinator, last_mod
									FROM issues LEFT JOIN issuetypes ON issues.type = issuetypes.id
									WHERE state = 1 AND coordinator != '-rejected' AND coordinator != '-proposal'
									ORDER BY last_mod DESC");
		$close = $this->sort_by_days($res2, 'last_mod');
		foreach ($close as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$close[$day][$ik]['class'] = 'issue_closed';
				$close[$day][$ik]['date'] = $close[$day][$ik]['last_mod'];
			}

		$res3 = $this->fetch_assoc("SELECT issues.id, issuetypes.$lang as type, title, coordinator, date, last_mod
									FROM issues LEFT JOIN issuetypes ON issues.type = issuetypes.id
									WHERE coordinator = '-rejected' ORDER BY last_mod DESC");
		$rejected = $this->sort_by_days($res3, 'last_mod');
		foreach ($rejected as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$rejected[$day][$ik]['class'] = 'issue_rejected';
				$rejected[$day][$ik]['date'] = $rejected[$day][$ik]['last_mod'];
			}

		return $this->helper->days_array_merge($create, $close, $rejected);
	}

	public function get($id) {
		global $bezlang, $errors, $conf;
		if	( ! ($this->helper->token_viewer() || $this->helper->user_viewer()))
			return false;

		$id = (int) $id;

		$lang = 'pl';
		if ($conf['lang'] != 'pl')
			$lang = 'en';

		$a = $this->fetch_assoc("SELECT issues.id, priority, title, description, state, opinion,
								issuetypes.$lang as type, coordinator, reporter, date, last_mod
								FROM issues LEFT JOIN issuetypes ON issues.type = issuetypes.id WHERE issues.id=$id");
		if (count($a) == 0) {
			$errors[] = $bezlang['error_issue_id_not_specifed'];
			return array();
		}
		$a = $this->join($a[0]);

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

	public function get_coordinators() {
		$all = $this->fetch_assoc("SELECT coordinator FROM issues GROUP BY coordinator");
		$coordinators = array();

		$usro = new Users();
		foreach ($all as $row)
			if (!in_array($row['coordinator'], $this->coord_special))
				$coordinators[$row['coordinator']] = $usro->name($row['coordinator']);

		asort($coordinators);
		return $coordinators;
	}


	public function get_years() {
		$all = $this->fetch_assoc("SELECT date FROM issues ORDER BY date LIMIT 1");
		if (count($all) == 0)
			return array();
		$oldest = date('Y', $all[0]['date']);
		
		$years = array();
		for ($year = $oldest; $year <= (int)date('Y'); $year++)
			$years[] = $year;
		return $years;
	}

	/*Zwróć wszystkie osoby, które w jakikolwiek sposób były zaangażowane w problem.*/
	public function get_team($issue_id) {
		$issue_id = (int)$issue_id;

		$team = array();
		$a = $this->fetch_assoc('SELECT coordinator, reporter FROM issues WHERE id='.$issue_id);
		if (count($a) == 0) {
			$errors[] = $bezlang['error_issue_id_not_specifed'];
			return array();
		}
		if (!in_array($a[0]['coordinator'], $this->coord_special))
			$team[] = $a[0]['coordinator'];

		$team[] = $a[0]['reporter'];

		/*komentarze*/
		$a = $this->fetch_assoc('SELECT reporter FROM comments WHERE issue='.$issue_id);
		if (count($a) > 0)
			foreach ($a as $comment)
				$team[] = $comment['reporter'];


		/*przyczyny źródłowe*/
		$a = $this->fetch_assoc('SELECT reporter FROM causes WHERE issue='.$issue_id);
		if (count($a) > 0)
			foreach ($a as $causes)
				$team[] = $causes['reporter'];

		/*zadania*/
		$a = $this->fetch_assoc('SELECT executor, reporter FROM tasks WHERE issue='.$issue_id);
		if (count($a) > 0) 
			foreach ($a as $task) {
				$team[] = $task['reporter'];
				$team[] = $task['executor'];
			}


		/*usuń duplikaty*/
		$team = array_unique($team);
		sort($team);

		$usro = new Users();
		foreach ($team as &$member)
			$member = $usro->name($member);

		return $team;
	}

	/*waliduje te pola które są brane przy filtrowaniu*/
	public function validate_filters($filters) {

		$data = array('title' => '', 'state' => '-all', 'type' => '-all', 'coordinator' => '-all', 'year' => '-all');

		if (isset($filters['title'])) {
			if (strlen($post['title']) <= $title_max && preg_match('/^[[:alnum:] \-,._]*$/ui', $filters['title']))
				$data['title'] = $filters['title'];
		}

		if (isset($filters['state'])) {
			$stato = new States();
			if ($filters['state'] == '-all' || array_key_exists($filters['state'], $stato->get_all()))
				$data['state'] = $filters['state'];
		}

		if (isset($filters['type'])) {
			$isstyo = new Issuetypes();
			if ($filters['type'] == '-all' || array_key_exists($filters['type'], $isstyo->get()))
				$data['type'] = $filters['type'];
		}

		if (isset($filters['coordinator'])) {
			$usro = new Users();
			$coords = $usro->nicks();
			if ($filters['coordinator'] == '-all' || $filters['coordinator'] == '-none' ||
				in_array($filters['coordinator'], $coords))
				$data['coordinator'] = $filters['coordinator'];
		}

		if (isset($filters['year'])) {
			$years = $this->get_years();
			if ($filters['year'] == '-all' || in_array($filters['year'], $years))
				$data['year'] = $filters['year'];
		}

		return $data;
	}

	public function get_filtered($filters) {
		global $conf;
		
		$vfilters = $this->validate_filters($filters);

		$where = array();

		$year = $vfilters['year'];
		$state = $vfilters['state'];
		$coordinator = $vfilters['coordinator'];

		unset($vfilters['year']);
		unset($vfilters['state']);
		unset($vfilters['coordinator']);

		$title = $vfilters['title'];
		unset($vfilters['title']);
		if ($title != '') {
			//$where[] = "issues.title LIKE '%".str_replace('_', '\\_', $title)."%' ESCAPE '\\'";
			$where[] = "issues.title GLOB '*$title*'";
		}


		foreach ($vfilters as $name => $value)
			if ($value != '-all')
				$where[] = "issues.$name = '".$this->escape($value)."'";

		if ($state != '-all') {
			/*-proposal or -rejected*/
			if (strstr($state, '-')) 
				$where[] = "issues.coordinator = '$state'";
			else {
				$where[] = "issues.state = $state";
				$where[] = "issues.coordinator != '-proposal'";
				$where[] = "issues.coordinator != '-rejected'";
			}
		}
		if ($year != '-all') {
			if ($state == '-all' || $state == '-proposal' || $state == '0')
				$data_field = 'issues.date';
			else
				$data_field = 'issues.last_mod';

			$year = (int)$year;
			$where[] = "$data_field >= ".mktime(0,0,0,1,1,$year);
			$where[] = "$data_field < ".mktime(0,0,0,1,1,$year+1);
		}
		if ($coordinator != '-all') {
			if ($coordinator == '-none') 
				$where[] = "(issues.coordinator = '-proposal' OR issues.coordinator = '-rejected')";
			else 
				$where[] = "issues.coordinator = '$coordinator'";
		}

		$where_q = '';
		if (count($where) > 0)
			$where_q = 'WHERE '.implode(' AND ', $where);

		$lang = 'pl';
		if ($conf['lang'] != 'pl')
			$lang = 'en';

		$a = $this->fetch_assoc("
			SELECT issues.id, issues.priority, issues.state, issuetypes.$lang as type,
				issues.title, issues.coordinator, issues.date, issues.last_mod, COUNT(tasks.id) AS tasks_opened,
				(SELECT SUM(cost) FROM tasks WHERE tasks.issue = issues.id GROUP BY tasks.issue) AS cost
				FROM (issues LEFT JOIN issuetypes ON issues.type = issuetypes.id)
				LEFT JOIN (SELECT * FROM tasks WHERE state = 0) AS tasks ON issues.id = tasks.issue
			$where_q
			GROUP BY issues.id, issues.state, issues.type, issues.title, issues.date, issues.last_mod
			ORDER BY issues.priority DESC, issues.last_mod DESC, issues.date DESC
			");
		foreach ($a as &$row)
			$row = $this->join($row);
		return $a;
	}
	public function get_oldest_close_date() {
		$a = $this->fetch_assoc("SELECT last_mod FROM issues WHERE state=1 ORDER BY last_mod LIMIT 1");
		if (count($a) > 0)
			return (int)$a[0]['last_mod'];
		else
			return time();
	}

	public function cron_get_unsolved() {
		$a = $this->fetch_assoc("SELECT id, title, coordinator FROM issues
								WHERE state=0 AND coordinator <> '-proposal' AND coordinator <> '-rejected'");
		return $a;
	}
}
