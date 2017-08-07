<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'task.php';

class BEZ_mdl_Tasks extends BEZ_mdl_Factory {
	
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
                    WHEN tasks.issue IS NULL OR tasks.issue = ''
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
	
	protected $filter_field_map = array(
		'issue' 	=> 'tasks.issue',
		'action' 	=> 'action',
		'cause'		=> 'tasks.cause',
        'executor'  => 'tasks.executor',
        'state'     => 'tasks.state',
        'plan_date' => 'tasks.plan_date'
	);
	    
    public function count($filters=array()) {
        if (in_array('action', $filters)) {
            throw new Exception('action filter not implemented in method count()');
        }
        
        return parent::count($filters);
    }
}
