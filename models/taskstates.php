<?php

class Taskstates {
	public function get() {
		global $bezlang;
		return array(	
				$bezlang['task_opened'],
				$bezlang['task_done'],
				$bezlang['task_rejected']
			);
	}
	public function id($name) {
		switch ($name) {
			case 'opened': return 0;
			case 'done': return 1;
			case 'rejected': return 2;
			default: return -1;
		}
	}
	public function close_states() {
		return array(1, 2);
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

