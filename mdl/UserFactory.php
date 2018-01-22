<?php

namespace dokuwiki\plugin\bez\mdl;

class UserFactory {
	
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
    
    private function get_hidden_groups() {
		$groups_s = $this->model->action->getConf('hidden_groups');
		$groups = explode(',', $groups_s);
        
		foreach($groups as &$group) {
            $group = trim($group);
        }
        
		return $groups;
	}
    
    public function get_groups() {
		global $auth;
		$wikiusers = $auth->retrieveUsers();
		$groups = array();
		foreach ($wikiusers as $data) {
			$groups = array_merge($groups, $data['grps']);
		}
		$groups = array_unique($groups);
        
		$groups = array_diff($groups, $this->get_hidden_groups());
		
		sort($groups);
		
		return $groups;
	}
    
    public function users_of_group($groups) {
		$wikiusers = $this->model->dw_auth->retrieveUsers();
		if (is_string($groups)) {
		    $groups = array($groups);
        }

		return array_keys(array_filter($wikiusers, function($data) use ($groups) {
           return count(array_intersect($groups, $data['grps'])) > 0;
        }));
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
		return isset($users[$nick]) ? $users[$nick] : '';
	}
	
	public function get_user_email($nick) {
		$wikiusers = $this->model->dw_auth->retrieveUsers();
		return $wikiusers[$nick]['mail'];
	}
}
