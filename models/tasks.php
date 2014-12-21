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
	cost DOUBLE NULL,
	reporter CHAR(100) NOT NULL,
	date INT(11) NOT NULL,
	issue INT(11) NOT NULL,

	PRIMARY KEY (id)
)
EOM;
	$this->errquery($q);
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
		if ( ! array_key_exists((int)$post['action'], $taskao->get())) {
			$errors['action'] = $bezlang['vald_action_required'];
		} 
		$data['action'] = (int) $post['action'];

		//cost is not required
		if ($post['cost'] != '') {
			//remove not nessesery chars
			$locale = localeconv();
			$separators = array(' ', $locale['thousands_sep']);	
			$fract_sep = $locale['decimal_point'];

			$cost = str_replace($separators, '', $post['cost']);
			$cost_ex = explode($fract_sep, $cost);

			if (count($cost_ex) > 2 || ! ctype_digit($cost_ex[0])) {
				$errors['cost'] = $bezlang['vald_cost_wrong_format'];
			} elseif (isset($cost_ex[1]) && (strlen($cost_ex[1]) > 2 || ! ctype_digit($cost_ex[1]))) {
				$errors['cost'] = $bezlang['vald_cost_wrong_format'];
			} elseif ( (double)$post['cost'] > $cost_max) {
				$errors['cost'] = str_replace('%d', $cost_max, $bezlang['vald_cost_too_big']);
			}
			$data['cost'] = (double) $post['cost'];
		}

		return $data;
	}
	public function add($post, $data=array())
	{
		$from_user = $this->validate($post);
		$data = array_merge($data, $from_user);

		/*przy dodawaniu domyślnym statusem jest odwarty*/
		$taskso = new Taskstates();
		$data['state'] = $taskso->id('opened');

		$this->errinsert($data, 'tasks');
	}
	public function update($post, $data, $id) {
		$from_user = $this->validate($post);
		$data = array_merge($data, $from_user);

		$this->errupdate($data, 'tasks', $id);
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
			$row['executor'] = $usro->name($row['executor']);
			$row['action'] = $taskao->name($row['action']);
			$row['state'] = $taskso->name($row['state']);
		}

		return $a;
	}
}

