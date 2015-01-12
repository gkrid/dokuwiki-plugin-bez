<?php

class States {
	public function get() {
		global $bezlang;
		return array(	
						$bezlang['state_opened'],
						$bezlang['state_closed']
					);
	}
	public function get_all() {
			global $bezlang;
			$ret = $this->get();
			$ret['-proposal'] = $bezlang['state_proposal'];
			$ret['-rejected'] = $bezlang['state_rejected'];
			return $ret;
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
		$a = $this->get_all();
		if (strstr($coordinator, '-'))
			return $a[$coordinator];
		 else
			return $a[$id];
	}

	public function open() {
		return 0;
	}
	
	public function rejected() {
		return 2;
	}
	public function proposal() {
		return '-proposal';
	}
}

