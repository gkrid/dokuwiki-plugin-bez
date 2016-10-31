<?php

require_once 'auth.php';
require_once 'validator.php';

require_once 'users.php';
require_once 'issues.php';
require_once 'tasks.php';
require_once 'tasktypes.php';

class BEZ_mdl_Model {
	private $db;
	
	private $users, $cache, $issues, $tasks, $tasktypes;
	
	private $dw_auth, $user_nick, $lang_code;
	
	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}
	
	public function __construct($dw_auth, $user_nick, $lang_code) {
		$this->dw_auth = $dw_auth;
		$this->user_nick = $user_nick;
				
		if ($lang_code === 'pl') {
			$this->lang_code = $lang_code;
		} else {
			$this->lang_code = 'en';
		}
		
		$db_path = DOKU_INC . 'data/bez.sqlite';
		//if database not exists
		if (!file_exists($db_path)) {
			$this->db = new PDO('sqlite:/' . $db_path);
			$schema = file_get_contents(DOKU_PLUGIN . 'bez/mdl/schema.sql');
			if ($schema === false) {
				throw new Exception('cannot find schema file: '.$schema);
			}
			$this->db->exec($schema);
		} else {		
			$this->db = new PDO('sqlite:/' . $db_path);
		}
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$this->users = new BEZ_mdl_Users($this);
		$this->issues = new BEZ_mdl_Issues($this);
		$this->tasks = new BEZ_mdl_Tasks($this);
		$this->tasktypes = new BEZ_mdl_Tasktypes($this);
	}
	
	public function __destruct() {
		//http://stackoverflow.com/questions/18277233/pdo-closing-connection
		$this->db = NULL;
	}
	
	
	
}
