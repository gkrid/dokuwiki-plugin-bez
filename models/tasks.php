<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";
include_once DOKU_PLUGIN."bez/models/taskstates.php";

class Tasks extends Connect {
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
	issue INT(11) NOT NULL,

	PRIMARY KEY (id)
)
EOM;
	$this->errquery($q);
	}
	public function can_modify($task_id) {
		$task = $this->getone($task_id);

		if ($task)
			if ($this->helper->user_coordinator($task['issue']) || $this->helper->user_admin()) 
				return true;

		return false;
	}
	public function can_change_state($task_id) {
		global $INFO;
		$task = $this->getone($task_id);
		if ($task['executor'] == $INFO['client'])
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
		if ($this->helper->user_coordinator($data['issue'])) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);

			/*przy dodawaniu domyślnym statusem jest odwarty*/
			$taskso = new Taskstates();
			$data['state'] = $taskso->id('opened');

			$this->errinsert($data, 'tasks');
		}
	}
	public function update($post, $data, $id) {
		if ($this->can_modify($id)) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$this->errupdate($data, 'tasks', $id);
		} elseif ($this->can_change_state($id)) {
			$state = $this->val_state($post['state']);
			$reason = $this->val_reason($post['reason']);
			$this->errupdate(array('state' => $state, 'reason' => $reason), 'tasks', $id);
		}
	}
	public function getone($id) {
		$id = (int) $id;
		$a = $this->fetch_assoc("SELECT * FROM tasks WHERE id=$id");

		return $a[0];
	}
	public function get($issue) {
		$issue = (int) $issue;

		$a = $this->fetch_assoc("SELECT * FROM tasks WHERE issue=$issue");

		$usro = new Users();
		$taskao= new Taskactions();
		$taskso = new Taskstates();
		foreach ($a as &$row) {
			$row['reporter'] = $usro->name($row['reporter']);
			$row['executor_nick'] = $row['executor'];
			$row['executor'] = $usro->name($row['executor']);
			$row['action'] = $taskao->name($row['action']);
			$row['state'] = $taskso->name($row['state']);
		}

		return $a;
	}
}

