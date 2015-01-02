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
	public function closed($name) {
		$states = $this->get();
		$key = array_search($name, $states);
		return $key == 1;
	}
	/*pobierz nazwę stanu, uwzględniając -proposal i -rejected*/
	public function name($id, $coordinator) {
		global $bezlang;
		switch($coordinator) {
			case '-proposal': return $bezlang['state_proposal'];
			case '-rejected': return $bezlang['state_rejected'];
			default:
				$a = $this->get();
				return $a[$id];
		}
	}

	public function open() {
		return 0;
	}
	
	public function rejected() {
		return 2;
	}
}

