<?php
include_once DOKU_PLUGIN."bez/models/connect.php";

class Entities extends Connect {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = <<<EOM
CREATE TABLE IF NOT EXISTS entities (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	entity CHAR(100) NOT NULL,

	PRIMARY KEY (id)
)
EOM;
	$this->errquery($q);
	}
	public function can_modify() {
		if ($this->helper->user_admin()) 
			return true;

		return false;
	}
	public function validate($post) {
		global $bezlang, $errors;

		$entity_max = 100;

		$post['entities'] = trim($post['entities']);
		$entites = explode("\n", $post['entities']);

		$array = array();
		foreach ($entites as $entity) {
			if (strlen($entity) > $entity_max) {
				$errors['entities'] = str_replace('%d', $entity_max, $bezlang['vald_entity_too_long']);
				break;
			}
		array_push($array, array('entity' => trim($entity)));
		}

		return $array;
	}
	public function get() {
		$result = $this->fetch_assoc("SELECT * FROM entities");
		return $result;
	}
	public function get_list() {
		$entities = $this->get();
		$array = array();
		foreach ($entities as $entity) {
			$array[] = $entity['entity'];
		}
		return $array;
	}
	public function get_string() {
		$s = '';
		$entities = $this->get();
		foreach ($entities as $e) {
			$s .= $e['entity']."\n";
		}
		return $s;
	}
	public function save($post) {
		if ($this->can_modify()) {
			$data = $this->validate($post);

			$this->errquery('START TRANSACTION');
			$this->errquery('DELETE FROM entities');
			$this->mul_errinsert($data, 'entities');
			if (count($errors) > 0)
				$this->errquery('ROLLBACK');
			else
				$this->errquery('COMMIT');
		}
	}

	public function ids() {
		return $this->get_list();
	}
	public function name($id) {
		return $id;
	}
}

