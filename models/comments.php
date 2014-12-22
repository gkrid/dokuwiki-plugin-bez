<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/issues.php";

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
	public function can_modify($comment_id) {
		global $INFO;

		$helper = plugin_load('helper', 'bez');
		$comment = $this->getone($comment_id);

		if ($comment)
			if ($comment['reporter'] == $INFO['client'] || $helper->user_coordinator($comment['issue']) || $helper->user_admin()) 
				return true;

		return false;
	}
	public function can_add() {
		$helper = plugin_load('helper', 'bez');
		return $helper->user_editor();
	}
	public function validate($post) {
		global $bezlang, $errors;

		$content_max = 65000;

		$post['content'] = trim($post['content']);
		if (strlen($post['content']) == 0) 
			$errors['content'] = $bezlang['vald_content_required'];
		else if (strlen($post['content']) > $content_max)
			$errors['content'] = str_replace('%d', $content_max, $bezlang['vald_content_too_long']);

		$data['content'] = $post['content'];

		return $data;
	}
	public function add($post, $data=array())
	{
		if ($this->can_add()) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);

			$this->errinsert($data, 'comments');
		}
	}
	public function update($post, $data, $id) {
		if ($this->can_modify($id)) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$this->errupdate($data, 'comments', $id);
		}
	}
	public function delete($comment_id) {
		if ($this->can_modify($comment_id))
			$this->errdelete('comments', $comment_id);
	}
	public function getone($id) {
		$id = (int) $id;
		$comment = $this->fetch_assoc("SELECT * FROM comments WHERE id=$id");

		if ($comment)
			return $comment[0];

		return NULL;
	}
	public function get($issue) {
		$issue = (int) $issue;

		$a = $this->fetch_assoc("SELECT * FROM comments WHERE issue=$issue");

		$usro = new Users();
		foreach ($a as &$row) {
			$row['reporter_nick'] = $row['reporter'];
			$row['reporter'] = $usro->name($row['reporter']);
		}

		return $a;
	}
}

