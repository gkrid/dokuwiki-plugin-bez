<?php

class Issuetypes {
	public function get() {
		global $bezlang;
		return array($bezlang['type_noneconformity'], $bezlang['type_complaint'], $bezlang['type_risk']);
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

