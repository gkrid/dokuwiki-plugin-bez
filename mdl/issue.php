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
	protected $participants_array, $subscribents_array,
				$assigned_tasks_count, $opened_tasks_count,
				$priority;
	
	protected $parse_int = array('assigned_tasks_count', 'opened_tasks_count');
	
	public function get_columns() {
		return array('id',
					'reporter', 'date', 'last_mod', 'last_activity',
					'participants', 'subscribents', 'coordinator',
					'title', 'description', 'description_cache', 'type',
					'state', 'opinion', 'opinion_cache');
	}
	
	public function get_virtual_columns() {
		return array('participants_array', 'subscribents_array',
					'assigned_tasks_count',	'opened_tasks_count',
					'priority');
	}

	
	public function __construct($model, $defaults=array()) {
		parent::__construct($model);
		
		$this->validator->set_rules(array(
			'title' => array(array('length', 200), 'NOT NULL'),
			'description' => array(array('length', 10000), 'NOT NULL'),
			'state' => array(array('select', array('0', '1', '2')), 'NULL'),
			'opinion' => array(array('length', 10000), 'NOT NULL'),
			'type' => array(array('numeric'), 'NULL'),
			'coordinator' => array(array('dw_user', array('-proposal')), 'NOT NULL'),
			'reporter' => array(array('dw_user'), 'NOT NULL'),
			'date' => array(array('unix_timestamp'), 'NOT NULL'),
			'last_mod' => array(array('unix_timestamp'), 'NULL'),
			'last_activity' => array(array('sqlite_datetime'), 'NOT NULL')
		));
		
		//we've created empty object
		if ($this->id === NULL) {
			$this->reporter = $this->auth->get_user();
			$this->date = time();
			
			$this->last_activity = $this->sqlite_date();
			
			$this->state = '0';
			
			$input = array('title', 'description');
			if ($this->auth->get_level() >= 20) {
				$input[] = 'coordinator';
			}
			
			$val_data = $this->validator->validate($defaults, $input);
			
			if ($val_data === false) {
				throw new ValidationException('issues', $this->validator->get_errors());
			}
			
			$this->title = $val_data['title'];
			$this->description = $val_data['description'];
			$this->description_cache = $this->helper->wiki_parse($this->description);
			
			$this->add_participant($this->reporter);
			$this->add_subscribent($this->reporter);
			
			if ($this->auth->get_level() >= 20) {
				$this->coordinator = $val_data['coordinator'];
				if ($val_data['coordinator'] !== '-proposal') {
					$this->add_participant($val_data['coordinator']);
					$this->add_subscribent($val_data['coordinator']);
				}
			} else {
				$this->coordinator = '-proposal';
			}
		}
		
		$this->auth->set_coordinator($this->coordinator);
		
		$this->participants_array = array();
		if ($this->participants !== NULL) {
			$exp_part = explode(',', $this->participants);
			foreach ($exp_part as $participant) {
				$this->participants_array[$participant] = $participant;
			}
		}
		
		$this->subscribents_array = array();
		if ($this->subscribents !== NULL) {
			$exp_part = explode(',', $this->subscribents);
			foreach ($exp_part as $subscribent) {
				$this->subscribents_array[$subscribent] = $subscribent;
			}
		}
	}
	
	public function set_data($data) {
		if ($this->auth->get_level() < 15) {
			throw new Exception('no permission');
		}

		$input = array('title', 'description', 'opinion', 'type');
		if ($this->auth->get_level() >= 20) {
			$input[] = 'coordinator';
		}
		$val_data = $this->validator->validate($data, $input); 
		if ($val_data === false) {
			throw new ValidationException('issues',	$this->validator->get_errors());
		}
		
		foreach ($val_data as $k => $v) {
			$this->$k = $v;
		}
		
		//!!! don't update activity on issue update
		
		$this->description_cache = $this->helper->wiki_parse($this->description);
		$this->opinion_cache = $this->helper->wiki_parse($this->opinion);
	}
	
	public function set_state($data) {
		if ($this->auth->get_level() < 15) {
			throw new Exception('no permission');
		}

		$input = array('opinion', 'state');
		$val_data = $this->validator->validate($data, $input); 
		if ($val_data === false) {
			throw new ValidationException('issues', $this->validator->get_errors());
		}

		
		foreach ($val_data as $k => $v) {
			$this->$k = $v;
		}
		
		//update activity on state update
		$this->last_mod = time();
		$this->update_last_activity();
		$this->opinion_cache = $this->helper->wiki_parse($this->opinion);
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
		if ($this->model->users->exists($participant)) {
			$this->participants_array[$participant] = $participant;
			$this->participants = implode(',', $this->participants_array);
		}
	}
	
	public function add_subscribent($subscribent) {
		if (! (	$this->auth->get_level() >= 15 ||
				$subscribent === $this->auth->get_user())
			) {
			throw new Exception('no permission');
		}
		if ($this->model->users->exists($subscribent)) {
			$this->subscribents_array[$subscribent] = $subscribent;
			$this->subscribents = implode(',', $this->subscribents_array);
		}
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
	
	public function get_participants() {
		$full_names = [];
		foreach ($this->participants_array as $par) {
			$name = $this->model->users->get_user_full_name($par);
			if ($name == '') {
				$full_names[$par] = $par;
			} else {
				$full_names[$par] = $name;
			}
		}
		//coordinator on top
		uksort($full_names, function ($a, $b) use($full_names) {
			if ($a === $this->coordinator) {
				return -1;
			} else if ($b === $this->coordinator) {
				return 1;
			}
			return $full_names[$a] > $full_names[$b];
		});
		
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
	
	public function is_task_executor($user=NULL) {
		if ($user === NULL) {
			$user = $this->auth->get_user();
		}
		$sth = $this->model->db->prepare('SELECT COUNT(*) FROM tasks
										WHERE issue=:issue AND executor=:executor');
		$sth->execute(array(':issue' => $this->id, ':executor' => $user));
		$fetch = $sth->fetch();
		if ($fetch[0] === '0') {
			return false;
		} else {
			return true;
		}
	}
	
	public function is_commentator($user=NULL) {
		if ($user === NULL) {
			$user = $this->auth->get_user();
		}
		$sth = $this->model->db->prepare('SELECT COUNT(*) FROM commcauses
										WHERE issue=:issue AND reporter=:reporter');
		$sth->execute(array(':issue' => $this->id, ':reporter' => $user));
		$fetch = $sth->fetch();
		if ($fetch[0] === '0') {
			return false;
		} else {
			return true;
		}
	}
		
	public function causes_without_tasks_count() {
		$sth = $this->model->db->prepare('SELECT COUNT(*) FROM
		(SELECT commcauses.id, COUNT(*) as cnt 
			FROM commcauses LEFT JOIN tasks ON commcauses.id = tasks.cause
			WHERE commcauses.type > 0 AND commcauses.issue = ?
			GROUP BY commcauses.id) WHERE cnt = 1');
		$sth->execute(array($this->id));
		$fetch = $sth->fetch();
		
		return $fetch[0];
	}
}
