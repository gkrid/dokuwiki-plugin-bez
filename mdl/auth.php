<?php
 
if(!defined('DOKU_INC')) die();

require_once 'issue.php';
require_once 'task.php';

/*
 * 12:bez7:start#macierz_przypadkow_uzycia
 */
class BEZ_mdl_Auth {
	/*
	 * 0 - brak uprawnień
	 * 5 - komentator
	 * 10 - wykonawca (executor)
	 * 15 - koordynator (coordinaotr)
	 * 20 - administrator
	 */
	private $dw_auth, $user_nick, $level = 0;
	
	private $coordinator, $program_coordinator, $executor;
	
	private function update_level($level) {
		if ($level > $this->level) {
			$this->level = $level;
		}
	}
		
	public function __construct($dw_auth, $user_nick) {
		$this->dw_auth = $dw_auth;
		$this->user_nick = $user_nick;
		
		$userd = $this->dw_auth->getUserData($this->user_nick); 
		if ($userd !== false && is_array($userd['grps'])) {
			$grps = $userd['grps'];
			if (in_array('admin', $grps ) || in_array('bez_admin', $grps )) {
				$this->update_level(20);
			} else {
				$this->update_level(5);
			}
		}
	}
	
	public function get_level() {
		return $this->level;
	}
	
	public function get_user() {
		return $this->user_nick;
	}
	
	public function set_executor($executor) {
		$this->executor = $executor;
		if ($this->executor === $this->user_nick) {
			$this->update_level(10);
		}
	}
	
	//special coordinator - allows all user to edit 
	public function set_coordinator($coordinator) {
		$this->coordinator = $coordinator;
		if ($this->coordinator === $this->user_nick) {
			$this->update_level(15);
		}
	}
}
