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

	public function is_coordinator($issue_id, $user) {
		global $auth, $errors;

		$data = $auth->getUserData($user);
		if ($data) {
			if (in_array('admin', $data['grps']))
				return true;

			$issuo = new Issues();
			$issue = $issuo->get($issue_id);
			if (count($errors) > 0 && $issue['coordinator'] == $INFO['client'])
				return true;
		}
		return false;
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
		$wikiusers = $auth->retrieveUsers();
		$a = array();
		foreach ($wikiusers as $nick => $data)
			$a[$nick] = $data['name'];
		asort($a);
		return $a;
	}

	public function nicks() {
		return array_keys($this->get());
	}
	

	public function groups() {
		global $auth;
		$wikiusers = $auth->retrieveUsers();
		$groups = array();
		foreach ($wikiusers as $data) {
			$groups = array_merge($groups, $data['grps']);
		}
		$groups = array_unique($groups);
		
		$helper = plugin_load('helper', 'bez');
		$hidden = $helper->get_hidden_groups();
		
		$groups = array_diff($groups, $hidden);
		
		sort($groups);
		
		return $groups;
	}
	
	public function users_of_group($group) {
		global $auth;
		
		$wikiusers = $auth->retrieveUsers();
		$users = array();
		foreach ($wikiusers as $nick => $data)
			if (in_array($group, $data['grps']))
				$users[] = $nick;

		return $users;
	}
}
