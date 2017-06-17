<?php

require_once DOKU_PLUGIN.'bez/exceptions.php';

require_once 'auth.php';
require_once 'acl.php';
require_once 'validator.php';

require_once 'users.php';
require_once 'issues.php';
require_once 'issuetypes.php';
require_once 'tasks.php';
require_once 'tasktypes.php';
require_once 'commcauses.php';
require_once 'timeline.php';

class BEZ_mdl_Model {
	private $db, $acl;
	
    private $models = array('users', 'issues', 'tasks', 'tasktypes', 'commcauses', 'timeline');
	private $users, $issues, $tasks, $tasktypes, $commcauses, $timeline;
	
	private $dw_auth, $user_nick, $action, $conf;
	
	public function __get($property) {
		if (in_array($property, $this->models) ||
            in_array($property, array('db', 'acl', 'dw_auth', 'user_nick', 'action', 'conf'))) {
			return $this->$property;
		}
	}
	
	public function __construct($dw_auth, $user_nick, $action, $conf) {
		$this->dw_auth = $dw_auth;
		$this->user_nick = $user_nick;
		$this->action = $action;
        $this->conf = $conf;
        		
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
        
        $this->acl = new BEZ_mdl_Acl($this);
        
        $this->issues = new BEZ_mdl_Issues($this);
        $this->tasks = new BEZ_mdl_Tasks($this);
        
		$this->users = new BEZ_mdl_Users($this);
        
		$this->issuetypes = new BEZ_mdl_Issuetypes($this);
		
		$this->tasktypes = new BEZ_mdl_Tasktypes($this);
		
		$this->commcauses = new BEZ_mdl_Commcauses($this);
        $this->timeline = new BEZ_mdl_Timeline($this);
	}
	
	public function __destruct() {
		//http://stackoverflow.com/questions/18277233/pdo-closing-connection
		$this->db = NULL;
	}
}
