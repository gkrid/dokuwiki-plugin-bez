<?php
include_once DOKU_PLUGIN."bez/models/connect.php";

class Tokens extends Connect {
	private $password;
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = "CREATE TABLE IF NOT EXISTS tokens (
				id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				token CHAR(100) NOT NULL,
				page  CHAR(200) NOT NULL,
				date INT(11) NOT NULL,
				PRIMARY KEY (id))";
		$this->errquery($q);

		$this->password = 'mlecznakrowa';
	}
	/*returns new token*/
	public function save($id) {
		global $errors;
		/*najpierw dodaj, a potem wygeneruj nowy token na podstawie id*/
		if ($this->helper->user_viewer()) {
			$data = array('page' => $id, 'date' => time());
			$this->errinsert($data, 'tokens');
			$token = sha1($password.$this->lastid);
			$this->errupdate(array('token' => $token), 'tokens', $this->lastid);

			if (count($errors) == 0)
				return $token;
		}
		return '';
	}

	/*sprawdź czy użytkownik o podanym $tokenie, może oglądać stronę o id = $page*/
	public function check($token, $page) {
		$a = $this->fetch_assoc("SELECT page FROM tokens WHERE token='".$this->db->real_escape_string($token)."'");
		if (count($a) > 1 && $a[0]['page'] == $page)
			return true;
		return false;
	}

	public function get($id) {
		$a = $this->fetch_assoc("SELECT token FROM tokens WHERE page='".$this->db->real_escape_string($id)."'");
		if (count($a) == 0)
			return $this->save($id);
		else
			return $a[0]['token'];
	}
}
