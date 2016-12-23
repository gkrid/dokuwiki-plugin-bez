<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/bezcache.php";
include_once DOKU_PLUGIN."bez/models/rootcauses.php";

class Issues extends Connect {
	public $coord_special = array('-proposal', '-rejected');
	public function __construct() {
		global $errors;
		parent::__construct();
		$createq = "CREATE TABLE IF NOT EXISTS issues (
				id INTEGER PRIMARY KEY,
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
	
	}
	public function validate($post)
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

		return $data;
	}
	
	public function validate_close($post)
	{
		global $bezlang, $errors;
		/*Przyczyna zamknięcia*/
		$data = array();
		
		$opinion_max = 65000;
		if (strlen($post['opinion']) == 0) {
			$errors['opinion'] = $bezlang['vald_opinion_required'];
		} else if (strlen($post['opinion']) > $opinion_max) {
			$errors['opinion'] = str_replace('%d', $opinion_max, $bezlang['vald_opinion_too_long']);
		} 
		$data['opinion'] = $post['opinion'];
		
		return $data;
	}

	public function add($post, $data=array()) {
		if ($this->helper->user_editor()) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$data['last_mod'] = time();
			$data['last_activity'] = date('Y-m-d H:i:s');
			
			$data['participants'] = $data['reporter'];
			if ($data['coordinator'] !== '-proposal') {
				$data['participants'] .= ','.$data['coordinator'];
			}
			$this->errinsert($data, 'issues');
			return $data;
		}
		return false;
	}

	public function update($post, $data, $id) {
		global $INFO;
		$issue = $this->get_clean($id);
		if ($this->helper->user_admin() || $issue['coordinator'] == $INFO['client']) {
			$from_user = $this->validate($post);
			$from_user2 = array();
			if ($issue['state'] == 1)
				$from_user2 = $this->validate_close($post);
			$data = array_merge($data, $from_user, $from_user2);
			$data['last_activity'] = date('Y-m-d H:i:s');
			$this->errupdate($data, 'issues', $id);

			$cache = new Bezcache();
			$cache->issue_toupdate($id);

			return $data;
		}
		return false;
	}
	
	public function close($post, $id) {
		global $INFO;
		$issue = $this->get_clean($id);
		$tasko = new Tasks();
		
		if ($this->helper->user_admin() || $issue['coordinator'] == $INFO['client']
			&& $this->any_task($id)
			&& !$this->any_open($id)
			&& !$this->cause_without_task($id)
			) {
			$from_user = $this->validate_close($post, $id);
			$data = array_merge($from_user, array('state' => '1', 'last_mod' => time()));
			$this->errupdate($data, 'issues', $id);

			$cache = new Bezcache();
			$cache->issue_toupdate($id);

			return $data;
		}
		return false;
	}
	public function reopen($id) {
		global $INFO;
		$issue = $this->get_clean($id);
		if ($this->helper->user_admin() || $issue['coordinator'] == $INFO['client']) {
			$this->errupdate(array('state' => '0'), 'issues', $id);
			return true;
		}
		return false;
	}

	public function update_last_mod($id) {
		//W obecnej wersji BEZ last_mod oznacza datę zamknięcia, więc uniemożliwiamy jej zmianę poza zamknięciem.
		/*if ($this->helper->user_editor())
			$this->errupdate(array('last_mod' => time()), 'issues', $id);*/
	}

	public function opened($id) {
		$issue = $this->get_clean($id);
		if ($issue['state'] == 1 || $issue['coordinator'] == '-rejected')
			return false;

		return true;
	}

	public function is_proposal($id) {
		$issue = $this->get_clean($id);
		if ($issue['coordinator'] == '-proposal')
			return true;
		return false;
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
		global $bezlang;
		
		$stao = new States();
		$tasko = new Tasks();
		$causo = new Causes();

		$a['raw_state'] = $a['state'];
		
		//$a['state'] = $stao->name($a, $tasko->any_task($a['id']));
		$a['state'] = $stao->name($a, $tasko->any_task($a['id']));

		$usro = new Users();
		$a['reporter'] = $usro->name($a['reporter']);

		$a['raw_coordinator'] = $a['coordinator'];
		$a['coordinator_email'] = $usro->email($a['coordinator']);
		$a['coordinator'] = $this->join_coordinator($a['coordinator']);

		$a['date'] = (int)$a['date'];

		$cache = new Bezcache();
		$wiki_text = $cache->get_issue($a['id']);
		$a['description'] = $wiki_text['description'];
		$a['raw_opinion'] = $a['opinion'];
		$a['opinion'] = $wiki_text['opinion'];
		
		$a['causes'] = $causo->get_ids($a['id']);
		$a['corrections'] = $tasko->get_corrections_ids($a['id']);
		
		//priorytet na podstaie zadań
		//$a['priority'] = $tasko->issue_priority($a['id']);
		if ($a['state'] == $bezlang['state_rejected']) {
			$a['priority'] = '3';
		} else if ($a['priority'] == NULL) {
			$a['priority'] = 'None';
		}

		return $a;
	}
	
	public function get_state($issue_clean) {
		$tasko = new Tasks();
		$stao = new States();
		$state = $stao->name($issue_clean, $tasko->any_task($issue_clean['id']));
		
		return array('state' => $state, 'raw_state' => $a['state']);
	}

	public function get_clean($id) {
		global $bezlang, $errors;
		if	( ! ($this->helper->token_viewer() || $this->helper->user_viewer()))
			return false;

		$id = (int) $id;

		$a = $this->fetch_assoc("SELECT *,
				(SELECT MIN((CASE	WHEN tasks.state > 0 THEN '3'
					WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
					WHEN tasks.plan_date >= date('now') THEN '1'
					ELSE '0' END)) FROM tasks WHERE tasks.issue = issues.id) AS priority FROM issues WHERE id=$id");
		
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

	public function get_by_days($days=7) {
		global $bezlang, $errors, $conf;
		if (!$this->helper->user_viewer()) return false;
		
		$border_date = time() - $days*24*60*60;
		
		$lang = 'pl';
		if ($conf['lang'] != 'pl')
			$lang = 'en';

		$res = $this->fetch_assoc("SELECT issues.id, issuetypes.$lang as type, title, coordinator, date
								FROM issues LEFT JOIN issuetypes ON issues.type = issuetypes.id
								WHERE date > $border_date
								ORDER BY date DESC");
		$create = $this->sort_by_days($res, 'date');
		foreach ($create as $day => $issues)
			foreach ($issues as $ik => $issue)
				$create[$day][$ik]['class'] = 'issue_created';

		$res2 = $this->fetch_assoc("SELECT issues.id, issuetypes.$lang as type, title, coordinator, last_mod
									FROM issues LEFT JOIN issuetypes ON issues.type = issuetypes.id
									WHERE last_mod > $border_date
									AND   state = 1 AND coordinator != '-rejected' AND coordinator != '-proposal'
									ORDER BY last_mod DESC");
		$close = $this->sort_by_days($res2, 'last_mod');
		foreach ($close as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$close[$day][$ik]['class'] = 'issue_closed';
				$close[$day][$ik]['date'] = $close[$day][$ik]['last_mod'];
			}

		$res3 = $this->fetch_assoc("SELECT issues.id, issuetypes.$lang as type, title, coordinator, date, last_mod
									FROM issues LEFT JOIN issuetypes ON issues.type = issuetypes.id
									WHERE last_mod > $border_date
									AND   coordinator = '-rejected' ORDER BY last_mod DESC");
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

		$a = $this->fetch_assoc("SELECT issues.id, title, description, state, opinion,
								issuetypes.$lang as type, coordinator, reporter, date, last_mod,
								(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.state != 0 AND
									issues.id = tasks.issue) AS tasks_closed,
								(SELECT COUNT(tasks.id) FROM tasks WHERE issues.id = tasks.issue) AS tasks_all,
								(SELECT MIN((CASE	WHEN tasks.state > 0 THEN '3'
											WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
											WHEN tasks.plan_date >= date('now') THEN '1'
											ELSE '0' END)) FROM tasks WHERE tasks.issue = issues.id) AS priority
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
		$a = $this->fetch_assoc('SELECT coordinator FROM issues WHERE id='.$issue_id);
		if (count($a) == 0) {
			$errors[] = $bezlang['error_issue_id_not_specifed'];
			return array();
		}
		if (!in_array($a[0]['coordinator'], $this->coord_special))
			$team[] = $a[0]['coordinator'];

		//$team[] = $a[0]['reporter'];

		/*komentarze*/
		$a = $this->fetch_assoc('SELECT reporter FROM comments WHERE issue='.$issue_id);
		if (count($a) > 0)
			foreach ($a as $comment)
			$team[] = $comment['reporter'];


		/*przyczyny źródłowe*/
		/*$a = $this->fetch_assoc('SELECT reporter FROM causes WHERE issue='.$issue_id);
		if (count($a) > 0)
			foreach ($a as $causes)
			$team[] = $causes['reporter'];*/

		/*zadania*/
		$a = $this->fetch_assoc('SELECT executor FROM tasks WHERE issue='.$issue_id);
		if (count($a) > 0) 
			foreach ($a as $task) {
				//$team[] = $task['reporter'];
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

	$data = array('title' => '', 'state' => '-all', 'type' => '-all', 'coordinator' => '-all', 'year' => '-all',
				'sort_open' => '', 'rootcause' => '-all');

		if (isset($filters['title'])) {
			if (strlen($post['title']) <= $title_max && preg_match('/^[[:alnum:] \-,._]*$/ui', $filters['title']))
				$data['title'] = $filters['title'];
		}

		if (isset($filters['sort_open'])) {
			if ($filters['sort_open'] == 'on')
				$data['sort_open'] = 'on';
		}

		if (isset($filters['state'])) {
			$stato = new States();
			if ($filters['state'] == '-all' || array_key_exists($filters['state'], $stato->get_list()))
				$data['state'] = $filters['state'];
		}

		if (isset($filters['type'])) {
			$isstyo = new Issuetypes();
			if ($filters['type'] == '-all' || array_key_exists($filters['type'], $isstyo->get()))
				$data['type'] = $filters['type'];
		}
		
		if (isset($filters['coordinator'])) {
			$usro = new Users();
			$excs = $usro->nicks();
			
			if ($filters['coordinator'] == '-all' || $filters['coordinator'] == '-none')
				$data['coordinator'] = $filters['coordinator'];
			else if ($filters['coordinator'][0] == '@') {
				$groups = $usro->groups();
				$group = substr($filters['coordinator'], 1);
				if (in_array($group, $groups))
					$data['coordinator'] = $filters['coordinator'];
			} else if (in_array($filters['coordinator'], $excs)) {
				$data['coordinator'] = $filters['coordinator'];
			}
		}

		if (isset($filters['year'])) {
			$years = $this->get_years();
			if ($filters['year'] == '-all' || in_array($filters['year'], $years))
				$data['year'] = $filters['year'];
		}
		
		if (isset($filters['rootcause'])) {
			$rootco = new Rootcauses();
			if ($filters['rootcause'] == '-all' || array_key_exists($filters['rootcause'], $rootco->get()))
				$data['rootcause'] = (int)$filters['rootcause'];
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
		$rootcause = $vfilters['rootcause'];


		unset($vfilters['year']);
		unset($vfilters['state']);
		unset($vfilters['coordinator']);
		unset($vfilters['rootcause']);
		
		$title = $vfilters['title'];
		unset($vfilters['title']);

		$sort_open = $vfilters['sort_open'];
		unset($vfilters['sort_open']);
		if ($title != '') {
			//$where[] = "issues.title LIKE '%".str_replace('_', '\\_', $title)."%' ESCAPE '\\'";
			$where[] = "issues.title GLOB '*$title*'";
		}
		

		foreach ($vfilters as $name => $value)
			if ($value != '-all')
				$where[] = "issues.$name = '".$this->escape($value)."'";

		if ($state != '-all') {
			if ($state == '-done') {
				$where[] = 'tasks_all > 0';
				$where[] = 'tasks_all = tasks_closed';
				$where[] = 'issues.state = 0';
			/*-proposal or -rejected*/
			} else if (strstr($state, '-')) 
				$where[] = "issues.coordinator = '$state'";
			/*rejected*/
			else if ($state == 2) {
				$where[] = "issues.state = 1";
				$where[] = "tasks_all == 0";
			} else {
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
			elseif ($coordinator[0] == '@') {
				$group = substr($coordinator, 1);
				$usro = new Users();
				$users = $usro->users_of_group($group);
				
				$usr_where = array();
				foreach($users as $user) {
					$usr_where[] = "issues.coordinator = '".$this->escape($user)."'";
				}

				$where[] = "(".implode(" OR ", $usr_where).")";
			} else 
				$where[] = "issues.coordinator = '$coordinator'";
		}
		$rootcause_join = '(issues LEFT JOIN issuetypes ON issues.type = issuetypes.id)';
		$rootcause_group = '';
		if($rootcause != '-all') {
			$rootcause_join = 'causes JOIN issues ON causes.issue = issues.id LEFT JOIN issuetypes ON issues.type = issuetypes.id';
			$rootcause_group = 'GROUP BY issues.id';
			$where[] = 'causes.rootcause = '.$this->escape($rootcause);
		}

		$where_q = '';
		if (count($where) > 0)
			$where_q = 'WHERE '.implode(' AND ', $where);

		$lang = 'pl';
		if ($conf['lang'] != 'pl')
			$lang = 'en';

		if (isset($sort_open) && $sort_open == 'on')
			$order = 'ORDER BY issues.date DESC, state, priority, issues.last_mod DESC';
		else
			$order = 'ORDER BY state, priority, issues.date DESC';

		$a = $this->fetch_assoc("
			SELECT issues.id, issues.state, issuetypes.$lang as type,
				issues.title, issues.coordinator, issues.date, issues.last_mod,
				(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.state != 0 AND issues.id = tasks.issue)
				AS tasks_closed,
				(SELECT COUNT(tasks.id) FROM tasks WHERE issues.id = tasks.issue) AS tasks_all,
				(SELECT SUM(cost) FROM tasks WHERE tasks.issue = issues.id GROUP BY tasks.issue) AS cost,
				(SELECT MIN((CASE	WHEN tasks.state > 0 THEN '3'
											WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
											WHEN tasks.plan_date >= date('now') THEN '1'
											ELSE '0' END)) FROM tasks WHERE tasks.issue = issues.id) AS priority
				FROM $rootcause_join
				$where_q
				$rootcause_group
				$order
			");
			//var_dump($a);
		foreach ($a as &$row) {
			$row = $this->join($row);
		}
		return $a;
	}
	public function get_oldest_close_date() {
		$a = $this->fetch_assoc("SELECT last_mod FROM issues WHERE state=1 ORDER BY last_mod LIMIT 1");
		if (count($a) > 0)
			return (int)$a[0]['last_mod'];
		else
			return time();
	}

	public function get_oldest_open_date() {
		$a = $this->fetch_assoc("SELECT date FROM issues ORDER BY date LIMIT 1");
		if (count($a) > 0)
			return (int)$a[0]['date'];
		else
			return time();
	}

	public function cron_get_unsolved() {
		$a = $this->fetch_assoc("SELECT issues.id, issuetypes.pl as type, date, last_mod, title, coordinator,
						(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.state != 0 AND issues.id = tasks.issue) AS tasks_closed,
						(SELECT COUNT(tasks.id) FROM tasks WHERE issues.id = tasks.issue) AS tasks_all,
						(SELECT MIN((CASE	WHEN tasks.state > 0 THEN '3'
											WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
											WHEN tasks.plan_date >= date('now') THEN '1'
											ELSE '0' END)) FROM tasks WHERE tasks.issue = issues.id) AS priority
						FROM issues JOIN issuetypes ON issues.type = issuetypes.id
						WHERE priority = '0' OR priority = '1' OR priority = '2'
						ORDER BY priority, issues.last_mod DESC, issues.date DESC");
		
		return $a;
	}
	
	public function cause_without_task($issue_id) {
		$causes = $this->fetch_assoc("SELECT id FROM causes WHERE issue=$issue_id");
		foreach ($causes as $cause) {
			$tasks = $this->fetch_assoc("SELECT id FROM tasks WHERE cause=$cause[id]");
			if (count($tasks) == 0)
				return true;
		}
		return false;
	}
}
