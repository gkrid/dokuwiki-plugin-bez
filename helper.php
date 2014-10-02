<?php

if(!defined('DOKU_INC')) die();

class helper_plugin_bez extends dokuwiki_plugin
{
	public function user_can_view() {
		global $auth, $INFO;

		if ($auth->getUserData($INFO['client']) == true) {
			return true;
		} else {
			return false;
		}
	}

	public function wiki_parse($content) {
		$info = array();
		return p_render('xhtml',p_get_instructions($content), $info);
	}

	public function html_issue_link($id) {
		return '<a href="?id=bez:bds_issue_show:'.$id.'">#'.$id.'</a>';
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
