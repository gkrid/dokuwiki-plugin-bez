<?php
include_once DOKU_PLUGIN."bez/models/connect.php";

class Comments extends Connect {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = <<<EOM
CREATE TABLE IF NOT EXISTS comments (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	content TEXT NOT NULL,
	reporter CHAR(100) NOT NULL,
	date INT(11) NOT NULL,
	issue INT(11) NOT NULL,

	PRIMARY KEY (id)
)
EOM;
	$this->errquery($q);
	}
	public function add($post, $data=array())
	{
		global $bezlang, $errors;

		$content_max = 65000;

		$post['content'] = trim($post['content']);
		if (strlen($post['content']) == 0) 
			$errors['content'] = $bezlang['vald_content_required'];
		else if (strlen($post['content']) > $content_max)
			$errors['content'] = str_replace('%d', $content_max, $bezlang['vald_content_too_long']);

		$this->errinsert($data);
	}
	public function lastid()
	{
		return $this->db->insert_id;
	}
	public function get($issue) {
		$issue = (int) $issue;
		return $this->fetch_assoc("SELECT * FROM comments WHERE issue=$issue");
	}
}

