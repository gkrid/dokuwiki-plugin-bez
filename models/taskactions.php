<?php

class Taskactions {
	public function get() {
		global $bezlang;
		return array(
			$bezlang['correction'],
			$bezlang['corrective_action'],
			$bezlang['preventive_action']
		);
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

