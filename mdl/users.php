<?php
 
if(!defined('DOKU_INC')) die();

class BEZ_mdl_Users {
	
	private $model;
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function get_all() {
		$wikiusers = $this->model->dw_auth->retrieveUsers();

		$a = array();
		foreach ($wikiusers as $nick => $data) {
			$a[$nick] = $data['name'];
        }
		asort($a);
		return $a;
	}
	
	public function exists($nick) {
        if (!is_string($nick)) {
            return false;
        }
        
		$users = $this->get_all();
		if (array_key_exists($nick, $users)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_user_full_name($nick) {
		$users = $this->get_all();
		return $users[$nick];
	}
	
	public function get_user_email($nick) {
		$wikiusers = $this->model->dw_auth->retrieveUsers();
		return $wikiusers[$nick]['mail'];
	}
    
    public function get_user_nick($full_name) {
        $users = $this->get_all();
        return array_search($full_name, $users);
    }
}
