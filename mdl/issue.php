<?php
 
if(!defined('DOKU_INC')) die();

require_once 'entity.php';

class BEZ_mdl_Issue extends BEZ_mdl_Entity {
	
	//meta
	protected $reporter, $date, $last_mod, $last_activity, $participants;
	
	//acl
	//coordinator is defined by issue or tasktype
	protected $coordinator;
	
	//data
	protected $title, $description, $type;
	
	//state
	protected $state, $opinion;
	
	//virtual
	protected $participants_array;
	
	public function get_columns() {
		return array('id',
					'reporter', 'date', 'last_mod', 'last_activity', 'participants',
					'coordinator',
					'title', 'description', 'type',
					'state', 'opinion');
	}
	
	public function get_virtual_columns() {
		return array('participants_array');
	}

	
	public function __construct($model, $defaults=array()) {
		parent::__construct($model);
		if ($this->participants !== NULL) {
			$exp_part = explode(',', $this->participants);
			foreach ($exp_part as $participant) {
				$this->participants_array[$participant] = $participant;
			}
		}
	}
	
	public function update_last_activity() {
		//SQLITE format: https://www.sqlite.org/lang_datefunc.html
		$this->last_activity = date('Y-m-d H:i:s');
	}
	
	public function add_participant($participant) {
		if (!$this->model->users->exists($participant)) {
			return false;
		}
		$this->participants_array[$participant] = $participant;
		$this->participants = implode(',', $this->participants_array);
	}
	
	public function get_participants_names() {
		$full_names = [];
		foreach ($this->participants_array as $par) {
			$name = $this->model->users->get_user_full_name($par);
			if ($name == '') {
				$full_names[] = $par;
			} else {
				$full_names[] = $name;
			}
		}
		sort($full_names);
		return $full_names;
	}
}
