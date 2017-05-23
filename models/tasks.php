<?php
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";
include_once DOKU_PLUGIN."bez/models/taskstates.php";
include_once DOKU_PLUGIN."bez/models/tasktypes.php";
include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/event.php";
include_once DOKU_PLUGIN."bez/models/bezcache.php";
include_once DOKU_PLUGIN."bez/models/issues.php";

class Tasks extends Event {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = "CREATE TABLE IF NOT EXISTS tasks (
				id INTEGER PRIMARY KEY,
				task TEXT NOT NULL,
				state INTEGER NOT NULL,
				tasktype INTEGER NULL,
				executor TEXT NOT NULL,
				cost INTEGER NULL,
				reason TEXT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				close_date INTEGER NULL,
				cause INTEGER NULL,
				plan_date TEXT NOT NULL,
				all_day_event INTEGET DEFAULT 0,
				start_time TEXT NULL,
				finish_time TEXT NULL,
				issue INTEGER NULL
				)";
		$this->errquery($q);
	}
	public function can_modify($task_id) {
		$task = $this->getone($task_id);

		if ($task) {
			if ($this->helper->user_coordinator($task['issue']) ||
			$this->helper->user_admin()) {
				return true;
			}
		}

		return false;
	}
	public function can_change_state($task_id) {
		global $INFO;
		$task = $this->getone($task_id);

		if ($task['executor'] == $INFO['client'] &&
			($task['issue'] == NULL || $this->issue->opened($task['issue']))) {
			return true;
		}

		return false;
	}
	public function validate($post) {
		global $bezlang, $errors;

		$task_max = 65000;
		$cost_max = 1000000;

		$post['task'] = trim($post['task']);
		if (strlen($post['task']) == 0) 
			$errors['task'] = $bezlang['vald_content_required'];
		else if (strlen($post['task']) > $task_max)
			$errors['task'] = str_replace('%d', $task_max, $bezlang['vald_content_too_long']);

		$data['task'] = $post['task'];

		$usro = new Users();
		if ( ! in_array($post['executor'], $usro->nicks())) {
			$errors['executor'] = $bezlang['vald_executor_not_exists'];
		}
		$data['executor'] = $post['executor'];

		//cost is not required
		if ($post['cost'] != '') {
			$cost = trim($post['cost']);
			if ( ! ctype_digit($cost)) {
				$errors['cost'] = $bezlang['vald_cost_wrong_format'];
			} elseif ( (int)$post['cost'] > $cost_max) {
				$errors['cost'] = str_replace('%d', $cost_max, $bezlang['vald_cost_too_big']);
			}
			$data['cost'] = (int) $post['cost'];
		} else {
			$data['cost'] = '';
		}
		
		//tasktype is not required
		if ($post['tasktype'] != '') {
			$tasktype = $post['tasktype'];
			if (!ctype_digit($tasktype)) {
				$errors['tasktype'] = $bezlang['vald_cost_wrong_format'];
			}
			$data['tasktype'] = (int) $post['tasktype'];
		} else {
			$data['tasktype'] = '';
		}
		
		/*zmienamy status tylko w przypadku edycji*/
		/*if (array_key_exists('state', $post)) 
			$data['state'] = $this->val_state($post['state']);*/

		/*if (array_key_exists('reason', $post) &&
							($data[state] == 2 || ($data[state] == 1 && $post[action] == 2)))*/
							
		if (array_key_exists('reason', $post))
			$data['reason'] = $this->val_reason($post['reason']);

		if (isset($_POST['cause']))
			if ($_POST['cauese'] == '')
				$data['cause'] = NULL;
			else
				$data['cause'] = (int)$_POST['cause'];

		return $data;
	}
	public function val_state($state) {
		global $errors, $bezlang;

		$taskso = new Taskstates();
		if ( ! array_key_exists((int)$state, $taskso->get())) {
			$errors['state'] = $bezlang['vald_state_required'];
			return -1;
		} 
		return (int) $state;
	}
	public function val_reason($reason) {
		global $errors, $bezlang;

		$reason_max = 65000;

		$reason = trim($reason);
		if (strlen($reason) == 0) 
			$errors['reason'] = $bezlang['vald_content_required'];
		else if (strlen($resaon) > $reason_max)
			$errors['reason'] = str_replace('%d', $task_max, $bezlang['vald_content_too_long']);

		return $reason;
	}
	
	public function validate_plan($post) {
		global $errors, $bezlang;
		$data = array();
		if (strtotime($post['plan_date']) === false) {
			$errors['plan_date'] = $bezlang['vald_valid_date_required'];
		} else
			$data['plan_date'] = date('Y-m-d', strtotime($post['plan_date']));
		
		if (!isset($post['all_day_event']) && $data['plan_date'] != '') {
			$data['all_day_event'] = '0';
			if (!preg_match('/^[0-9]{1,2}:[0-9][0-9]$/', $post['start_time']) ||
				strtotime($post['start_time']) === false) {
				$errors['start_time'] = $bezlang['vald_valid_start_hour_required'];
			} else {
				$data['start_time'] = $post['start_time'];
				$start_time = strtotime($post['start_time']);
			}
			
			if (!preg_match('/^[0-9]{1,2}:[0-9][0-9]$/', $post['finish_time']) ||
				strtotime($post['finish_time']) === false) {
				$errors['finish_time'] = $bezlang['vald_valid_finish_hour_required'];
			} else if (strtotime($post['finish_time']) - $start_time < 0) {
				$errors['finish_time'] = $bezlang['vald_valid_finish_hour_required'];
			} else {
				$data['finish_time'] = $post['finish_time'];
			}
			
		} else {
			$data['all_day_event'] = '1';
		}
		return $data;
	}
	
	public function add($post, $data=array())
	{
		global $errors;
		if ( 
			(!isset($data['issue']) && $this->helper->user_admin())
				||
			($this->helper->user_coordinator($data['issue']) &&
			$this->issue->opened($data['issue']) &&
			!$this->issue->is_proposal($data['issue']))
			) {
			
			$from_user = $this->validate($post);
			$plan = $this->validate_plan($post);
			$data = array_merge($data, $from_user, $plan);

			/*przy dodawaniu domyślnym statusem jest odwarty*/
			$taskso = new Taskstates();
			$data['state'] = $taskso->id('opened');
			$this->errinsert($data, 'tasks');
			$this->issue->update_last_mod($data['issue']);
			return $data;
		}
		$errors[] = 'Nie udało się dodać rekordu.';

		return array();
	}
	public function update($post, $data, $id) {
		throw new Exception('upgrade tasks using new model');
		
		//~ $task = $this->getone($id);

		//~ $cache = new Bezcache();
		//~ if ($this->can_modify($id)) {
			//~ $from_user = $this->validate($post);
			//~ $plan = $this->validate_plan($post);
			//~ $data = array_merge($data, $from_user, $plan);
			//~ $this->errupdate($data, 'tasks', $id);
			//~ $cache->task_toupdate($id);
			//~ //$this->issue->update_last_mod($task['issue']);


			//~ return $data;
		//~ }
		//~ return false;
	}
	public function update_state($state, $reason, $id) {
		throw new Exception('upgrade tasks using new model');
		
		//~ $cache = new Bezcache();
		//~ if ($this->can_modify($id) || $this->can_change_state($id)) {
			//~ $state = $this->val_state($state);
			//~ $reason = $this->val_reason($reason);
			//~ $data = array('state' => $state, 'reason' => $reason);
			
			//~ $data['close_date'] = time();
			
			//~ $this->errupdate($data, 'tasks', $id);
			//~ $cache->task_toupdate($id);
			//~ //$this->issue->update_last_mod($task['issue']);

			//~ return $data;
		//~ }
	}
	
	public function save_plan($post, $id) {
		if ($this->can_modify($id)) {
			$data = $this->validate_plan($post);
			$this->errupdate($data, 'tasks', $id);
		}
	}
	
	public function getone($id) {
		$id = (int) $id;
		$a = $this->fetch_assoc("SELECT
		tasks.id, tasks.task_cache, tasks.reason_cache, task,executor,state,cost,reason,tasks.reporter,tasks.date,
		close_date,tasks.issue,tasks.cause, causes.potential, tasks.cause,
		tasks.plan_date, tasks.all_day_event, tasks.start_time, tasks.finish_time,
		tasks.tasktype
		FROM tasks LEFT JOIN causes ON tasks.cause = causes.id WHERE tasks.id=$id");

		return $a[0];
	}
	public function any_open($issue) {
		$issue = (int)$issue;
		$a = $this->fetch_assoc("SELECT state FROM tasks WHERE issue=$issue");
		foreach ($a as $task) {
			if ($task['state'] == 0)
				return true;
		}
		return false;
	}
	public function any_task($issue) {
		$issue = (int)$issue;
		$a = $this->fetch_assoc("SELECT * FROM tasks WHERE issue=$issue");
		if (count($a) > 0)
			return true;
		return false;
	}
	public function get_by_days($days=7) {
		if (!$this->helper->user_viewer()) return false;
		
		$border_date = time() - $days*24*60*60;
		
		$res = $this->fetch_assoc("SELECT tasks.id, tasks.task_cache, tasks.reason_cache,
								(CASE	WHEN tasks.issue IS NULL THEN '3'
										WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
										WHEN causes.potential = 0 THEN '1'
										ELSE '2' END) AS naction,
			tasks.issue, tasks.task, tasks.date, tasks.executor, tasks.reason
			FROM tasks LEFT JOIN issues ON tasks.issue = issues.id LEFT JOIN causes ON tasks.cause = causes.id
			WHERE tasks.date > $border_date
			ORDER BY tasks.date DESC");

		$create = $this->sort_by_days($res, 'date');
		foreach ($create as $day => $issues)
			foreach ($issues as $ik => $issue)
				$create[$day][$ik]['class'] = 'task_opened';

		$res2 = $this->fetch_assoc("SELECT tasks.id, tasks.task_cache, tasks.reason_cache,
								(CASE	WHEN tasks.issue IS NULL THEN '3'
										WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
										WHEN causes.potential = 0 THEN '1'
										ELSE '2' END)  AS naction,
				tasks.issue, tasks.task, tasks.close_date, tasks.executor, tasks.reason
				FROM tasks LEFT JOIN issues ON tasks.issue = issues.id LEFT JOIN causes ON tasks.cause = causes.id
				WHERE tasks.close_date > $border_date 
				AND   tasks.state = 1 ORDER BY tasks.close_date DESC");
		$close = $this->sort_by_days($res2, 'close_date');
		foreach ($close as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$close[$day][$ik]['class'] = 'task_done';
				$close[$day][$ik]['date'] = $close[$day][$ik]['close_date'];
			}

		$res3 = $this->fetch_assoc("SELECT tasks.id, tasks.task_cache, tasks.reason_cache,
								(CASE	WHEN tasks.issue IS NULL THEN '3'
										WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
										WHEN causes.potential = 0 THEN '1'
										ELSE '2' END) AS naction,
				tasks.issue, tasks.task, tasks.close_date, tasks.executor, tasks.reason
				FROM tasks LEFT JOIN issues ON tasks.issue = issues.id LEFT JOIN causes ON tasks.cause = causes.id
				WHERE tasks.close_date > $border_date 
				AND   tasks.state = 2 ORDER BY tasks.close_date DESC");
		$rejected = $this->sort_by_days($res3, 'close_date');
		foreach ($rejected as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$rejected[$day][$ik]['class'] = 'task_rejected';
				$rejected[$day][$ik]['date'] = $rejected[$day][$ik]['close_date'];
			}

		return $this->helper->days_array_merge($create, $close, $rejected);
	}
	public function join($row, $keep=array()) {
		global $bezlang;
		$usro = new Users();
		$taskao = new Taskactions();
		$taskso = new Taskstates();
		$stato = new States();
		$taskto = new Tasktypes();
		//~ $cache = new Bezcache();
		
		$tasktypes = $taskto->get();

		$row['reporter'] = $usro->name($row['reporter']);
		$row['executor_nick'] = $row['executor'];
		$row['executor_email'] = $usro->email($row['executor']);
		$row['executor'] = $usro->name($row['executor']);
		$row['tasktype'] = $tasktypes[$row['tasktype']];

		if (isset($row[naction]))
			switch($row[naction]) {
				case 0: $row[action] = $bezlang['correction']; break;
				case 1: $row[action] = $bezlang['corrective_action']; break;
				case 2: $row[action] = $bezlang['preventive_action']; break;
				case 3: $row[action] = $bezlang['programme']; break;
			}
		else {
			if ($row['issue'] == NULL)
				$row[action] = $bezlang['programme'];
			elseif ($row[cause] == NULL)
				$row[action] = $bezlang['correction'];
			elseif ($row[potential] == 0)
				$row[action] = $bezlang['corrective_action'];
			else
				$row[action] = $bezlang['preventive_action'];
		}

		//$row['rejected'] = $row['state'] == $stato->rejected();
		$row['raw_state'] = $row['state'];
		$row['state'] = $taskso->name($row['state']);

		$row['raw_task'] = $row['task'];

		$wiki_text = array();
		if (!in_array('task', $keep) || !in_array('reason', $keep)) {
			$wiki_text['task'] = $row['task_cache'];
			$wiki_text['reason'] = $row['reason_cache'];
		}
		
		if (!in_array('task', $keep))
			$row['task'] = $wiki_text['task'];
		if (!in_array('reason', $keep))
			$row['reason'] = $wiki_text['reason'];
		
		if (isset($row['cause_text'])) {
			$row['cause_text'] = $this->helper->wiki_parse($row[cause_text]);
		}

		return $row;
	}

	public function get_clean($issue, $cause=-1) {
		$issue = (int) $issue;
		$wcause = '';
		if (is_null($cause))
			$wcause = " AND (tasks.cause = '' OR tasks.cause is NULL)";
		else if ($cause > -1)
			$wcause = " AND tasks.cause=$cause";


		$q = "SELECT
				tasks.id, tasks.task_cache, tasks.reason_cache, task,executor,state,cost,reason,tasks.reporter,tasks.date,
				close_date,tasks.issue,tasks.cause, causes.potential, causes.cause as cause_text, causes.id as cause_id
				FROM tasks LEFT JOIN causes ON tasks.cause = causes.id WHERE tasks.issue=$issue $wcause";
		return $this->fetch_assoc($q);
	}
	
	public function get_corrections_ids($issue) {
		$q = "SELECT tasks.id FROM tasks WHERE tasks.issue=$issue
											AND (tasks.cause = '' OR tasks.cause is NULL)";
		return $this->fetch_assoc($q);
	}

	public function get_preventive($issue) {
		$issue = (int) $issue;
		$q = "SELECT tasks.id, tasks.task_cache, tasks.reason_cache, causes.id as cid, tasks.task, tasks.executor, tasks.state, tasks.date, tasks.close_date, tasks.reason, tasks.issue
				FROM tasks LEFT JOIN causes ON tasks.cause = causes.id
				WHERE tasks.issue=$issue AND causes.potential = 1 AND tasks.state != 2";
		$rows = $this->fetch_assoc($q);

		$rootco = new Rootcauses();
		//~ $cache = new Bezcache();

		$bycause = array();
		foreach ($rows as &$row) {
			/*$row[cause] = $this->helper->wiki_parse($row[cause]);
			$row['rootcause'] = $rootco->name($row['rootcause']);*/

			$usro = new Users();
			$row['executor'] = $usro->name($row['executor']);
			$taskso = new Taskstates();
			$row['state'] = $taskso->name($row['state']);

			$wiki_text = $cache->get_task($row['id']);
			$row['task'] = $row['task_cache'];
			$row['reason'] = $wiki_text['reason_cache'];

			if (!isset($bycause[$row[cid]]))
				$bycause[$row[cid]] = array();
			$bycause[$row[cid]][] = $row;
		}

		return $bycause;

	}
	public function get($issue, $cause=-1) {
		$a = $this->get_clean($issue, $cause);
		foreach ($a as &$row)
			$row = $this->join($row);

		return $a;
	}
	public function get_stats() {
		$all = $this->fetch_assoc("SELECT COUNT(*) AS tasks_all FROM tasks;");
		$opened = $this->fetch_assoc("SELECT COUNT(*) as tasks_opened FROM tasks WHERE state=0;");

		$stats = array();
		$stats['all'] = $all[0]['tasks_all'];
		$stats['opened'] = $opened[0]['tasks_opened'];
		return $stats;
	}

	public function get_by_8d($issue) {
		$issue = (int)$issue;
		$a = $this->fetch_assoc("SELECT tasks.id, tasks.task_cache, tasks.reason_cache, tasks.state,
									(CASE	WHEN tasks.issue IS NULL THEN '3'
											WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
											WHEN causes.potential = 0 THEN '1'
											ELSE '2' END) AS naction,
		tasks.executor, tasks.cost, tasks.date, tasks.close_date, tasks.issue, tasks.close_date
		FROM tasks LEFT JOIN issues ON tasks.issue = issues.id 
		LEFT JOIN causes ON tasks.cause = causes.id
		WHERE tasks.state != 2 AND tasks.issue = $issue ORDER BY tasks.plan_date, tasks.start_time DESC");
		$b = array();
		$taskao = new Taskactions();
		foreach ($a as $row) {
			$k = $taskao->map_8d($row['naction']);
			if ( !isset($b[$k]) )
				$b[$k] = array();
			$b[$k][] = $this->join($row);
		}
		ksort($b);
		return $b;
	}
	public function get_total_cost($issue) {
		$issue = (int) $issue;
		$a = $this->fetch_assoc("SELECT SUM(cost) AS 'cost_total' FROM tasks WHERE issue=$issue GROUP BY issue");
		return $a[0]['cost_total'];
	}

	public function get_executors() {
		$all = $this->fetch_assoc("SELECT executor FROM tasks GROUP BY executor");
		$execs = array();

		$usro = new Users();
		foreach ($all as $row)
			$execs[$row['executor']] = $usro->name($row['executor']);

		asort($execs);
		return $execs;
	}
	
	public function get_year($iso_date) {
		$tmp = explode('-', $iso_date);
		return (int)$tmp[0];
	}
	
	public function get_plan_years() {
		$all = $this->fetch_assoc("SELECT MIN(plan_date) as min_date, MAX(plan_date) as max_date FROM tasks WHERE plan_date != ''");

		if ($all['min_date'] == NULL)
			return array(date('Y'));
			
		$oldest = $this->get_year($all[0]['min_date']);
		$newest = $this->get_year($all[0]['max_date']);

		$years = array();
		for ($year = $oldest; $year <= $newest; $year++)
			$years[] = $year;

		return $years;
	}

	public function get_years() {
		$close = $this->fetch_assoc("SELECT MIN(close_date) AS min, MAX(close_date) AS max FROM tasks");
		$plan = $this->fetch_assoc("SELECT MIN(plan_date) AS min, MAX(plan_date) AS max FROM tasks");
		
		if ($close[0]['min'] == NULL && $close[0]['max'] == NULL &&
			$plan[0]['min'] == NULL && $plan[0]['max'] == NULL)
			return array(date('Y'));
		if ($close[0]['min'] == NULL && $close[0]['max'] == NULL) {
			$oldest = (int)date('Y', strtotime($plan[0]['min']));
			$newest = (int)date('Y', strtotime($plan[0]['max']));
		} else {
			$plan_oldest = (int)date('Y', strtotime($plan[0]['min']));
			$plan_newest = (int)date('Y', strtotime($plan[0]['max']));
			
			$close_oldest = (int)date('Y', $close[0]['min']);
			$close_newest = (int)date('Y', $close[0]['max']);
			
			$oldest = min($plan_oldest, $close_oldest);
			$newest = max($plan_newest, $close_newest, (int)date('Y'));
		}
		
		$years = array();
		for ($year = $oldest; $year <= $newest; $year++)
			$years[] = $year;

		return $years;
	}

	public function validate_filters($filters) {

		$data = array('issue' => '-all', 'naction' => '-all', 'taskstate' => '-all', 'executor' => '-all', 'year' => '-all', 'task' => '', 'reason' => '');
		
		if (isset($filters['task']))
			$data['task'] = str_replace(array(':', "'"), '', $filters['task']);
			
		if (isset($filters['reason']))
			$data['reason'] = str_replace(array(':', "'"), '', $filters['reason']);

		if (isset($filters['issue'])) {
			$isso = new Issues();
			if ($filters['issue'] == '-all' || in_array($filters['issue'], $isso->get_ids()))
				$data['issue'] = $filters['issue'];
		}

		if (isset($filters['action'])) {
			$taskao = new Taskactions();
			if ($filters['action'] == '-all' || in_array($filters['action'], array_keys($taskao->get())))
				$data['action'] = $filters['action'];
		}
	
		//!!! taskstate changing to state
		if (isset($filters['taskstate'])) {
			$taskso = new Taskstates();
			if ($filters['taskstate'] == '-all' ||
				$filters['taskstate'] == '-outdated' ||
				array_key_exists($filters['taskstate'], array_keys($taskso->get()))) {
					$data['taskstate'] = $filters['taskstate'];
				}
		}


		if (isset($filters['executor'])) {
			$usro = new Users();
			$excs = $usro->nicks();
			
			if ($filters['executor'] == '-all')
				$data['executor'] = $filters['executor'];
			else if ($filters['executor'][0] == '@') {
				$groups = $usro->groups();
				$group = substr($filters['executor'], 1);
				if (in_array($group, $groups))
					$data['executor'] = $filters['executor'];
			} else if (in_array($filters['executor'], $excs)) {
				$data['executor'] = $filters['executor'];
			}
		}

		if (isset($filters['year'])) {
			if ($data['taskstate'] == '0')
				$years = $this->get_plan_years();
			else
				$years = $this->get_years();
			if ($filters['year'] == '-all' || in_array($filters['year'], $years))
				$data['year'] = $filters['year'];
		}
		
		if (isset($filters['tasktype'])) {
			if ($filters['tasktype'] == '-all' ||
				$filters['tasktype'] == '-none' ||
				ctype_digit($filters['tasktype']))
					$data['tasktype'] = $filters['tasktype'];
		}
		
		if (isset($filters['month'])) {
			if ($filters['month'] == '-all' ||
				(ctype_digit($filters['month'])
				&& $filters['month'] >= 1 && $filters['month'] <= 12))
					$data['month'] = $filters['month'];
		} else {
			$data['month'] = '-all';
		}


		if (isset($filters['date_type'])) {
			if ($filters['date_type'] == 'plan' ||
				$filters['date_type'] == 'open' ||
				$filters['date_type'] == 'closed')
				$data['date_type'] = $filters['date_type'];
		}

		return $data;
	}

	public function get_filtered($filters, $keep=array()) {
		$vfilters = $this->validate_filters($filters);

		$year = $vfilters['year'];
		unset($vfilters['year']);
		
		$month = $vfilters['month'];
		unset($vfilters['month']);
		
		
		$where = array();

		if (isset($vfilters['action'])) {
			$vfilters['naction'] = $vfilters['action'];
			unset($vfilters['action']);
		}
		
		$task = $vfilters['task'];
		unset($vfilters['task']);
		if ($task != '') {
            $task = preg_replace('/\s/', '%', $task);
			$where[] = "tasks.task LIKE '%".$this->escape($task)."%'";
		}
		
		
		$reason = $vfilters['reason'];
		unset($vfilters['reason']);
		if ($reason != '') {
            $reason = preg_replace('/\s/', '%', $reason);
			$where[] = "tasks.reason LIKE '%".$this->escape($reason)."%'";
		}
		
		if ($vfilters['executor'][0] == '@') {
			$group = substr($vfilters['executor'], 1);
			unset($vfilters['executor']);
			$usro = new Users();
			$users = $usro->users_of_group($group);
			
			$usr_where = array();
			foreach($users as $user) {
				$usr_where[] = "tasks.executor = '".$this->escape($user)."'";
			}
			$where[] = "(".implode(" OR ", $usr_where).")";
		}
		
		$date_type = $vfilters['date_type'];
		unset($vfilters['date_type']);
		
		if ($vfilters['taskstate'] == '-outdated') {
			$vfilters['taskstate'] = '0';
			$where[] = "tasks.plan_date < date('now')";
		}
		
		foreach ($vfilters as $name => $value) {
			if ($name == 'tasktype' && $value == '-none')
				$where[] = "(tasks.tasktype = '' OR tasks.tasktype ISNULL)";
			else if ($value != '-all') {
				if ($name == 'naction')
					$where[] = "$name = '".$this->escape($value)."'";
				elseif ($name == 'taskstate')
					$where[] = "tasks.state = '".$this->escape($value)."'";
				else
					$where[] = "tasks.$name = '".$this->escape($value)."'";
			}
		}

		if ($year != '-all') {
			if ($date_type == 'plan') {
				$date_field = 'tasks.plan_date';
			} else if ($date_type == 'open') {
				$date_field = 'tasks.date';
			} else {
				$date_field = 'tasks.close_date';
			}
				
			
			if ($month != '-all') {
				$month = (int)$month;
				$year = (int)$year;
				$start_date = date('Y-m-d', mktime(0,0,0,$month,1,$year));
				$finish_date = date('Y-m-d', mktime(0,0,0,$month,
								cal_days_in_month(CAL_GREGORIAN, $month, $year),$year));
								
				if ($date_field == 'tasks.plan_date') {
					$where[] = "(($date_field BETWEEN '$start_date' AND '$finish_date')
								OR (tasks.state = 0 AND (tasks.plan_date = '' OR tasks.plan_date ISNULL)))";
				} else {
					$where[] = "$date_field >= ".mktime(0,0,0,$month,1,$year);
					$where[] = "$date_field <= ".mktime(0,0,0,$month,cal_days_in_month(CAL_GREGORIAN, $month, $year),$year);
				}
			} else {
				$year = (int)$year;
				$start_date = date('Y-m-d', mktime(0,0,0,1,1,$year));
				$finish_date = date('Y-m-d', mktime(0,0,0,12,31,$year));
				if ($date_field == 'tasks.plan_date') {
					$where[] = "(($date_field BETWEEN '$start_date' AND '$finish_date')
								OR (tasks.state = 0 AND (tasks.plan_date = '' OR tasks.plan_date ISNULL)))";
				} else {
					$where[] = "$date_field >= ".mktime(0,0,0,1,1,$year);
					$where[] = "$date_field < ".mktime(0,0,0,1,1,$year+1);
				}
			}
		}

		$where_q = '';
		if (count($where) > 0)
			$where_q = 'WHERE '.implode(' AND ', $where);

		$a = $this->fetch_assoc("SELECT tasks.id,tasks.state, tasks.task_cache, tasks.reason_cache,
									(CASE	WHEN tasks.issue IS NULL THEN '3'
											WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
											WHEN commcauses.type = 1 THEN '1'
											ELSE '2' END)AS naction,
									(CASE	WHEN tasks.state > 0 THEN '3'
											WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
											WHEN tasks.plan_date >= date('now') THEN '1'
											ELSE '0' END) as priority,
		tasks.executor, tasks.cost, tasks.date, tasks.close_date, tasks.issue, tasks.close_date,
		tasks.tasktype, tasks.task, tasks.reason, tasks.plan_date, tasks.all_day_event,
		tasks.start_time, tasks.finish_time
		FROM tasks LEFT JOIN issues ON tasks.issue = issues.id 
		LEFT JOIN commcauses ON tasks.cause = commcauses.id
		$where_q ORDER BY priority, tasks.plan_date, tasks.start_time");
		foreach ($a as &$row)
			$row = $this->join($row, $keep);
		return $a;
	}
	
	//cron_get_coming_tasks, cron_get_outdated_tasks
	public function cron_get_outdated_tasks() {
		global $bezlang;
		$a = $this->fetch_assoc("SELECT tasks.id, tasks.task_cache, tasks.reason_cache, tasks.issue, tasks.executor, tasks.date, tasks.plan_date, tasks.start_time, tasks.finish_time, tasktypes.pl as tasktype, all_day_event,
									(CASE	WHEN tasks.issue IS NULL THEN '3'
											WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
											WHEN commcauses.type = 1 THEN '1'
											ELSE '2' END) AS naction,
									(CASE	WHEN tasks.state > 0 THEN '3'
											WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
											WHEN tasks.plan_date >= date('now') THEN '1'
											ELSE '0' END) AS priority
								FROM tasks LEFT JOIN issues ON tasks.issue = issues.id 
								LEFT JOIN commcauses ON tasks.cause = commcauses.id
								LEFT JOIN tasktypes ON tasks.tasktype = tasktypes.id
								WHERE priority = '0'");
		foreach ($a as &$row)						
			switch($row['naction']) {
				case 0: $row['action'] = $bezlang['correction']; break;
				case 1: $row['action'] = $bezlang['corrective_action']; break;
				case 2: $row['action'] = $bezlang['preventive_action']; break;
				case 3: $row['action'] = $bezlang['programme']; break;
			}
		return $a;
	}
	
	//one month
	public function cron_get_coming_tasks() {
		global $bezlang;
		$a = $this->fetch_assoc("SELECT tasks.id, tasks.task_cache, tasks.reason_cache, tasks.issue, tasks.executor, tasks.date, tasks.plan_date, tasks.start_time, tasks.finish_time, tasktypes.pl as tasktype, all_day_event,
									(CASE	WHEN tasks.issue IS NULL THEN '3'
											WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
											WHEN commcauses.type = 1 THEN '1'
											ELSE '2' END) AS naction,
									(CASE	WHEN tasks.state > 0 THEN '3'
											WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
											WHEN tasks.plan_date >= date('now') THEN '1'
											ELSE '0' END) AS priority
								FROM tasks LEFT JOIN issues ON tasks.issue = issues.id 
								LEFT JOIN commcauses ON tasks.cause = commcauses.id
								LEFT JOIN tasktypes ON tasks.tasktype = tasktypes.id
								WHERE priority = '1'");
		foreach ($a as &$row)						
			switch($row['naction']) {
				case 0: $row['action'] = $bezlang['correction']; break;
				case 1: $row['action'] = $bezlang['corrective_action']; break;
				case 2: $row['action'] = $bezlang['preventive_action']; break;
				case 3: $row['action'] = $bezlang['programme']; break;
			}
		return $a;
	}
	
	public function get_type($tid) {
		$tid = (int)$tid;
		$a = $this->fetch_assoc("SELECT tasktype FROM tasks WHERE id=$tid");
		return $a[0]['tasktype'];
	}
	public function get_state($tid) {
		$tid = (int)$tid;
		$a = $this->fetch_assoc("SELECT state FROM tasks WHERE id=$tid");
		return $a[0]['state'];
	}
}

