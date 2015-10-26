<?php

include_once "connect.php";

class Rootcauses extends Connect {
	public function __construct() {
		global $errors;
		parent::__construct();

		$exists = true;
		$q = "SELECT name FROM sqlite_master WHERE type='table' AND name='rootcauses'";
		$r = $this->fetch_assoc($q);
		if (count($r) == 0)
			$exists = false;

		$q = "CREATE TABLE IF NOT EXISTS rootcauses (
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

			$types = array(	'manpower',
							'method',
							'machine',
							'material',
							'managment',
							'measurement',
							'money',
							'environment',
							'communication'
						);
			for ($i=0;$i<count($types);$i++){
				$data = array(
					'en' => $en[$types[$i]],
					'pl' => $pl[$types[$i]]
				);
				$this->errinsert($data, 'rootcauses');
			}

			$this->errquery("UPDATE causes SET rootcause=rootcause+1");
		}
	}
	public function can_modify() {
		if ($this->helper->user_admin()) 
			return true;

		return false;
	}
	public function validate($post) {
		global $bezlang, $errors;

		$post['en'] = trim($post['en']);
		$post['pl'] = trim($post['pl']);

		$regex = '/^[[:alnum:] ]*$/ui';
		if( ! preg_match($regex, $post['en']) || ! preg_match($regex, $post['pl'])) {
			$errors['type'] = $bezlang['vald_type_wrong_chars'];
		} 

		return $post;
	}
	public function get() {
		global $conf;

		$lang = $conf['lang'];
		$result = $this->fetch_assoc("SELECT * FROM rootcauses");

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

	public function get_clean() {
		$result = $this->fetch_assoc("SELECT rootcauses.id, pl, en,
							(SELECT COUNT(*) FROM causes WHERE rootcause = rootcauses.id) as refs 
							FROM rootcauses");
		return $result;
	}

	public function get_one($id) {
		$result = $this->fetch_assoc("SELECT * FROM rootcauses WHERE id=$id");
		return $result[0];
	}

	public function add($post) {
		global $bezlang, $errors;
		if ( ! $this->can_modify()) 
			return false;

		$data = $this->validate($post);
		if (strlen($data['pl']) == 0 || strlen($data['en']) == 0) {
			$errors['type'] = $bezlang['vald_type_required'];
			return false;
		}
		$this->errinsert($post, 'rootcauses');
		return $data;
	}
	public function update($post, $id) {
		global $INFO;
		if ( ! $this->can_modify()) 
			return false;

		$data = $this->validate($post);
		if (strlen($data['pl']) == 0 && strlen($data['en']) == 0) {
			$this->errquery("DELETE FROM rootcauses WHERE id=$id");
		} else {
			$this->errupdate($data, 'rootcauses', $id);
		}
		return $post;
	}
	public function clean_empty() {
		$res = $this->get_clean();
		foreach ($res as $r) {
			if ((int)$r['refs'] == 0) {
				$id = $r['id'];
				$this->errquery("DELETE FROM  rootcauses WHERE id=$id");
			}
		}
	}
}