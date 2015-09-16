<?php
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";
include_once DOKU_PLUGIN."bez/models/taskstates.php";
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
				executor TEXT NOT NULL,
				action INTEGER NOT NULL,
				cost INTEGER NULL,
				reason TEXT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				close_date INTEGER NULL,
				issue INTEGER NOT NULL,
				cause INTEGER NULL
				)";
		$this->errquery($q);

		$schema = $this->fetch_assoc("PRAGMA table_info(tasks)");
		$cause_exists = false;
		foreach ($schema as $column) {
			if ($column['name'] == 'cause') {
				$cause_exists = true;
				break;
			}
		}
		if ($cause_exists == false) 
			$this->errquery("ALTER TABLE tasks ADD COLUMN cause INTEGER NULL");
	}
	public function can_modify($task_id) {
		$task = $this->getone($task_id);

		if ($task && $this->issue->opened($task['issue']))
			if ($this->helper->user_coordinator($task['issue']) || $this->helper->user_admin()) 
				return true;

		return false;
	}
	public function can_change_state($task_id) {
		global $INFO;
		$task = $this->getone($task_id);
		if ($task['executor'] == $INFO['client'] && $this->issue->opened($task['issue']))
			return true;

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

		$taskao = new Taskactions();
		if (array_key_exists('action', $post)) {
			if ( ! array_key_exists((int)$post['action'], $taskao->get())) {
				$errors['action'] = $bezlang['vald_action_required'];
			} 
			$data['action'] = (int) $post['action'];
		} else
			$data['action'] = $taskao->id('correction');

		//cost is not required
		if ($post['cost'] != '') {
			$cost = trim($post['cost']);
			if ( ! ctype_digit($cost)) {
				$errors['cost'] = $bezlang['vald_cost_wrong_format'];
			} elseif ( (int)$post['cost'] > $cost_max) {
				$errors['cost'] = str_replace('%d', $cost_max, $bezlang['vald_cost_too_big']);
			}
			$data['cost'] = (int) $post['cost'];
		}
		
		/*zmienamy status tylko w przypadku edycji*/
		if (array_key_exists('state', $post)) 
			$data['state'] = $this->val_state($post['state']);

		if (array_key_exists('reason', $post))
			$data['reason'] = $this->val_reason($post['reason']);

		if (isset($_POST['cause']))
			$data['cause'] = (int)$_POST['cause'];

		return $data;
	}
	public function val_state($state) {
		$taskso = new Taskstates();
		if ( ! array_key_exists((int)$state, $taskso->get())) {
			$errors['state'] = $bezlang['vald_state_required'];
			return -1;
		} 
		return (int) $state;
	}
	public function val_reason($reason) {
		$reason_max = 65000;

		$reason = trim($reason);
		if (strlen($reason) == 0) 
			$errors['reason'] = $bezlang['vald_content_required'];
		else if (strlen($resaon) > $reason_max)
			$errors['reason'] = str_replace('%d', $task_max, $bezlang['vald_content_too_long']);

		return $reason;
	}
	public function add($post, $data=array())
	{
		if ($this->helper->user_coordinator($data['issue']) &&
			$this->issue->opened($data['issue']) &&
			!$this->issue->is_proposal($data['issue'])) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);

			/*przy dodawaniu domyślnym statusem jest odwarty*/
			$taskso = new Taskstates();
			$data['state'] = $taskso->id('opened');

			$this->errinsert($data, 'tasks');
			$this->issue->update_last_mod($data['issue']);
			return $data;
		}
		return false;
	}
	public function update($post, $data, $id) {
		$task = $this->getone($id);

		if ($this->can_modify($id)) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$data['close_date'] = time();
			$this->errupdate($data, 'tasks', $id);
			$this->issue->update_last_mod($task['issue']);

			$cache = new Bezcache();
			$cache->task_toupdate($id);

			return $data;
		} elseif ($this->can_change_state($id)) {
			$state = $this->val_state($post['state']);
			$reason = $this->val_reason($post['reason']);
			$data = array('state' => $state, 'reason' => $reason, 'close_date' => time());
			$this->errupdate($data, 'tasks', $id);
			$this->issue->update_last_mod($task['issue']);

			return $data;
		}
		return false;
	}
	public function getone($id) {
		$id = (int) $id;
		$a = $this->fetch_assoc("SELECT * FROM tasks WHERE id=$id");

		return $a[0];
	}
	public function any_open($issue) {
		$issue = (int)$issue;
		$a = $this->fetch_assoc("SELECT * FROM tasks WHERE issue=$issue");
		$stato = new States();
		foreach ($a as $task) {
			if ($task['state'] == $stato->open())
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
	public function get_by_days() {
		if (!$this->helper->user_viewer()) return false;

		$res = $this->fetch_assoc("SELECT tasks.id, tasks.issue, tasks.task, tasks.date, tasks.executor, tasks.reason, issues.priority FROM tasks JOIN issues ON tasks.issue = issues.id ORDER BY tasks.date DESC");
		$create = $this->sort_by_days($res, 'date');
		foreach ($create as $day => $issues)
			foreach ($issues as $ik => $issue)
				$create[$day][$ik]['class'] = 'task_opened';

		$res2 = $this->fetch_assoc("SELECT tasks.id, tasks.issue, tasks.task, tasks.close_date, tasks.executor, tasks.reason, issues.priority FROM tasks JOIN issues ON tasks.issue = issues.id WHERE tasks.state = 1 ORDER BY tasks.close_date DESC");
		$close = $this->sort_by_days($res2, 'close_date');
		foreach ($close as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$close[$day][$ik]['class'] = 'task_done';
				$close[$day][$ik]['date'] = $close[$day][$ik]['close_date'];
			}

		$res3 = $this->fetch_assoc("SELECT tasks.id, tasks.issue, tasks.task, tasks.close_date, tasks.executor, tasks.reason, issues.priority FROM tasks JOIN issues ON tasks.issue = issues.id WHERE tasks.state = 2 ORDER BY tasks.close_date DESC");
		$rejected = $this->sort_by_days($res3, 'close_date');
		foreach ($rejected as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$rejected[$day][$ik]['class'] = 'task_rejected';
				$rejected[$day][$ik]['date'] = $rejected[$day][$ik]['close_date'];
			}

		return $this->helper->days_array_merge($create, $close, $rejected);
	}
	public function join($row) {
		$usro = new Users();
		$taskao = new Taskactions();
		$taskso = new Taskstates();
		$stato = new States();

		$cache = new Bezcache();

		$row['reporter'] = $usro->name($row['reporter']);
		$row['executor_nick'] = $row['executor'];
		$row['executor_email'] = $usro->email($row['executor']);
		$row['executor'] = $usro->name($row['executor']);
		$row['action'] = $taskao->name($row['action']);
		//$row['rejected'] = $row['state'] == $stato->rejected();
		$row['state'] = $taskso->name($row['state']);

		$wiki_text = $cache->get_task($row['id']);
		$row['task'] = $wiki_text['task'];
		$row['reason'] = $wiki_text['reason'];

		return $row;
	}

	public function get_clean($issue, $cause=-1) {
		$issue = (int) $issue;
		$wcause = '';
		if (is_null($cause))
			$wcause = " AND cause is NULL";
		else if ($cause > -1)
			$wcause = " AND cause=$cause";


		$q = "SELECT * FROM tasks WHERE issue=$issue $wcause";
		return $this->fetch_assoc($q);
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
		$a = $this->fetch_assoc("SELECT * FROM tasks WHERE issue=$issue AND state != 2");
		$b = array();
		$taskao = new Taskactions();
		foreach ($a as $row) {
			$k = $taskao->map_8d($row['action']);
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

	public function get_years() {
		$all = $this->fetch_assoc("SELECT date FROM tasks ORDER BY date LIMIT 1");
		if (count($all) == 0)
			return array();
		$oldest = date('Y', $all[0]['date']);
		
		$years = array();
		for ($year = $oldest; $year <= (int)date('Y'); $year++)
			$years[] = $year;

		return $years;
	}

	public function validate_filters($filters) {

		$data = array('issue' => '-all', 'action' => '-all', 'state' => '-all', 'executor' => '-all', 'year' => '-all');

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

		if (isset($filters['state'])) {
			$taskso = new Taskstates();
			if ($filters['state'] == '-all' || array_key_exists($filters['state'], array_keys($taskso->get())))
				$data['state'] = $filters['state'];
		}


		if (isset($filters['executor'])) {
			//$excs = array_keys($this->get_executors());
			$usro = new Users();
			$excs = $usro->nicks();
			if ($filters['executor'] == '-all' || in_array($filters['executor'], $excs))
				$data['executor'] = $filters['executor'];
		}

		if (isset($filters['year'])) {
			$years = $this->get_years();
			if ($filters['year'] == '-all' || in_array($filters['year'], $years))
				$data['year'] = $filters['year'];
		}

		return $data;
	}

	public function get_filtered($filters) {
		$vfilters = $this->validate_filters($filters);

		$year = $vfilters['year'];
		unset($vfilters['year']);

		$where = array();

		foreach ($vfilters as $name => $value)
			if ($value != '-all')
				$where[] = "tasks.$name = '".$this->escape($value)."'";

		if ($year != '-all') {
			$state = $vfilters['state'];
			if ($state == '-all' || $state == '0')
				$date_field = 'tasks.date';
			else
				$date_field = 'tasks.close_date';

			$year = (int)$year;
			$where[] = "$date_field >= ".mktime(0,0,0,1,1,$year);
			$where[] = "$date_field < ".mktime(0,0,0,1,1,$year+1);
		}

		$where_q = '';
		if (count($where) > 0)
			$where_q = 'WHERE '.implode(' AND ', $where);

		$a = $this->fetch_assoc("SELECT tasks.id,tasks.state, tasks.action, tasks.executor, tasks.cost, tasks.date, tasks.close_date, tasks.issue, tasks.close_date, issues.priority
		FROM tasks JOIN issues ON tasks.issue = issues.id $where_q ORDER BY priority DESC, tasks.date DESC");
		foreach ($a as &$row)
			$row = $this->join($row);
		return $a;
	}

	public function cron_get_unsolved() {
		$a = $this->fetch_assoc("SELECT id, issue, executor FROM tasks 
								WHERE state=0");
		return $a;
	}
}

