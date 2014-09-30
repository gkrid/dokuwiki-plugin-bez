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
}
