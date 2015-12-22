<?php
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
$errors = array();
include_once DOKU_PLUGIN."bez/models/connect.php";
class admin_plugin_bez_dbschema extends DokuWiki_Admin_Plugin {

	private $exp = false;
	private $connect;
 
	function getMenuText() {
		return 'Zaktualizuj schemat bazy BEZ.';
	}
	
	function __construct() {
		$this->connect = new Connect();
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
						$this->connect->errquery("UPDATE causes SET potential=1 WHERE cause=$cid");
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
	
	private $actions = array(
				
				array('1. Słownik kategorii przyczyn', check_rootcause, do_rootcause),
				array('2. Słownik typów problemów', check_types, do_types),
				array('3. Usunięcie podmiotu w problemach', check_rementity, do_rementity),
				array('4. Kolumna "potential" w zadaniach', check_potentials, do_potentials),
				array('5. Zadania przypisane do przczyn', check_causetask, do_causetask),
				);
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
	  	$fname = $this->actions[$pr_id][2];
	  	$this->$fname();
	  }
	}
	
	function create_backup() {
	}
	
	function restore_backup($name) {
	}
 
	/**
	 * output appropriate html
	 */
	function html() {
		global $errors;
	  ptln('<h1>'.$this->getMenuText().'</h1>');
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
	  	$is_applaied = $this->$action[1]();
		if ($is_applaied) ptln('<tr style="background-color: #0f0">');
		else ptln('<tr style="background-color: #f00">');
		
	  	ptln("<td>$name</td>");
	  	
	  	if ($is_applaied) ptln("<td>Zastosowana</td>");
		else ptln('<td><input type="submit" name="applay['.$i.']"  value="Zastosuj" /></td>');
		
	  	ptln('</tr>');
	  	$i++;
	  }
	  ptln('</table>');
	  ptln('</form>');
	}
 
}

