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
	public function id($name) {
		switch ($name) {
			case 'correction': return 0;
			case 'corrective_action': return 1;
			case 'preventive_action': return 2;
			default: return -1;
		}
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

