<?php
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";
include_once DOKU_PLUGIN."bez/models/taskstates.php";
include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/event.php";

class Tasks extends Event {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = <<<EOM
CREATE TABLE IF NOT EXISTS tasks (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	task TEXT NOT NULL,
	state INT(11) NOT NULL,
	executor CHAR(100) NOT NULL,
	action INT(11) NOT NULL,
	cost INT(11) NULL,
	reason TEXT NOT NULL,
	reporter CHAR(100) NOT NULL,
	date INT(11) NOT NULL,
	close_date INT(11) NULL,
	issue INT(11) NOT NULL,

	PRIMARY KEY (id)
)
EOM;
	$this->errquery($q);
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
		if ($this->helper->user_coordinator($data['issue']) && $this->issue->opened($data['issue'])) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);

			/*przy dodawaniu domyślnym statusem jest odwarty*/
			$taskso = new Taskstates();
			$data['state'] = $taskso->id('opened');

			$this->errinsert($data, 'tasks');
			$this->issue->update_last_mod($data['issue']);
		}
	}
	public function update($post, $data, $id) {
		$task = $this->getone($id);

		if ($this->can_modify($id)) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$data['close_date'] = time();
			$this->errupdate($data, 'tasks', $id);
			$this->issue->update_last_mod($task['issue']);
		} elseif ($this->can_change_state($id)) {
			$state = $this->val_state($post['state']);
			$reason = $this->val_reason($post['reason']);
			$data = array('state' => $state, 'reason' => $reason, 'close_date' => time());
			$this->errupdate($data, 'tasks', $id);
			$this->issue->update_last_mod($task['issue']);
		}
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
	public function get_by_days() {
		if (!$this->helper->user_viewer()) return false;

		$res = $this->fetch_assoc("SELECT tasks.issue, tasks.task, tasks.date, tasks.executor, tasks.reason, issues.priority FROM tasks JOIN issues ON tasks.issue = issues.id ORDER BY tasks.date DESC");
		$create = $this->sort_by_days($res, 'date');
		foreach ($create as $day => $issues)
			foreach ($issues as $ik => $issue)
				$create[$day][$ik]['class'] = 'task_opened';

		$res2 = $this->fetch_assoc("SELECT tasks.issue, tasks.task, tasks.close_date, tasks.executor, tasks.reason, issues.priority FROM tasks JOIN issues ON tasks.issue = issues.id WHERE tasks.state = 1 ORDER BY tasks.close_date DESC");
		$close = $this->sort_by_days($res2, 'close_date');
		foreach ($close as $day => $issues)
			foreach ($issues as $ik => $issue) {
				$close[$day][$ik]['class'] = 'task_done';
				$close[$day][$ik]['date'] = $close[$day][$ik]['close_date'];
			}

		$res3 = $this->fetch_assoc("SELECT tasks.issue, tasks.task, tasks.close_date, tasks.executor, tasks.reason, issues.priority FROM tasks JOIN issues ON tasks.issue = issues.id WHERE tasks.state = 2 ORDER BY tasks.close_date DESC");
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

		$row['reporter'] = $usro->name($row['reporter']);
		$row['executor_nick'] = $row['executor'];
		$row['executor_email'] = $usro->email($row['executor']);
		$row['executor'] = $usro->name($row['executor']);
		$row['action'] = $taskao->name($row['action']);
		$row['rejected'] = $row['state'] == $stato->rejected();
		$row['state'] = $taskso->name($row['state']);

		return $row;
	}
	public function get_clean($issue) {
		$issue = (int) $issue;
		return $this->fetch_assoc("SELECT * FROM tasks WHERE issue=$issue");
	}
	public function get($issue) {
		$a = $this->get_clean($issue);
		foreach ($a as &$row)
			$row = $this->join($row);

		return $a;
	}
	public function get_close_task() {
		global $INFO;
		$executor = $INFO['client'];
		$a = $this->fetch_assoc("SELECT tasks.id, tasks.task, tasks.state, tasks.action, tasks.cost, tasks.date, tasks.issue, issues.priority
		FROM tasks JOIN issues ON tasks.issue = issues.id WHERE executor='$executor' AND tasks.state=0 ORDER BY priority DESC, tasks.date DESC");
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
		$a = $this->get_clean($issue);
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
		$all = $this->fetch_assoc("SELECT FROM_UNIXTIME(date, '%Y') AS year FROM tasks GROUP BY year ORDER BY year DESC");
		$years = array();
		foreach ($all as $row)
			$years[] = $row['year'];
		return $years;
	}

	public function validate_filters($filters) {

		$taskao = new Taskactions();
		if ($filters['action'] == '-all' || in_array($filters['action'], array_keys($taskao->get())))
			$data['action'] = $filters['action'];
		else
			$data['action'] = '-all';	

		$taskso = new Taskstates();
		if ($filters['state'] == '-all' || array_key_exists($filters['state'], array_keys($taskso->get())))
			$data['state'] = $filters['state'];
		else
			$data['state'] = '-all';


		$excs = array_keys($this->get_executors());
		if ($filters['executor'] == '-all' || in_array($filters['executor'], $excs))
			$data['executor'] = $filters['executor'];
		else
			$data['executor'] = '-all';	

		$years = $this->get_years();
		if ($filters['year'] == '-all' || in_array($filters['year'], $years))
			$data['year'] = $filters['year'];
		else
			$data['year'] = '-all';

		return $data;
	}

	public function get_filtered($filters) {
		$vfilters = $this->validate_filters($filters);

		$year = $vfilters['year'];
		unset($vfilters['year']);

		$where = array();

		foreach ($vfilters as $name => $value)
			if ($value != '-all')
				$where[] = "tasks.$name = '".$this->db->real_escape_string($value)."'";

		if ($year != '-all') {
			$year = (int)$year;
			$where[] = 'tasks.date >= '.mktime(0,0,0,1,1,$year);
			$where[] = 'tasks.date < '.mktime(0,0,0,1,1,$year+1);
		}

		$where_q = '';
		if (count($where) > 0)
			$where_q = 'WHERE '.implode(' AND ', $where);

		$a = $this->fetch_assoc("SELECT tasks.id,tasks.state, tasks.action, tasks.executor, tasks.cost, tasks.date, tasks.close_date, tasks.issue, tasks.close_date, issues.priority
		FROM tasks JOIN issues ON tasks.issue = issues.id $where_q ORDER BY priority DESC, date DESC");
		foreach ($a as &$row)
			$row = $this->join($row);
		return $a;
	}
}

