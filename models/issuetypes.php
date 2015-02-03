<?php

class Issuetypes {
	public function get() {
		global $bezlang;
		return array($bezlang['type_noneconformity'], $bezlang['type_complaint'], $bezlang['type_threat'], $bezlang['type_opportunity']);
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

