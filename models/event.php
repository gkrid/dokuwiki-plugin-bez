<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/issues.php";

class Event extends Connect {
	protected $issue;
	public function __construct() {
		global $errors;
		parent::__construct();
		$this->issue = new Issues();
	}
}
