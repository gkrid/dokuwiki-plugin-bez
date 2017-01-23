<?php
 
if(!defined('DOKU_INC')) die();

require_once 'entity.php';

class BEZ_mdl_Issue extends BEZ_mdl_Entity {
	
	//meta
	protected $reporter, $date, $last_mod, $last_activity,
				$participants, $subscribents;
	
	//acl
	//coordinator is defined by issue or tasktype
	protected $coordinator;
	
	//data
	protected $title, $description, $type;
	
	//state
	protected $state, $opinion;
	
	//virtual
	protected $participants_array, $subscribents_array;
	
	public function get_columns() {
		return array('id',
					'reporter', 'date', 'last_mod', 'last_activity',
					'participants', 'subscribents', 'coordinator',
					'title', 'description', 'type',
					'state', 'opinion');
	}
	
	public function get_virtual_columns() {
		return array('participants_array', 'subscribents_array');
	}

	
	public function __construct($model, $defaults=array()) {
		parent::__construct($model);
		if ($this->participants !== NULL) {
			$exp_part = explode(',', $this->participants);
			foreach ($exp_part as $participant) {
				$this->participants_array[$participant] = $participant;
			}
		}
		if ($this->subscribents !== NULL) {
			$exp_part = explode(',', $this->subscribents);
			foreach ($exp_part as $subscribent) {
				$this->subscribents_array[$subscribent] = $subscribent;
			}
		}
	}
	
	public function update_last_activity() {
		$this->last_activity = $this->sqlite_date();
	}
	
	public function add_participant($participant) {
		if (! (	$this->auth->get_level() >= 15 ||
				$participant === $this->auth->get_user())
			) {
			throw new Exception('no permission');
		}
		if (!$this->model->users->exists($participant)) {
			throw new Exception($participant. ': not a dokuwiki user');
		}
		$this->participants_array[$participant] = $participant;
		$this->participants = implode(',', $this->participants_array);
	}
	
	public function add_subscribent($subscribent) {
		if (! (	$this->auth->get_level() >= 15 ||
				$subscribent === $this->auth->get_user())
			) {
			throw new Exception('no permission');
		}
		if (!$this->model->users->exists($subscribent)) {
			throw new Exception($subscribent. ': not a dokuwiki user');
		}
		$this->subscribents_array[$subscribent] = $subscribent;
		$this->subscribents = implode(',', $this->subscribents_array);
	}
	
	public function remove_subscribent($subscribent) {
		if (! (	$this->auth->get_level() >= 15 ||
				$subscribent === $this->auth->get_user())
			) {
			throw new Exception('no permission');
		}
		unset($this->subscribents_array[$subscribent]);
		$this->subscribents = implode(',', $this->subscribents_array);
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
	
	public function is_subscribent($user=NULL) {
		if ($user === NULL) {
			$user = $this->auth->get_user();
		}
		if (in_array($user, $this->subscribents_array)) {
			return true;
		}
		return false;
	}
}
