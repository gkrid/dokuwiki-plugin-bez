<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/rootcauses.php";

class Causes extends Connect {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = <<<EOM
CREATE TABLE IF NOT EXISTS causes (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	cause TEXT NOT NULL,
	rootcause INT(11) NOT NULL,
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

		$cause_max = 65000;

		$post['cause'] = trim($post['cause']);
		if (strlen($post['cause']) == 0) 
			$errors['cause'] = $bezlang['vald_content_required'];
		else if (strlen($post['cause']) > $cause_max)
			$errors['cause'] = str_replace('%d', $cause_max, $bezlang['vald_content_too_long']);

		$data['cause'] = $post['cause'];

		$rootco = new Rootcauses();
		if ( ! array_key_exists((int)$post['rootcause'], $rootco->get()))
			$errors['type'] = $bezlang['vald_root_cause'];
		 
		$data['rootcause'] = (int)$post['rootcause'];

		return $data;
	}
	public function add($post, $data=array())
	{
		$from_user = $this->validate($post);
		$data = array_merge($data, $from_user);

		$this->errinsert($data, 'causes');
	}
	public function update($post, $data, $id) {
		$from_user = $this->validate($post);
		$data = array_merge($data, $from_user);

		$this->errupdate($data, 'causes', $id);
	}
	public function getone($id) {
		$id = (int) $id;
		$a = $this->fetch_assoc("SELECT * FROM causes WHERE id=$id");

		return $a[0];
	}
	public function get($issue) {
		$issue = (int) $issue;

		$a = $this->fetch_assoc("SELECT * FROM causes WHERE issue=$issue");

		$usro = new Users();
		$rootco = new Rootcauses();
		foreach ($a as &$row) {
			$row['reporter'] = $usro->name($row['reporter']);
			$row['rootcause'] = $rootco->name($row['rootcause']);
		}

		return $a;
	}
}

