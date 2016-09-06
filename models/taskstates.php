<?php

class Taskstates {
	public function get() {
		global $bezlang;
		return array(	
				'0' => $bezlang['task_opened'],
				'-outdated' => $bezlang['task_outdated'],
				'1' => $bezlang['task_done'],
				'2' => $bezlang['task_rejected']
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

