<?php

if(!defined('DOKU_INC')) die();

include_once DOKU_PLUGIN."bez/models/issues.php";

class helper_plugin_bez extends dokuwiki_plugin
{
	public function user_viewer() {
		global $auth, $INFO;

		return true;

		if ($auth->getUserData($INFO['client']))
			return true;


		return false;
	}

	public function user_editor() {
		global $INFO, $auth;

		if ($auth->getUserData($INFO['client']))
			return true;

		return false;
	}

	public function user_coordinator($issue_id) {
		global $INFO;
		global $bezlang, $errors;

		if (self::user_admin())
			return true;

		if (self::user_editor()) {
			$issuo = new Issues();
			$issue = $issuo->get($issue_id);
			if (count($errors) > 0 && $issue['coordinator'] == $INFO['client'])
				return true;
		}
		return false;
	}

	public function user_admin() {
		global $INFO, $auth;

		$userd = $auth->getUserData($INFO['client']); 
		if ($userd && in_array('admin', $userd['grps']))
				return true;

		return false;
	}

	public function wiki_parse($content) {
		$info = array();
		return p_render('xhtml',p_get_instructions($content), $info);
	}

	public function html_issue_link($id) {
		return '<a href="?id=bez:issue_show:'.$id.'">#'.$id.'</a>';
	}

	public function string_time_to_now($value) {
		$diff = time() - $value;
		if ($diff < 5) {
			return $this->getLang('just_now');
		}
		$time_str = '';
		$minutes = floor($diff/60);
		if ($minutes > 0) {
			$hours = floor($minutes/60);
			if ($hours > 0) {
				$days = floor($hours/24);
				if ($days > 0) {
					$time_str = $days.' '.$this->getLang('days');
				} else {
					$time_str = $hours.' '.$this->getLang('hours');
				}
			} else {
				$time_str = $minutes.' '.$this->getLang('minutes');
			}
		} else {
			$time_str = $diff.' '.$this->getLang('seconds');
		}
		$time_str .= ' '.$this->getLang('ago');
		return $time_str;
	}
}
