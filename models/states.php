<?php

class States {
	public function get() {
		global $bezlang;
		return array(	
						$bezlang['state_opened'],
						$bezlang['state_closed']
					);
	}
	public function id($name) {
		switch ($name) {
			case 'opened': return 0;
			case 'closed': return 1;
			default: return -1;
		}
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

