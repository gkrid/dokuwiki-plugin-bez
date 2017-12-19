<?php

namespace dokuwiki\plugin\bez\mdl;


class Model {
    /** @var \helper_plugin_sqlite */
	protected $sqlite;

	/** @var \SQLite3 */
	protected $db;

	/** @var Acl */
    protected $acl;

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
	
	public function __construct($dw_auth, $user_nick, $action, $conf) {
		$this->dw_auth = $dw_auth;
		$this->user_nick = $user_nick;
		$this->action = $action;
        $this->conf = $conf;

        $this->db_helper =  plugin_load('helper', 'bez_db');

        $this->sqlite = $this->db_helper->getDB();
        $this->db = $this->sqlite->getAdapter()->getDb();
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->acl = new Acl($this);

        $this->userFactory = new UserFactory($this);

        $this->threadFactory = new ThreadFactory($this);

        $this->labelFactory = new LabelFactory($this);

        $this->thread_commentFactory = new Thread_commentFactory($this);

        $this->taskFactory = new TaskFactory($this);

        $this->task_programFactory = new Task_programFactory($this);

        $this->task_commentFactory = new Task_commentFactory($this);

        $this->authentication_tokenFactory = new Authentication_tokenFactory($this);
    }
}
