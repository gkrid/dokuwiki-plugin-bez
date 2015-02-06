<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/event.php";

class Comments extends Event {
	public function __construct() {
		global $errors;
		parent::__construct();
		$q = "CREATE TABLE IF NOT EXISTS comments (
				id INTEGER PRIMARY KEY,
				content TEXT NOT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				issue INTEGER NOT NULL)";
		$this->errquery($q);
	}
	public function can_modify($comment_id) {
		global $INFO;

		$comment = $this->getone($comment_id);

		if ($comment && $this->issue->opened($comment['issue']))
			if ($comment['reporter'] == $INFO['client'] || $this->helper->user_coordinator($comment['issue'])) 
				return true;

		return false;
	}
	public function can_add($issue_id) {
		return $this->helper->user_editor() && $this->issue->opened($issue_id);
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
		if ($this->can_add($data['issue'])) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$this->errinsert($data, 'comments');
			$this->issue->update_last_mod($data['issue']);
		}
	}
	public function update($post, $data, $id) {
		if ($this->can_modify($id)) {
			$from_user = $this->validate($post);
			$data = array_merge($data, $from_user);
			$this->errupdate($data, 'comments', $id);

			$comment = $this->getone($id);
			$this->issue->update_last_mod($comment['issue']);
		}
	}
	public function delete($comment_id) {
		if ($this->can_modify($comment_id)) {
			$comment = $this->getone($comment_id);
			$this->errdelete('comments', $comment_id);
			$this->issue->update_last_mod($comment['issue']);
		}
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

