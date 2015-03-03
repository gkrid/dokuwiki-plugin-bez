<?php
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
$errors = array();
include_once DOKU_PLUGIN."bez/models/connect.php";
class admin_plugin_bez extends DokuWiki_Admin_Plugin {

	private $exp = false;
 
	function getMenuText() {
		return 'Zaimportuj bazę starego BEZa do nowego.';
	}
 
	/**
	 * handle user request
	 */
	function handle() {
		global $errors;
 
	  if (!isset($_REQUEST['run'])) return;   // first time - nothing to do
	  if (!checkSecurityToken()) return;
	  //importuj

	  $this->exp = true;
	  $sqlite = new Connect();

		$db_name = preg_replace('/[^a-zA-Z0-9]/', '', $_SERVER['SERVER_NAME']);

		$m = new MongoClient();
		$mongo = $m->selectDB($db_name);

		$cursor = $mongo->issues->find();
		$entities = array();
		  $sqlite->errquery("DELETE FROM causes");
		  $sqlite->errquery("DELETE FROM comments");
		  $sqlite->errquery("DELETE FROM entities");
		  $sqlite->errquery("DELETE FROM issues");
		  $sqlite->errquery("DELETE FROM tasks");
		  $sqlite->errquery("DELETE FROM tokens");
		foreach ($cursor as $v) {
			$id = $v['_id'];
			$priority = 1;
			$title = $v['title'];
			$description = $v['description'];
			$coordinator = $v['coordinator'];
			if ($v['state'] == 0) {
				$state = 0;
				$coordinator = '-proposal';
			} else if ($v['state'] == 1) {
				$state = 0;
			} else if ($v['state'] == 2) {
				$state = 0;
				$coordinator = '-rejected';
			} else {
				$state = 1;
			}

			$opinion = $v['opinion'];
			switch($v['type']) {
				//niezgodność
				case 0:
					$type = 0;
					break;
				//reklamacja
				case 1:
					$type = 1;
					break;
				//ryzyko
				case 2:
					$type = 3;
					break;
			}
			$entity = $v['entity'];
			$reporter = $v['reporter'];
			$date = $v['date'];
			$last_mod = $v['last_mod_date'];

			$q = "INSERT INTO issues (id, priority,title,description,state,opinion,type,entity,coordinator,reporter,date,last_mod)
				VALUES ($id, $priority, '".$sqlite->escape($title)."', '".$sqlite->escape($description)."', $state, '$opinion', $type,
				'$entity', '$coordinator', '$reporter', $date, $last_mod)";

			$sqlite->errquery($q);
			if (count($errors) > 0)
				return;

			foreach ($v['events'] as $e) {
				$reporter = $e['author'];
				$date = $e['date'];
				$issue = $v['_id'];
				if ($e['type'] == 'comment') {
					if (isset($e['root_cause']) && $e['root_cause'] != 0) {
						$cause = $e['content'];
						$rootcause = (int)$e['root_cause']-1;
						$q = "INSERT INTO causes(cause,rootcause,reporter,date,issue)
							VALUES ('".$sqlite->escape($cause)."',$rootcause,'$reporter',$date,$issue)";
					} else {
						$content = $e['content'];
						$q = "INSERT INTO comments(content,reporter,date,issue)
							VALUES ('".$sqlite->escape($content)."','$reporter',$date,$issue)";
					}
					$sqlite->errquery($q);
					if (count($errors) > 0)
						return;
				} else if ($e['type'] == 'task') {
					$task = $e['content'];
					$state = $e['state'];
					$executor = $e['executor'];
					$action = $e['class'];
					$cost = (int)$e['cost'];
					$reason = $e['reason'];
					$close_date = $e['last_mod_date'];
					$q = "INSERT INTO tasks(task,state,executor,action,cost,reason,reporter,date,close_date,issue)
						VALUES ('".$sqlite->escape($task)."',$state,'$executor',$action,$cost,
						'".$sqlite->escape($reason)."','$reporter',$date,$close_date,$issue)";
					$sqlite->errquery($q);
					if (count($errors) > 0)
						return;
				}
			}

			$entities[] = $entity;
			
		}
		foreach (array_unique($entities) as $ent) {
			$ent  = strtr($ent, 'ąćęłńóśżźĄĆĘŁŃÓŚŻŹ', 'acelnoszzACELNOSZZ'); 
			$q = "INSERT INTO entities(entity) VALUES ('".$sqlite->escape($ent)."')";
			$sqlite->errquery($q);
			if (count($errors) > 0)
				return;
		}
	}
 
	/**
	 * output appropriate html
	 */
	function html() {
		global $errors;
	  ptln('<h1>'.$this->getMenuText().'</h1>');
	  if ($this->exp == true) {
		  ptln('<div class="success">Baza zaimportowana poprawnie</div>');
	  } else {
	  		if (is_array($errors))
				foreach ($errors as $error) {
					echo '<div class="error">';
					echo $error;
					echo '</div>';
				}
		  ptln('<div class="error"><b>Uwaga!</b> Ta operacja usunie wszystkie dane z NOWEGO BEZa.</div>');
	  }
 
	  ptln('<form action="'.wl($ID).'" method="post">');
	  // output hidden values to ensure dokuwiki will return back to this plugin
	  ptln('  <input type="hidden" name="do"   value="admin" />');
	  ptln('  <input type="hidden" name="page" value="'.$this->getPluginName().'" />');
	  formSecurityToken();
 
	  ptln('  <input type="submit" name="run"  value="Importuj" />');
	  ptln('</form>');
	}
 
}

