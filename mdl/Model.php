<?php

namespace dokuwiki\plugin\bez\mdl;

//ACL level defines
define('BEZ_AUTH_NONE', 0);
define('BEZ_AUTH_VIEWER', 2);
define('BEZ_AUTH_USER', 5);
define('BEZ_AUTH_LEADER', 10);
define('BEZ_AUTH_ADMIN', 20);

define('BEZ_PERMISSION_UNKNOWN', -1);
define('BEZ_PERMISSION_NONE', 0);
define('BEZ_PERMISSION_VIEW', 1);
define('BEZ_PERMISSION_CHANGE', 2);
define('BEZ_PERMISSION_DELETE', 3);


class Model {
    /** @var \helper_plugin_sqlite */
	protected $sqlite;

	/** @var \SQLite3 */
	protected $db;

    protected $level = BEZ_AUTH_NONE;

    protected $dw_auth, $user_nick, $action, $conf;

    /** @var ThreadFactory */
    protected $threadFactory;

    /** @var UserFactory  */
    protected $userFactory;

    /** @var LabelFactory */
    protected $labelFactory;

    /** @var Thread_commentFactory */
    protected $thread_commentFactory;

    /** @var TaskFactory */
    protected $taskFactory;

    /** @var Task_programFactory */
    protected $task_programFactory;

    /** @var Task_commentFactory */
    protected $task_commentFactory;

    /** @var Authentication_tokenFactory */
    protected $authentication_tokenFactory;

	public function __get($property) {
        $models = array('userFactory', 'threadFactory', 'labelFactory', 'thread_commentFactory', 'taskFactory', 'task_programFactory', 'task_commentFactory', 'authentication_tokenFactory');
		if (in_array($property, $models) ||
            in_array($property, array('sqlite', 'db', 'acl', 'dw_auth', 'user_nick', 'action', 'conf'))) {
			return $this->$property;
		}
	}

	public function factory($table) {
	    $prop = $table . 'Factory';

	    return $this->$prop;
    }

    protected function update_level($level) {
        if ($level > $this->level) {
            $this->level = $level;
        }
    }

    public function get_level() {
        return $this->level;
    }
	
	public function __construct($dw_auth, $user_nick, $action, $skip_acl=false) {
        $this->dw_auth = $dw_auth;
        $this->user_nick = $user_nick;
		$this->action = $action;
        $this->conf = $action->getGlobalConf();

        $this->db_helper =  plugin_load('helper', 'bez_db');

        $this->sqlite = $this->db_helper->getDB();
        $this->db = $this->sqlite->getAdapter()->getDb();
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->userFactory = new UserFactory($this);

        $this->threadFactory = new ThreadFactory($this);

        $this->labelFactory = new LabelFactory($this);

        $this->thread_commentFactory = new Thread_commentFactory($this);

        $this->taskFactory = new TaskFactory($this);

        $this->task_programFactory = new Task_programFactory($this);

        $this->task_commentFactory = new Task_commentFactory($this);

        $this->authentication_tokenFactory = new Authentication_tokenFactory($this);

        if ($skip_acl) {
            $this->update_level(BEZ_AUTH_ADMIN);
        } else {
            $userd = $this->dw_auth->getUserData($this->user_nick);
            if ($userd !== false && is_array($userd['grps'])) {
                $grps = $userd['grps'];
                if (in_array('admin', $grps ) || in_array('bez_admin', $grps )) {
                    $this->update_level(BEZ_AUTH_ADMIN);
                } elseif (in_array('bez_leader', $grps )) {
                    $this->update_level(BEZ_AUTH_LEADER);
                } else {
                    $this->update_level(BEZ_AUTH_USER);
                }
            } elseif (isset($_GET['t'])) {
                $page_id = $this->action->id();

                $user_tok = trim($_GET['t']);
                if ($this->authentication_tokenFactory->get_token($page_id) == $user_tok) {
                    $this->update_level(BEZ_AUTH_VIEWER);
                }
            }
        }
    }
}
