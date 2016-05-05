<?php

class Taskactions {
	public function get() {
		global $bezlang;
		return array(
			$bezlang['correction'],
			$bezlang['corrective_action'],
			$bezlang['preventive_action'],
			$bezlang['programme']
		);
	}
	public function get_with_cause() {
		global $bezlang;
		return array(
			1 => $bezlang['corrective_action'],
			2 => $bezlang['preventive_action']
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
	public function map_8d($name) {
		switch ($name) {
			case 0: return '3d';
			case 1: return '5d';
			case 2: return '6d';
			default: return '';
		}
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

