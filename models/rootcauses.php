<?php

class Rootcauses {
	public function get() {
		global $bezlang;
		return array(
			$bezlang['manpower'],
			$bezlang['method'],
			$bezlang['machine'],
			$bezlang['material'],
			$bezlang['managment'],
			$bezlang['measurement'],
			$bezlang['money'],
			$bezlang['environment']
		);
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

