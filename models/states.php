<?php

class States {
	public function get() {
		global $bezlang;
		return array(	$bezlang['state_proposal'],
						$bezlang['state_opened'],
						$bezlang['state_rejected'],
						$bezlang['state_closed']
					);
	}
	public function id($name) {
		switch ($name) {
			case 'proposal': return 0;
			case 'opened': return 1;
			case 'rejected': return 2;
			case 'closed': return 3;
			default: return -1;
		}
	}
	public function name($id) {
		$a = $this->get();
		return $a[$id];
	}
}

