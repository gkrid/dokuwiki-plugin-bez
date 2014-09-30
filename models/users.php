<?php

class Users {
	public function get_nick() {
		global $INFO;
		return $INFO['client'];
	}
	public function email($user) {
		global $auth;
		$data = $auth->getUserData($user);
		return $data['mail'];

	}

	public function name($user) {
		global $auth;
		$data = $auth->getUserData($user);
		return $data['name'];

	}

	public function can_edit() {
		global $INFO;
		global $auth;

		if ($auth->getUserData($INFO['client']) == true) {
				return true;
		} else {
			return false;
		}
	}

	public function is_coordinator($user=NULL) {
		global $INFO;
		global $auth;
		if ($user == NULL)
			$user = $INFO['client'];


		$data = $auth->getUserData($user);
		if ($data == false) {
			return false;
		} elseif (in_array('bez_moderator', $data['grps'])) {
			return true;	
		} elseif (in_array('admin', $data['grps'])) {
			return true;
		} else {
			return false;
		}
	}

	public function exists($user=NULL) {
		global $INFO;
		global $auth;

		$data = $auth->getUserData($user);
		if ($data == false) {
			return false;
		} else {
			return true;
		}
	}

	public function get() {
		global $auth;
		return $auth->retrieveUsers();
	}

	public function nicks() {
		return array_keys($this->get());
	}

	public function coordinators_nicks() {
		return array_keys($this->coordinators());
	}

	public function coordinators() {
		$mod = array();
		foreach ($this->get() as $k => $v)
			if ($this->is_coordinator($k))
				$mod[$k] = $v['name'];
		return $mod;
	}
}
