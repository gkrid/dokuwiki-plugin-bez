<?php
 
if(!defined('DOKU_INC')) die();

require_once 'auth.php';

class BEZ_mdl_Factory {
	protected $model, $auth;
	
	public function __construct($model) {
		$this->model = $model;
		$this->auth = new BEZ_mdl_Auth($this->model->dw_auth, $this->model->user_nick);
	}
	
	public function get_level() {
		return $this->auth->get_level();
	}
}
