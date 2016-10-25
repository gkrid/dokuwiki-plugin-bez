<?php
 
if(!defined('DOKU_INC')) die();

require_once 'issue.php';
require_once 'task.php';

/*
 * 12:bez7:start#macierz_przypadkow_uzycia
 */
class BEZ_mdl_Auth {
	private $dw_auth;
	/*
	 * 0 - brak uprawnień
	 * 5 - komentator
	 * 10 - wykonawca (exector)
	 * 15 - koordynator (coordinaotr)
	 * 20 - administrator
	 */
	private $user_nick, $level;
	
	private $coordinator, $executor;
	private function getAbsoluteLevel() {
		$userd = $this->dw_auth->getUserData($this->user_nick); 

		if ($userd === false) {
			return 0;
		}

		$grps = $userd['grps'];
		if (in_array('admin', $grps ) || in_array('bez_admin', $grps )) {
			return 20;
		}
		
		return 5;
	}
		
	public function __construct($dw_auth, $user_nick) {
		$this->user_nick = $user_nick;
		$this->dw_auth = $dw_auth;
		
		$this->level = $this->getAbsoluteLevel();
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
			$this->level = 10;
		}
	}
	
	public function set_coordinator($coordinator) {
		$this->coordinator = $coordinator;
		if ($this->coordinator === $this->user_nick) {
			$this->level = 15;
		}
	}

	
	
	public function zgloszenie_problemu() {
		if ($this->level >= 5) {
			return true;
		}
		return false;
	}
	
	public function komentarz_do_problemu() {
		if ($this->level >= 5) {
			return true;
		}
		return false;
	}
	
	public function otwarcie_zadania($issue) {
		if ($issue->coordinator === $this->user_nick) {
		}
		
		if ($this->level >= 15) {
			return true;
		}
		return false;
	}
	
	public function zamkniecie_zadania($task) {
		$this->updateRelatieveLevel($issue, $task);
		
		if ($this->level >= 15) {
			return true;
		}
		return false;
	}
	

}
