<?php
include_once DOKU_PLUGIN."bez/models/connect.php";

class Issuetypes extends Connect {
	public function get() {
		global $bezlang;
		return array($bezlang['type_noneconformity'], $bezlang['type_complaint'], $bezlang['type_risk']);
	}
}

