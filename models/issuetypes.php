<?php

include_once "connect.php";

class Issuetypes extends Connect {
	/*public function get_old_format() {
		global $bezlang;
		return array(
			$bezlang['type_noneconformity_internal'],
			$bezlang['type_noneconformity_customer'],
			$bezlang['type_noneconformity_supplier'],
			$bezlang['type_threat'],
			$bezlang['type_opportunity']
		);
	}*/

	public function __construct() {
		global $errors;
		parent::__construct();

		$exists = true;
		$q = "SELECT name FROM sqlite_master WHERE type='table' AND name='issuetypes'";
		$r = $this->fetch_assoc($q);
		if (count($r) == 0)
			$exists = false;

		$q = "CREATE TABLE IF NOT EXISTS issuetypes (
				id INTEGER PRIMARY KEY,
				pl VARCHAR(100) NOT NULL,
				en VARCHAR(100) NOT NULL)";
		$this->errquery($q);

		/*!!*/
		if ( ! $exists) {
			include DOKU_PLUGIN."bez/lang/en/lang.php";
			$en = $lang;
			include DOKU_PLUGIN."bez/lang/pl/lang.php";
			$pl = $lang;

			$types = array('type_noneconformity_internal',
							'type_noneconformity_customer',
							'type_noneconformity_supplier',
							'type_threat',
							'type_opportunity');
			$issuetypes = array_flip($types);

			/*mapowanie[type][enitiy id] = new type id*/
			$nist = array(array(), array(), array(), array(), array());

			$result = $this->fetch_assoc("SELECT * FROM entities");
			foreach ($types as $type)
				foreach ($result as $entity) {
					$data = array(
						'en' => $en[$type].' '.$entity['entity'],
						'pl' => $pl[$type].' '.$entity['entity'],
					);
					$this->errinsert($data, 'issuetypes');
					$nist[$issuetypes[$type]][$entity['entity']] = $this->lastid;
				}

			$result = $this->fetch_assoc("SELECT * FROM issues");
			foreach($result as $r) {
				$id = $r['id'];
				$type = $r['type'];
				$entity = $r['entity'];

				$newtype = $nist[$type][$entity];

				$this->errquery("UPDATE issues SET type=$newtype WHERE id=$id");
			}
		}
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
				return false;
			}
			if ( ! mb_detect_encoding($entity, 'ASCII', true)) {
				$errors['entities'] = str_replace('%d', $entity_max, $bezlang['vald_entity_no_ascii']);
				return false;
			}

			array_push($array, array('entity' => trim($entity)));
		}

		return $array;
	}
	public function get() {
		global $conf;

		$lang = $conf['lang'];
		$result = $this->fetch_assoc("SELECT * FROM issuetypes");

		$data = array();
		foreach ($result as $r) {
			$data[$r['id']] = $r[$lang];
		}

		return $data;
	}

	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

