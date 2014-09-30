<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/entities.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/users.php";

class Issues extends Connect {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = <<<EOM
CREATE TABLE IF NOT EXISTS issues (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	title CHAR(100) NOT NULL,
	description TEXT NOT NULL,
	state INT(11) NOT NULL,
	opinion TEXT NULL,
	type INT(11) NOT NULL,
	entity INT(11) NOT NULL,
	coordinator CHAR(100) NULL,
	reporter CHAR(100) NOT NULL,
	date INT(11) NOT NULL,
	comment CHAR(100) NULL,
	oldrev INT(11) NULL,

	PRIMARY KEY (id)
)
EOM;
	$this->errquery($q);
	}
	public function add($post, $data=array())
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
		if ( ! in_array($post['entity'], $ento->ids())) {
			$errors['entity'] = $bezlang['vald_entity_required'];
		} 
		$data['entity'] = (int)$post['entity'];

		$usro = new Users();
		if ($post['coordinator'] != NULL && !in_array($post['coordinator'], $usro->coordinators_nicks())) {
			$errors['coordinator'] = $bezlang['vald_coordinator_required'];
		} 
		$data['coordinator'] = $post['coordinator'];

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

		$this->errinsert($data);
	}
	public function lastid()
	{
		return $this->db->insert_id;
	}
	public function get($id) {
		global $bezlang, $errors;

		$id = (int) $id;
		$stao = new States();
		$a = $this->fetch_assoc("SELECT * FROM issues WHERE id=$id");
		if (count($a) == 0) {
			$errors[] = $bezlang['error_issue_id_not_specifed'];
			return array();
		}
		$a = $a[0];
		$a['state'] = $stao->name($a['state']);

		$isstyo = new Issuetypes();
		$a['type'] = $isstyo->name($a['type']);

		$ento = new Entities();
		$a['entity'] = $ento->name($a['entity']);

		$usro = new Users();
		$a['reporter'] = $usro->name($a['reporter']);

		return $a;
	}
}

