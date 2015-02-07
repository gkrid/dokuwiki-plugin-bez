<?php

class Issuetypes {
	public function get() {
		global $bezlang;
		return array(
			$bezlang['type_noneconformity_internal'],
			$bezlang['type_noneconformity_customer'],
			$bezlang['type_noneconformity_supplier'],
			$bezlang['type_threat'],
			$bezlang['type_opportunity']
		);
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

