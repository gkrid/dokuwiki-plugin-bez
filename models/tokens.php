<?php
include_once DOKU_PLUGIN."bez/models/connect.php";

class Tokens extends Connect {
	private $password;
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = "CREATE TABLE IF NOT EXISTS tokens (
				id INTEGER PRIMARY KEY,
				token TEXT NOT NULL,
				page TEXT NOT NULL,
				date INTEGER NOT NULL)";
		$this->errquery($q);

	}
	private function salt() {
		$length = 10;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	/*returns new token*/
	public function save($id) {
		global $errors;
		/*najpierw dodaj, a potem wygeneruj nowy token na podstawie id*/
		if ($this->helper->user_viewer()) {
			
			$salt = $this->salt();
			$token = md5($salt.$id);
			$data = array('token' => $token, 'page' => $id, 'date' => time());
			$this->errinsert($data, 'tokens');

			if (count($errors) == 0)
				return $token;
		}
		return '';
	}

	/*sprawdź czy użytkownik o podanym $tokenie, może oglądać stronę o id = $page*/
	public function check($token, $page) {
		$a = $this->fetch_assoc("SELECT page FROM tokens WHERE token='".$this->escape($token)."'");
		if (count($a) >= 1 && $a[0]['page'] == $page)
			return true;
		return false;
	}

	public function get($id) {
		$a = $this->fetch_assoc("SELECT token FROM tokens WHERE page='".$this->escape($id)."'");
		if (count($a) == 0)
			return $this->save($id);
		else
			return $a[0]['token'];
	}
}
