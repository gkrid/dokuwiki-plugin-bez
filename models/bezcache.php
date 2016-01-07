<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";
include_once DOKU_PLUGIN."bez/models/causes.php";

class Bezcache extends Connect {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = "CREATE TABLE IF NOT EXISTS tasks_cache (
				id INTEGER PRIMARY KEY,
				task TEXT NOT NULL,
				reason TEXT NULL,
				toupdate INTEGER DEFAULT 0)";
		$this->errquery($q);
		$q = "CREATE TABLE IF NOT EXISTS issues_cache (
				id INTEGER PRIMARY KEY,
				description TEXT NOT NULL,
				opinion TEXT NULL,
				toupdate INTEGER DEFAULT 0)";
		$this->errquery($q);
		$q = "CREATE TABLE IF NOT EXISTS comments_cache (
				id INTEGER PRIMARY KEY,
				content TEXT NOT NULL,
				toupdate INTEGER DEFAULT 0)";
		$this->errquery($q);
		
		$q = "CREATE TABLE IF NOT EXISTS causes_cache (
				id INTEGER PRIMARY KEY,
				cause TEXT NOT NULL,
				toupdate INTEGER DEFAULT 0)";
		$this->errquery($q);
	}

	public function get_task($id) {
		$id = (int)$id;
		$a = $this->fetch_assoc("SELECT * FROM tasks_cache WHERE id=$id");
		if (count($a) == 0 || $a[0]['toupdate'] == 1) {
			$tasko = new Tasks();
			$t = $tasko->getone($id);
			$task = $this->helper->wiki_parse($t['task']);
			$reason = $this->helper->wiki_parse($t['reason']);
			$this->errquery("REPLACE INTO tasks_cache(id, task,reason,toupdate)
							VALUES ($id, '".$this->escape($task)."', '".$this->escape($reason)."', 0)");
			return array('task' => $task, 'reason' => $reason);
		} 
		return array('task' => $a[0]['task'], 'reason' => $a[0]['reason']);
	}

	public function task_toupdate($id) {
		$id = (int)$id;
		$this->errquery("UPDATE tasks_cache SET toupdate=1 WHERE id=$id");
	}
	
	public function get_comment($id) {
		$id = (int)$id;
		$a = $this->fetch_assoc("SELECT * FROM comments_cache WHERE id=$id");
		if (count($a) == 0 || $a[0]['toupdate'] == 1) {
			$commo = new Comments();
			$c = $commo->getone($id);
			$content = $this->helper->wiki_parse($c['content']);
			$this->errquery("REPLACE INTO comments_cache(id, content,toupdate)
							VALUES ($id, '".$this->escape($content)."', 0)");
			return $content;
		} 
		return $a[0][content];
	}

	public function comment_toupdate($id) {
		$id = (int)$id;
		$this->errquery("UPDATE comments_cache SET toupdate=1 WHERE id=$id");
	}
	
	public function get_cause($id) {
		$id = (int)$id;
		$a = $this->fetch_assoc("SELECT * FROM causes_cache WHERE id=$id");
		if (count($a) == 0 || $a[0]['toupdate'] == 1) {
			$causo = new Causes();
			$c = $causo->getone($id);
			$cause = $this->helper->wiki_parse($c['cause']);
			$this->errquery("REPLACE INTO causes_cache(id, cause ,toupdate)
							VALUES ($id, '".$this->escape($cause)."', 0)");
			return $cause;
		} 
		return $a[0][cause];
	}

	public function cause_toupdate($id) {
		$id = (int)$id;
		$this->errquery("UPDATE causes_cache SET toupdate=1 WHERE id=$id");
	}	


	public function get_issue($id) {
		$id = (int)$id;
		$a = $this->fetch_assoc("SELECT * FROM issues_cache WHERE id=$id");
		if (count($a) == 0 || $a[0]['toupdate'] == 1) {
			$isso = new Issues();
			$i = $isso->get_clean($id);

			$description = $this->helper->wiki_parse($i['description']);
			$opinion = $this->helper->wiki_parse($i['opinion']);
			$this->errquery("REPLACE INTO issues_cache(id, description,opinion,toupdate)
							VALUES ($id, '".$this->escape($description)."', '".$this->escape($opinion)."', 0)");
			return array('description' => $description, 'opinion' => $opinion);
		} 
		return array('description' => $a[0]['description'], 'opinion' => $a[0]['opinion']);
	}

	public function issue_toupdate($id) {
		$id = (int)$id;
		$this->errquery("UPDATE issues_cache SET toupdate=1 WHERE id=$id");
	}	
}
