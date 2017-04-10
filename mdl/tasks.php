<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'task.php';

class BEZ_mdl_Tasks extends BEZ_mdl_Factory {
	private $select_query;
	
	public function __construct($model) {
		parent::__construct($model);
		$this->select_query = "SELECT tasks.*,
                tasktypes.".$this->model->conf['lang']." AS tasktype_string,

                (CASE
                    WHEN tasks.state = 0
                        THEN '".$this->model->action->getLang('task_opened')."'
                    WHEN tasks.state = 1
                        THEN '".$this->model->action->getLang('task_done')."'
                    WHEN tasks.state = 2
                        THEN '".$this->model->action->getLang('task_rejected')."'
                END) AS state_string,

                (CASE	WHEN tasks.issue IS NULL THEN '3'
                        WHEN tasks.cause IS NULL OR tasks.cause = '' THEN '0'
                        WHEN causes.potential = 0 THEN '1'
                        ELSE '2' END) AS action,

                (CASE
                    WHEN tasks.issue IS NULL
                        THEN '".$this->model->action->getLang('programme')."'
                    WHEN tasks.cause IS NULL OR tasks.cause = ''
                        THEN '".$this->model->action->getLang('correction')."'
                     WHEN causes.potential = 0
                        THEN '".$this->model->action->getLang('corrective_action')."'
                    ELSE
                        '".$this->model->action->getLang('preventive_action')."'
                END) AS action_string,

                (CASE WHEN tasks.issue IS NULL THEN '-none' 
                      ELSE issues.coordinator END) AS coordinator
                FROM tasks
                    LEFT JOIN tasktypes ON tasks.tasktype = tasktypes.id
                    LEFT JOIN causes ON tasks.cause = causes.id
                    LEFT JOIN issues ON tasks.issue = issues.id";
	}
						
	public function get_one($id) {
		if ($this->auth->get_level() < 5) {
			throw new Exception('BEZ_mdl_Tasks: no permission to get_one()');
		}
		
		$q = $this->select_query.' WHERE tasks.id = ?';
			
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
			
		$task = $sth->fetchObject("BEZ_mdl_Task",
					array($this->model));
        
        if ($task === false) {
            throw new Exception('there is no task with id: '.$id);
        }
				
		return $task;
	}
	
	protected $filter_field_map = array(
		'issue' 	=> 'tasks.issue',
		'action' 	=> 'action',
		'cause'		=> 'tasks.cause',
        'executor'  => 'tasks.executor',
        'state'     => 'tasks.state'
	);
	
	public function get_all($filters=array()) {
		if ($this->auth->get_level() < 5) {
			throw new Exception('BEZ_mdl_Tasks: no permission to get_all()');
		}
		
		list($where_q, $execute) = $this->build_where($filters);
		
		$q = $this->select_query . $where_q;
			
		$sth = $this->model->db->prepare($q);
		
		$sth->setFetchMode(PDO::FETCH_CLASS, "BEZ_mdl_Task",
				array($this->model));
				
		$sth->execute($execute);
						
		return $sth;
	}
    
    public function count($filters=array()) {
        if ($this->auth->get_level() < 5) {
			throw new Exception('BEZ_mdl_Tasks: no permission to get_all()');
		}
        
        if (in_array('action', $filters)) {
            throw new Exception('BEZ_mdl_Tasks: action filter not implemented in method count()');
        }
        
        list($where_q, $execute) = $this->build_where($filters);
        
        $q = 'SELECT COUNT(*) FROM tasks' . $where_q;
        $sth = $this->model->db->prepare($q);
        $sth->execute($execute);
        
        $count = $sth->fetchColumn();
        return $count;
    }
	
	public function create_object($defaults) {
		echo "<b>Warngin: </b> function create_object depraced";
		
		if (isset($defaults['issue'])) {
			$defaults['coordinator'] =
				$this->model->issues->get_one($defaults['issue'])->coordinator;
		} elseif (isset($defaults['tasktype'])) {
			$defaults['coordinator'] = '-all';
		} else {
			throw new Exception('cannot create object with no issue or tasktype');
		}

		$task = new BEZ_mdl_Task($this->model, $defaults);
		return $task;
	}
	
	public function create_object_program($defaults) {
		$defaults['coordinator'] = '-none';
		
		$task = new BEZ_mdl_Task($this->model, $defaults);
		return $task;
	}
	
	public function create_object_issue($defaults) {
		$issue_id = $defaults['issue'];
		$issue = $this->model->issues->get_one($issue_id);
		
		$coordinator = $issue->coordinator;
		$defaults['coordinator'] = $coordinator;
		
		$task = new BEZ_mdl_Task($this->model, $defaults);
		return $task;
	}
}
