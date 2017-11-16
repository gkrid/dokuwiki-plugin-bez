<?php

//require_once DOKU_PLUGIN.'bez/exceptions.php';

//require_once 'acl.php';
//require_once 'validator.php';

//require_once 'users.php';
//require_once 'threads.php';
//require_once 'issuetypes.php';
//require_once 'tasks.php';
//require_once 'tasktypes.php';
//require_once 'commcauses.php';
//require_once 'timeline.php';

namespace dokuwiki\plugin\bez\mdl;


class Model {
    /** @var \helper_plugin_sqlite */
	protected $sqlite;

	/** @var \SQLite3 */
	protected $db;

	/** @var Acl */
    protected $acl;

	//private $db;
	
    //private $models = array('users', 'issues', 'tasks', 'tasktypes', 'commcauses', 'timeline');
	//private $users, $issues, $tasks, $tasktypes, $commcauses, $timeline;

    protected $dw_auth, $user_nick, $action, $conf;

    /** @var ThreadFactory */
    protected $threadFactory;

    /** @var UserFactory  */
    protected $userFactory;

    /** @var LabelFactory */
    protected $labelFactory;

	public function __get($property) {
        $models = array('userFactory', 'threadFactory', 'labelFactory');
		if (in_array($property, $models) ||
            in_array($property, array('sqlite', 'db', 'acl', 'dw_auth', 'user_nick', 'action', 'conf'))) {
			return $this->$property;
		}
	}
	
	public function __construct($dw_auth, $user_nick, $action, $conf) {
		$this->dw_auth = $dw_auth;
		$this->user_nick = $user_nick;
		$this->action = $action;
        $this->conf = $conf;
        		
//		$db_path = DOKU_INC . 'data/bez.sqlite';
//		//if database not exists
//		if (!file_exists($db_path)) {
//			$this->db = new PDO('sqlite:/' . $db_path);
//			$schema = file_get_contents(DOKU_PLUGIN . 'bez/mdl/schema.sql');
//			if ($schema === false) {
//				throw new Exception('cannot find schema file: '.$schema);
//			}
//			$this->db->exec($schema);
//		} else {
//			$this->db = new PDO('sqlite:/' . $db_path);
//		}
//		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//        //convert NULLS to empty strings
//        $this->db->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING);

        $this->sqlite = plugin_load('helper', 'sqlite');
        if(!$this->sqlite) {
            if(defined('DOKU_UNITTEST')) throw new \Exception('Couldn\'t load sqlite.');
            return;
        }

        if($this->sqlite->getAdapter()->getName() != DOKU_EXT_PDO) {
            if(defined('DOKU_UNITTEST')) throw new \Exception('Couldn\'t load PDO sqlite.');
            $this->sqlite = null;
            return;
        }
        $this->sqlite->getAdapter()->setUseNativeAlter(true);

        // initialize the database connection
        if(!$this->sqlite->init('b3p', DOKU_PLUGIN . 'bez/db/')) {
            if(defined('DOKU_UNITTEST')) throw new \Exception('Couldn\'t init sqlite.');
            $this->sqlite = null;
            return;
        }

        $this->db = $this->sqlite->getAdapter()->getDb();

        $this->acl = new Acl($this);

        $this->threadFactory = new ThreadFactory($this);
        $this->userFactory = new UserFactory($this);
        $this->labelFactory = new LabelFactory($this);
        
//        $this->acl = new BEZ_mdl_Acl($this);
//
//        $this->issues = new BEZ_mdl_Issues($this);
//        $this->tasks = new BEZ_mdl_Tasks($this);
//
//		$this->users = new BEZ_mdl_Users($this);
//
//		$this->issuetypes = new BEZ_mdl_Issuetypes($this);
//
//		$this->tasktypes = new BEZ_mdl_Tasktypes($this);
//
//		$this->commcauses = new BEZ_mdl_Commcauses($this);
//        $this->timeline = new BEZ_mdl_Timeline($this);
	}

//	public function __destruct() {
//		//http://stackoverflow.com/questions/18277233/pdo-closing-connection
//		$this->db = NULL;
//	}
}
