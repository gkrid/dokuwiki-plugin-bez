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
                        WHEN commcauses.type = '1' THEN '1'
                        ELSE '2' END) AS action,

                (CASE
                    WHEN tasks.issue IS NULL
                        THEN '".$this->model->action->getLang('programme')."'
                    WHEN tasks.cause IS NULL OR tasks.cause = ''
                        THEN '".$this->model->action->getLang('correction')."'
                     WHEN commcauses.type = 1
                        THEN '".$this->model->action->getLang('corrective_action')."'
                    ELSE
                        '".$this->model->action->getLang('preventive_action')."'
                END) AS action_string,

                (CASE WHEN tasks.issue IS NULL THEN '-none' 
                      ELSE issues.coordinator END) AS coordinator
                FROM tasks
                    LEFT JOIN tasktypes ON tasks.tasktype = tasktypes.id
                    LEFT JOIN commcauses ON tasks.cause = commcauses.id
                    LEFT JOIN issues ON tasks.issue = issues.id";
	}
						
	public function get_one($id) {
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
        'state'     => 'tasks.state',
        'plan_date' => 'tasks.plan_date'
	);
	
	public function get_all($filters=array()) {
		list($where_q, $execute) = $this->build_where($filters);
		
		$q = $this->select_query . $where_q;
			
		$sth = $this->model->db->prepare($q);
		
		$sth->setFetchMode(PDO::FETCH_CLASS, "BEZ_mdl_Task",
				array($this->model));
				
		$sth->execute($execute);
						
		return $sth;
	}
    
    public function count($filters=array()) {
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
		$task = new BEZ_mdl_Task($this->model, $defaults);
		return $task;
	}
	
    public function create_dummy_object($defaults) {
		$issue = new BEZ_mdl_Dummy_Task($this->model, $defaults);
		return $issue;
	}
}
