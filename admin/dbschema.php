<?php
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
 
 // must be run within DokuWiki
if(!defined('DOKU_INC')) die();
 if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
 
$errors = array();
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/tasktypes.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

require_once DOKU_PLUGIN.'bez/mdl/model.php';

class admin_plugin_bez_dbschema extends DokuWiki_Admin_Plugin {

	private $exp = false;
	private $connect;
 
	function getMenuText($lang) {
		return 'Zaktualizuj schemat bazy BEZ';
	}
	
	function __construct() {
		global $auth, $INFO;
		$this->connect = new Connect();
		$this->model = new BEZ_mdl_Model($auth, $INFO['client'], $conf['lang']);
	}
	
	function check_remove_action_from_tasks() {
		/*potential cause*/
		$schema = $this->connect->fetch_assoc("PRAGMA table_info(tasks)");
		foreach ($schema as $column)
			if ($column['name'] == 'action')
				return false;
		
		return true;
	}
	
	function do_remove_action_from_tasks() {
		$q = "
		BEGIN TRANSACTION;
		ALTER TABLE tasks RENAME to tasks_backup;
		CREATE TABLE IF NOT EXISTS tasks (
				id INTEGER PRIMARY KEY,
				task TEXT NOT NULL,
				state INTEGER NOT NULL,
				executor TEXT NOT NULL,
				cost INTEGER NULL,
				reason TEXT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				close_date INTEGER NULL,
				cause INTEGER NULL,
				issue INTEGER NOT NULL
				);
		INSERT INTO tasks SELECT id, task, state, executor, cost, reason, reporter, date, close_date, cause, issue FROM tasks_backup;
		COMMIT;
		";
		
		$qa = explode(';', $q);
		$con = new Connect();
		$db = $con->open();
		foreach ($qa as $e)  {
			$db->query($e);
		}
		$db->close();
	}
	
	function check_potentials() {
		/*potential cause*/
		$schema = $this->connect->fetch_assoc("PRAGMA table_info(causes)");
		foreach ($schema as $column)
			if ($column['name'] == 'potential')
				return true;
		
		return false;
	}
	
	function do_potentials() {
		$this->connect->errquery("ALTER TABLE causes ADD COLUMN potential INTEGER DEFAULT 0");
	}
	
	function check_causetask() {
		/*cause in task*/
		$schema = $this->connect->fetch_assoc("PRAGMA table_info(tasks)");
		foreach ($schema as $column)
			if ($column['name'] == 'cause')
				return true;
		return false;
	}
	
	function do_causetask() {
			global $errors;
			$this->connect->errquery("ALTER TABLE tasks ADD COLUMN cause INTEGER NULL");
			$issues = $this->connect->fetch_assoc("SELECT * FROM issues");
			foreach($issues as $issue) {
				$id = $issue[id];
				$tasks = $this->connect->fetch_assoc("SELECT * FROM tasks WHERE issue=$id");
				$koryg = array();
				$zapo = array();
				foreach($tasks as $task) {
					if($task[action] == 1)
						$koryg[] = $task[id];
					else if($task[action] == 2)
						$zapo[] = $task[id];
				}
				$causes = $this->connect->fetch_assoc("SELECT * FROM causes WHERE issue=$id");
				
				if(count($causes) > 1 || count($causes) == 0) {
					if(count($koryg) > 0) {
						$lastid = $this->connect->ins_query("INSERT INTO 
								causes(potential, cause, rootcause, reporter, date, issue) VALUES
								(0, 'Zadania nie przypisane do przyczyn.', 8, '',  ".time().", $id)");
								
						foreach($koryg as $tid)
							$this->connect->errquery("UPDATE tasks SET cause=$lastid WHERE id=$tid");
					}
					if(count($zapo) > 0) {
						$lastid = $this->connect->ins_query("INSERT INTO 
								causes (potential, cause, rootcause, reporter, date, issue) VALUES
								(1, 'Zadania nie przypisane do potencjalnej przyczyn.', 8, '',  ".time().", $id)");
						foreach($zapo as $tid)
							$this->connect->errquery("UPDATE tasks SET cause=$lastid WHERE id=$tid");
					}
				} else {
					$cid = $causes[0][id];
					if(count($koryg) == 0 && count($zapo) > 0) {
						$this->connect->errquery("UPDATE causes SET potential=1 WHERE id=$cid");
						foreach(array_merge($koryg, $zapo) as $tid){
							$this->connect->errquery("UPDATE tasks SET cause=$cid WHERE id=$tid");
							
						}
					} else foreach(array_merge($koryg, $zapo) as $tid)
						$this->connect->errquery("UPDATE tasks SET cause=$cid WHERE id=$tid");
				}
			}
	}
	
	function check_rementity() {
		$q = "PRAGMA table_info(issues)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) 
			if ($r['name'] == 'entity')
				return false;
		return true;
	}
	
	function do_rementity() {
	$createq = "CREATE TABLE IF NOT EXISTS issues (
				id INTEGER PRIMARY KEY,
				priority INTEGER NOT NULL DEFAULT 0,
				title TEXT NOT NULL,
				description TEXT NOT NULL,
				state INTEGER NOT NULL,
				opinion TEXT NULL,
				type INTEGER NOT NULL,
				coordinator TEXT NOT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				last_mod INTEGER)";
		$q = "	BEGIN TRANSACTION;
			CREATE TEMPORARY TABLE issues_backup
			(
					id INTEGER PRIMARY KEY,
					priority INTEGER NOT NULL DEFAULT 0,
					title TEXT NOT NULL,
					description TEXT NOT NULL,
					state INTEGER NOT NULL,
					opinion TEXT NULL,
					type INTEGER NOT NULL,
					coordinator TEXT NOT NULL,
					reporter TEXT NOT NULL,
					date INTEGER NOT NULL,
					last_mod INTEGER);
			INSERT INTO issues_backup SELECT
					id,
					priority,
					title,
					description,
					state,
					opinion,
					type,
					coordinator,
					reporter,
					date,
					last_mod
				FROM issues;
			DROP TABLE issues;
			$createq;
			INSERT INTO issues SELECT 
					id,
					priority,
					title,
					description,
					state,
					opinion,
					type,
					coordinator,
					reporter,
					date,
					last_mod
				FROM issues_backup;
			DROP TABLE issues_backup;
			COMMIT;
			";
			$qa = explode(';', $q);
			$con = new Connect();
			$db = $con->open();
			foreach ($qa as $e)  {
				$db->query($e);
			}
			$db->close();
	}
	
	function check_rootcause() {
		$q = "SELECT name FROM sqlite_master WHERE type='table' AND name='rootcauses'";
		$r = $this->connect->fetch_assoc($q);
		if (count($r) == 0)
			return false;
		return true;
	}
	
	function do_rootcause() {
		$q = "CREATE TABLE IF NOT EXISTS rootcauses (
				id INTEGER PRIMARY KEY,
				pl VARCHAR(100) NOT NULL,
				en VARCHAR(100) NOT NULL)";
		$this->connect->errquery($q);

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
				$this->connect->errinsert($data, 'rootcauses');
			}

			$this->connect->errquery("UPDATE causes SET rootcause=rootcause+1");
	}
	
	function check_add_plan_date_to_tasks() {
		$q = "PRAGMA table_info(tasks)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) 
			if ($r['name'] == 'all_day_event')
				return true;
		return false;
	}
	
	function do_add_plan_date_to_tasks() {
		$q = "ALTER TABLE tasks ADD COLUMN all_day_event INTEGER DEFAULT 0";
		$this->connect->errquery($q);
		$q = "ALTER TABLE tasks ADD COLUMN plan_date TEXT NULL";
		$this->connect->errquery($q);
		$q = "ALTER TABLE tasks ADD COLUMN start_time TEXT NULL";
		$this->connect->errquery($q);
		$q = "ALTER TABLE tasks ADD COLUMN finish_time TEXT NULL";
		$this->connect->errquery($q);
	}
	
	function check_add_type_to_tasks() {
		$q = "PRAGMA table_info(tasks)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) 
			if ($r['name'] == 'tasktype')
				return true;
		return false;
	}
	
	function do_add_type_to_tasks() {
		$q = "ALTER TABLE tasks ADD COLUMN tasktype INTEGER NULL";
		$this->connect->errquery($q);
	}
	
	
	function check_types() {
		$q = "SELECT name FROM sqlite_master WHERE type='table' AND name='issuetypes'";
		$r = $this->connect->fetch_assoc($q);
		if (count($r) == 0)
			return false;
		return true;
	}
	
	function do_types() {
	
		$q = "CREATE TABLE IF NOT EXISTS issuetypes (
				id INTEGER PRIMARY KEY,
				pl VARCHAR(100) NOT NULL,
				en VARCHAR(100) NOT NULL)";
		$this->connect->errquery($q);

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

			$result = $this->connect->fetch_assoc("SELECT * FROM entities");
			foreach ($types as $type)
				foreach ($result as $entity) {
					$data = array(
						'en' => $en[$type].' '.$entity['entity'],
						'pl' => $pl[$type].' '.$entity['entity'],
					);
					$this->connect->errinsert($data, 'issuetypes');
					$nist[$issuetypes[$type]][$entity['entity']] = $this->connect->lastid;
				}
			$result = $this->connect->fetch_assoc("SELECT * FROM issues");
			foreach($result as $r) {
				$id = $r['id'];
				$type = $r['type'];
				$entity = $r['entity'];

				$newtype = $nist[$type][$entity];

				$this->connect->errquery("UPDATE issues SET type=$newtype WHERE id=$id");
			}
	}
	
	
	function check_task_issue_null() {
		$q = "PRAGMA table_info(tasks)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) 
			if ($r['name'] == 'issue' && $r['notnull'] == 1) {
				return false;
			}
		return true;
	}
	
	function do_task_issue_null() {
		$createq = "CREATE TABLE IF NOT EXISTS tasks (
				id INTEGER PRIMARY KEY,
				task TEXT NOT NULL,
				state INTEGER NOT NULL,
				tasktype INTEGER NULL,
				executor TEXT NOT NULL,
				cost INTEGER NULL,
				reason TEXT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				close_date INTEGER NULL,
				cause INTEGER NULL,
				plan_date TEXT NULL,
				all_day_event INTEGET DEFAULT 0,
				start_time TEXT NULL,
				finish_time TEXT NULL,
				issue INTEGER NULL
				)";
				
		$q = "	BEGIN TRANSACTION;
			CREATE TEMPORARY TABLE tasks_backup
			(
				id INTEGER PRIMARY KEY,
				task TEXT NOT NULL,
				state INTEGER NOT NULL,
				tasktype INTEGER NULL,
				executor TEXT NOT NULL,
				cost INTEGER NULL,
				reason TEXT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				close_date INTEGER NULL,
				cause INTEGER NULL,
				plan_date TEXT NULL,
				all_day_event INTEGET DEFAULT 0,
				start_time TEXT NULL,
				finish_time TEXT NULL,
				issue INTEGER NULL
				);
			INSERT INTO tasks_backup SELECT
					id,
					task,
					state,
					tasktype,
					executor,
					cost,
					reason,
					reporter,
					date,
					close_date,
					cause,
					plan_date,
					all_day_event,
					start_time,
					finish_time,
					issue
				FROM tasks;
			DROP TABLE tasks;
			$createq;
			INSERT INTO tasks SELECT
					id,
					task,
					state,
					tasktype,
					executor,
					cost,
					reason,
					reporter,
					date,
					close_date,
					cause,
					plan_date,
					all_day_event,
					start_time,
					finish_time,
					issue
				FROM tasks_backup;
			DROP TABLE tasks_backup;
			COMMIT;
			";
			$qa = explode(';', $q);
			$con = new Connect();
			$db = $con->open();
			foreach ($qa as $e)  {
				$db->query($e);
			}
			$db->close();
	}
	
	function check_proza_import() {
		$fname = DOKU_INC . 'data/proza_imported';
		return file_exists($fname);
	}
	
	function do_proza_import() {
			//~ $con = new Connect();
			//$bez = $con->open();
			//~ $tasko = new Tasktypes();
			//$to = new Tasks();
			$proza = new SQLite3(DOKU_INC . 'data/proza.sqlite');
			
			//sprawdź czy mamy wszystkich potrzebnych użytkowników
			$r = $proza->query("SELECT coordinator FROM events GROUP BY coordinator");
			$wiki_users = $this->model->users->get_all();
			$unknown_users = array();
			while ($w = $r->fetchArray(SQLITE3_ASSOC)) {
				$user = $w['coordinator'];
				if (!array_key_exists($user, $wiki_users)) {
					$unknown_users[] = $user;
				}
			}
			
			if (!empty($unknown_users)) {
				throw new Exception('PROZA has unknown users: '.implode(',', $unknown_users));
			}

			//groupy
			$z_prozy_do_bezu = array();//mapownaie grup
			$r = $proza->query("SELECT * FROM groups");
			while ($w = $r->fetchArray(SQLITE3_ASSOC)) {
				//~ $post = $w;
				//~ unset($post['id']);
				//~ $post['coordinator'] = 'rolewniczak';
				$tasktype = $this->model->tasktypes->create_object();
				$tasktype->set(array(
							'pl' => $w['pl'],
							'en' => $w['en'],
							'coordinator' => 'rolewniczak'
							
				));
				$lastid = $this->model->tasktypes->save($tasktype);
				$z_prozy_do_bezu[$w['id']] = $lastid;
				
				//~ $tasko->add($post);
				//~ $lastid = $tasko->lastid();
				//~ $z_prozy_do_bezu[$w['id']] = $lastid;
			}			
						
			$r = $proza->query("SELECT * FROM events");
			while ($w = $r->fetchArray(SQLITE3_ASSOC)) {
				
				$meta = array('reporter' => $w['coordinator'] ,
								'date' => time());
								
				if ($w['finish_date'] != '') {
					$meta['close_date'] = strtotime($w['finish_date']);
				}
				
				$data = array(
					'task' => $w['assumptions'],
					'executor' => $w['coordinator'],
					'cost' => $w['cost'],
					'all_day_event' => 1,
					'plan_date' => $w['plan_date']
					
				);
				
				$state_data = array('state' => $w['state'],
								'reson'=> $w['summary']);
				
				$tasktype = $z_prozy_do_bezu[$w['group_n']];
				
				$task = $this->model->tasks->create_object(array('tasktype' => $tasktype));
				$state = $task->set_meta($meta);
				if ($state == false) {
					throw new Exception(print_r($task->get_errors(), true));
				}
				$state = $task->set_data($data);
				if ($state == false) {
					throw new Exception(print_r($task->get_errors(), true));
				}
				$state = $task->set_state($state_data);
				if ($state == false) {
					throw new Exception(print_r($task->get_errors(), true));
				}

				$this->model->tasks->save($task);
			}
			
			$proza->close();
			//$bez->close();
			$fname = DOKU_INC . 'data/proza_imported';
			fopen($fname, "w");
	}
	
	function check_add_plan_date() {
		$q = "PRAGMA table_info(tasks)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) {
			if ($r['name'] == 'plan_date' && $r['notnull'] == 1) {
				return true;
			}
		}
		return false;
	}
	
	function do_add_plan_date() {
		$con = new Connect();
		$db = $con->open();
		$db->query("UPDATE tasks SET plan_date=date(tasks.date, 'unixepoch', '+2 months'), all_day_event=1
					WHERE tasks.plan_date = '' OR tasks.plan_date ISNULL;");

		$createq = "
				CREATE TABLE IF NOT EXISTS tasks (
				id INTEGER PRIMARY KEY,
				task TEXT NOT NULL,
				state INTEGER NOT NULL,
				tasktype INTEGER NULL,
				executor TEXT NOT NULL,
				cost INTEGER NULL,
				reason TEXT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				close_date INTEGER NULL,
				cause INTEGER NULL,
				plan_date TEXT NOT NULL,
				all_day_event INTEGET DEFAULT 0,
				start_time TEXT NULL,
				finish_time TEXT NULL,
				issue INTEGER NULL
				)";
				
		$q = "	BEGIN TRANSACTION;
			DROP TABLE tasks_backup;
			CREATE TEMPORARY TABLE tasks_backup
			(
				id INTEGER PRIMARY KEY,
				task TEXT NOT NULL,
				state INTEGER NOT NULL,
				tasktype INTEGER NULL,
				executor TEXT NOT NULL,
				cost INTEGER NULL,
				reason TEXT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				close_date INTEGER NULL,
				cause INTEGER NULL,
				plan_date TEXT NOT NULL,
				all_day_event INTEGET DEFAULT 0,
				start_time TEXT NULL,
				finish_time TEXT NULL,
				issue INTEGER NULL
				);
			INSERT INTO tasks_backup SELECT
					id,
					task,
					state,
					tasktype,
					executor,
					cost,
					reason,
					reporter,
					date,
					close_date,
					cause,
					plan_date,
					all_day_event,
					start_time,
					finish_time,
					issue
				FROM tasks;
			DROP TABLE tasks;
			$createq;
			INSERT INTO tasks SELECT
					id,
					task,
					state,
					tasktype,
					executor,
					cost,
					reason,
					reporter,
					date,
					close_date,
					cause,
					plan_date,
					all_day_event,
					start_time,
					finish_time,
					issue
				FROM tasks_backup;
			DROP TABLE tasks_backup;
			COMMIT;
			";
			$qa = explode(';', $q);

			foreach ($qa as $e)  {
				$db->query($e);
			}
			$db->close();
	}
	
	
function check_issues_remove_priority() {
		$q = "PRAGMA table_info(issues)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) {
			if ($r['name'] == 'priority') {
				return false;
			}
		}
		return true;
	}
	
function do_issues_remove_priority() {
		$con = new Connect();
		$db = $con->open();

		$createq = "
				CREATE TABLE IF NOT EXISTS issues (
				id INTEGER PRIMARY KEY,
				title TEXT NOT NULL,
				description TEXT NOT NULL,
				state INTEGER NOT NULL,
				opinion TEXT NULL,
				type INTEGER NOT NULL,
				coordinator TEXT NOT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				last_mod INTEGER)";
				
		$q = "	BEGIN TRANSACTION;
			CREATE TEMPORARY TABLE issues_backup
			(
				id INTEGER PRIMARY KEY,
				title TEXT NOT NULL,
				description TEXT NOT NULL,
				state INTEGER NOT NULL,
				opinion TEXT NULL,
				type INTEGER NOT NULL,
				coordinator TEXT NOT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				last_mod INTEGER);
			INSERT INTO issues_backup SELECT
					id,
					title,
					description,
					state,
					opinion,
					type,
					coordinator,
					reporter,
					date,
					last_mod
				FROM issues;
			DROP TABLE issues;
			$createq;
			INSERT INTO issues SELECT
					id,
					title,
					description,
					state,
					opinion,
					type,
					coordinator,
					reporter,
					date,
					last_mod
				FROM issues_backup;
			DROP TABLE issues_backup;
			COMMIT;
			";
			$qa = explode(';', $q);

			foreach ($qa as $e)  {
				$db->query($e);
			}
			$db->close();
	}
	
	function check_add_coordinator_to_tasktypes() {
		$q = "PRAGMA table_info(tasktypes)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) 
			if ($r['name'] == 'coordinator')
				return true;
		return false;
	}
	
	function do_add_coordinator_to_tasktypes() {
		$q = "ALTER TABLE tasktypes ADD COLUMN coordinator TEXT NOT NULL DEFAULT 'rolewniczak'";
		$this->connect->errquery($q);
	}
	
	function check_add_cache_to_tasks() {
		$q = "PRAGMA table_info(tasks)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) {
			if ($r['name'] == 'task_cache') {
				return true;
			}
		}
		return false;
	}
	
	function do_add_cache_to_tasks() {
		$q = "ALTER TABLE tasks ADD COLUMN task_cache TEXT NOT NULL DEFAULT ''";
		$this->connect->errquery($q);
		$q = "ALTER TABLE tasks ADD COLUMN reason_cache TEXT NULL";
		$this->connect->errquery($q);
		
		$result = $this->connect->fetch_assoc("SELECT * FROM tasks_cache ");
		foreach ($result as $task_cache) {
			$this->connect->errupdate(array(
				'task_cache' => $task_cache['task'],
				'reason_cache' => $task_cache['reason']
			), 'tasks', $task_cache['id']);
		}
		
	}
	
	function check_add_activity_to_issue() {
		$q = "PRAGMA table_info(issues)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) {
			if ($r['name'] == 'last_activity') {
				return true;
			}
		}
		return false;
	}
	
	function do_add_activity_to_issue() {
		$q = "ALTER TABLE issues ADD COLUMN last_activity DATE NOT NULL DEFAULT ''";
		$this->connect->errquery($q);
		
		$q = "UPDATE issues SET last_activity=(datetime('now','localtime'));";
		$this->connect->errquery($q);
		
		$q = "ALTER TABLE issues ADD COLUMN participants TEXT NOT NULL DEFAULT ''";
		$this->connect->errquery($q);
		
		$issues = $this->connect->fetch_assoc("SELECT * FROM issues");
		foreach($issues as $issue) {
			$participants = [];
			$id = $issue['id'];
			$reporter = $issue['reporter'];
			$coordinator = $issue['coordinator'];
			
			$participants[$reporter] = $reporter;
			if ($coordinator !== '-proposal' && $coordinator !== '-rejected') {
				$participants[$coordinator] = $coordinator;
			}
				
			$com = $this->connect->fetch_assoc("SELECT reporter FROM comments WHERE issue=$id");
			foreach ($com as $c) {
				$reporter = $c['reporter'];
				$participants[$reporter] = $reporter;
			}
			$tsk = $this->connect->fetch_assoc("SELECT reporter,executor FROM tasks WHERE issue=$id");
			foreach ($tsk as $t) {
				$reporter = $t['reporter'];
				$executor = $t['executor'];
				
				$participants[$reporter] = $reporter;
				$participants[$executor] = $executor;
			}
			$cause = $this->connect->fetch_assoc("SELECT reporter FROM causes WHERE issue=$id");
			foreach ($cause as $c) {
				$reporter = $c['reporter'];
				$participants[$reporter] = $reporter;
			}
			$part = implode(',', $participants);
			
			$q = "UPDATE issues SET participants='$part' WHERE id=$id";
			$this->connect->errquery($q);
		}
	}
	
		function check_issue_primary_key() {
		$q = "PRAGMA table_info(issues)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) {
				if ($r['name'] == 'id' && $r['pk'] === 0) {
					return false;
				}
		}
		return true;
	}
	
	function do_issue_primary_key() {
	$createq = 'CREATE TABLE issues (
    "id" INTEGER PRIMARY KEY,
    "title" TEXT NOT NULL,
    "description" TEXT NOT NULL,
    "state" INTEGER NOT NULL,
    "opinion" TEXT,
    "type" INTEGER NOT NULL,
    "coordinator" TEXT NOT NULL,
    "reporter" TEXT NOT NULL,
    "date" INTEGER NOT NULL,
    "last_mod" INTEGER NULL,
    "last_activity" TEXT NOT NULL,
    "participants" TEXT NOT NULL
);';
		$q = 'BEGIN TRANSACTION;
			CREATE TEMPORARY TABLE issues_backup
			(
					    "id" INTEGER PRIMARY KEY,
    "title" TEXT NOT NULL,
    "description" TEXT NOT NULL,
    "state" INTEGER NOT NULL,
    "opinion" TEXT,
    "type" INTEGER NOT NULL,
    "coordinator" TEXT NOT NULL,
    "reporter" TEXT NOT NULL,
    "date" INTEGER NOT NULL,
    "last_mod" INTEGER NULL,
    "last_activity" TEXT NOT NULL,
    "participants" TEXT NOT NULL);
			INSERT INTO issues_backup SELECT
					id,
					title,
					description,
					state,
					opinion,
					type,
					coordinator,
					reporter,
					date,
					last_mod,
					last_activity,
					participants
				FROM issues;
			DROP TABLE issues;
			'.$createq.'
			INSERT INTO issues SELECT 
					id,
					title,
					description,
					state,
					opinion,
					type,
					coordinator,
					reporter,
					date,
					last_mod,
					last_activity,
					participants
				FROM issues_backup;
			DROP TABLE issues_backup;
			COMMIT;
			';
			$qa = explode(';', $q);
			$con = new Connect();
			$db = $con->open();
			foreach ($qa as $e)  {
				$db->query($e);
			}
			$db->close();
	}
	
	private function check_remove_coordinator_from_tasktypes() {
		$q = "PRAGMA table_info(tasktypes)";
		$a = $this->connect->fetch_assoc($q);
		$entity = false;
		foreach ($a as $r) {
			if ($r['name'] == 'coordinator') {
				return false;
			}
		}
		return true;
	}
	
	private function do_remove_coordinator_from_tasktypes() {
	$con = new Connect();
		$db = $con->open();

		$createq = "
				CREATE TABLE tasktypes (
				id INTEGER PRIMARY KEY,
				pl VARCHAR(100) NOT NULL,
				en VARCHAR(100) NOT NULL);";
				
		$q = "	BEGIN TRANSACTION;
			CREATE TEMPORARY TABLE tasktypes_backup
			(
				id INTEGER PRIMARY KEY,
				pl VARCHAR(100) NOT NULL,
				en VARCHAR(100) NOT NULL);
			INSERT INTO tasktypes_backup SELECT
					id,pl,en
				FROM tasktypes;
			DROP TABLE tasktypes;
			$createq;
			INSERT INTO tasktypes SELECT
					id,pl,en
				FROM tasktypes_backup;
			DROP TABLE tasktypes_backup;
			COMMIT;
			";
			$qa = explode(';', $q);

			foreach ($qa as $e)  {
				$db->query($e);
			}
			$db->close();
	}
	
	private $actions = array(
				array('Słownik kategorii przyczyn', 'check_rootcause', 'do_rootcause'),
				array('Słownik typów problemów', 'check_types', 'do_types'),
				array('Usunięcie podmiotu w problemach', 'check_rementity', 'do_rementity'),
				array('Kolumna "potential" w zadaniach', 'check_potentials', 'do_potentials'),
				array('Zadania przypisane do przczyn', 'check_causetask', 'do_causetask'),
				array('Usunięcie kolumny "action" z tabeli zadań', 'check_remove_action_from_tasks', 'do_remove_action_from_tasks'),
				array('Dodanie planowanej daty do zadań w BEZie', 'check_add_plan_date_to_tasks', 'do_add_plan_date_to_tasks'),
				array('Dodanie typu dla zadań niezależnych w BEZie', 'check_add_type_to_tasks', 'do_add_type_to_tasks'),
				array('Zmiana kolumny issue w zadaniach na NULL.',
				'check_task_issue_null', 'do_task_issue_null'),
				array('Zaimportuj zadania z PROZY.',
				'check_proza_import', 'do_proza_import'),
				array('Dodaj datę planowania zadań.',
				'check_add_plan_date', 'do_add_plan_date'),
				array('Usuń priorytet z tabeli problemów',
				'check_issues_remove_priority', 'do_issues_remove_priority'),
				array('Dodaj koordynatora do programów',
				'check_add_coordinator_to_tasktypes', 'do_add_coordinator_to_tasktypes'),
				array('Dodaj cache do zadań',
				'check_add_cache_to_tasks', 'do_add_cache_to_tasks'),
				array('Dodaj aktywność do problemów',
				'check_add_activity_to_issue', 'do_add_activity_to_issue'),
				array('Dodaj klucz główny do problemów',
				'check_issue_primary_key', 'do_issue_primary_key'),
				array('Usuń koordynatora z programów',
				'check_remove_coordinator_from_tasktypes', 'do_remove_coordinator_from_tasktypes')
		);

	function _backup($sufix) {
		$db_file = DOKU_INC . 'data/bez.sqlite';
		$copy_file = DOKU_INC.'data/bez.sqlite.'.date('%c').'.'.$sufix;
		if (!file_exists($db_file)) {
			throw new Exception("database file: $db_file doesn't exists");
		}
		$copy = copy($db_file, $copy_file);
		if (!$copy) {
			throw new Exception("cannot create copy file: $copy_file");
		}
	}
	/**
	 * handle user request
	 */
	function handle() {
		global $errors;
 
	  if (!isset($_POST['applay'])) return;   // first time - nothing to do
	  if (!checkSecurityToken()) return;
	  //importuj
	  $this->exp = true;
	  $keys = array_keys($_POST[applay]);
	  $pr_id = $keys[0];
	  if (array_key_exists($pr_id, $this->actions)) {
		try {
			//create db backup
			$this->_backup('before'.$pr_id);
			$fname = $this->actions[$pr_id][2];
			$this->$fname();
		} catch (Exception $e) {
			$errors[] = $e->getMessage();
		}
	  }
	}
	
	/**
	 * output appropriate html
	 */
	function html() {
		global $errors, $conf;
	  ptln('<h1>'.$this->getMenuText($conf['lang']).'</h1>');
	  if ($this->exp == true) {
	  		if (is_array($errors))
				foreach ($errors as $error) {
					echo '<div class="error">';
					echo $error;
					echo '</div>';
				}
	  }
	  	  ptln('<form action="'.wl($ID).'" method="post">');
	  // output hidden values to ensure dokuwiki will return back to this plugin
	  ptln('  <input type="hidden" name="do"   value="admin" />');
	  ptln('  <input type="hidden" name="page" value="bez_dbschema" />');
	  formSecurityToken();
	  ptln('<table>');
	  ptln('<tr><th>Akcja</th><th>Status</th></tr>');
	  $i = 0;
	  foreach ($this->actions as $action) {
	  	$name = $action[0];
	  	$is_applaied = call_user_func(array($this, $action[1]));
		if ($is_applaied) ptln('<tr style="background-color: #0f0">');
		else ptln('<tr style="background-color: #f00">');
		
	  	ptln("<td>$i. $name</td>");
	  	
	  	if ($is_applaied) ptln("<td>Zastosowana</td>");
		else ptln('<td><input type="submit" name="applay['.$i.']"  value="Zastosuj" /></td>');
		
	  	ptln('</tr>');
	  	$i++;
	  }
	  ptln('</table>');
	  ptln('</form>');
	}
 
}

