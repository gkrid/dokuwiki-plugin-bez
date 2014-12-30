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
	comment CHAR(100) NULL,

	PRIMARY KEY (id)
)
EOM;
	$this->errquery($q);
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

		$ento = new Entities();
		if ( ! in_array($post['entity'], $ento->get_list())) {
			$errors['entity'] = $bezlang['vald_entity_required'];
		} 
		$data['entity'] = $post['entity'];

		/*Koordynator nie jest wymagany*/
		if ($post['coordinator'] != '') {
			$usro = new Users();
			 if (!in_array($post['coordinator'], $usro->nicks()))
				$errors['coordinator'] = $bezlang['vald_coordinator_required'];

			$data['coordinator'] = $post['coordinator'];
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

		return $data;
	}

	public function add($post, $data=array()) {
		if ($this->helper->user_editor()) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$this->errinsert($data, 'issues');
		}
	}

	public function get($id) {
		global $bezlang, $errors;
		if ($this->helper->user_viewer()) {
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

			$usro = new Users();
			$a['reporter'] = $usro->name($a['reporter']);

			$a['date'] = (int)$a['date'];
			
			return $a;
		}
	}
}

