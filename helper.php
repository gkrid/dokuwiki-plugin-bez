<?php

if(!defined('DOKU_INC')) die();

class helper_plugin_bez extends dokuwiki_plugin
{
	public function get_user_email($user) {
		global $auth;
		$data = $auth->getUserData($user);
		return $data['mail'];

	}

	public function get_user_name($user) {
		global $auth;
		$data = $auth->getUserData($user);
		return $data['name'];

	}

	public function user_can_edit() {
		global $INFO;
		global $auth;

		if ($auth->getUserData($INFO['client']) == true) {
				return true;
		} else {
			return false;
		}
	}
	public function user_can_view() {
		global $INFO;
		global $auth;

		if ($auth->getUserData($INFO['client']) == true) {
			return true;
		} else {
			return false;
		}
	}
	public function user_is_moderator($user=NULL) {
		global $INFO;
		global $auth;
		if ($user == NULL)
			$user = $INFO['client'];


		$data = $auth->getUserData($user);
		if ($data == false) {
			return false;
		/* Obecnie: bez_moderator */
		} elseif (in_array('bez_moderator', $data['grps'])) {
			return true;	
		} elseif (in_array('admin', $data['grps'])) {
			return true;
		} else {
			return false;
		}
	}
	public function user_exists($user=NULL) {
		global $INFO;
		global $auth;

		$data = $auth->getUserData($user);
		if ($data == false) {
			return false;
		} else {
			return true;
		}
	}
	public function retrieveUsers() {
		global $auth;
		return $auth->retrieveUsers();
	}
	public function issue_types() {
		return array($this->getLang('type_noneconformity'), $this->getLang('type_complaint'), $this->getLang('type_risk'));
	}
}
