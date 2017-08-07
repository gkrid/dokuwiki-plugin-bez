<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'issue.php';

class BEZ_mdl_Issues extends BEZ_mdl_Factory {
	
	public function __construct($model) {
		parent::__construct($model);
		/* state_string:
			0 -> opened
			1 -> closed
			2 -> rejected
			if state = 0 and all tasks are done -> done
		*/
		$this->select_query = "SELECT *,
					(CASE
                        WHEN state = 2
							THEN '".$this->model->action->getLang('state_rejected')."'
						WHEN coordinator = '-proposal'
							THEN '".$this->model->action->getLang('state_proposal')."'
						WHEN state = 0 	AND assigned_tasks_count > 0
										AND opened_tasks_count = 0
							THEN '".$this->model->action->getLang('state_done')."'
						WHEN state = 0
							THEN '".$this->model->action->getLang('state_opened')."'
						WHEN state = 1
							THEN '".$this->model->action->getLang('state_closed')."'
					END) AS state_string,
					(CASE	WHEN  state = 0 AND assigned_tasks_count > 0
								AND opened_tasks_count = 0 THEN '1'
							ELSE '1'
					END) AS is_done,
					(CASE 	WHEN state = 2 then '3'
                            WHEN task_priority IS NULL THEN 'None'
							ELSE task_priority
					END) AS priority
					
					FROM (SELECT issues.*,
							(SELECT COUNT(*) FROM tasks
								WHERE tasks.issue = issues.id)
							AS assigned_tasks_count,
							(SELECT COUNT(*) FROM tasks
								WHERE tasks.issue = issues.id AND tasks.state = 0)
							AS opened_tasks_count,
							(SELECT MIN((CASE	WHEN tasks.state > 0 THEN '3'
								WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
								WHEN tasks.plan_date >= date('now') THEN '1'
								ELSE '0' END)) FROM tasks WHERE tasks.issue = issues.id)
							AS task_priority,
					issuetypes.".$this->model->conf['lang']." AS type_string
					FROM issues
						LEFT JOIN issuetypes ON issues.type = issuetypes.id)";
	}
    
    protected $filter_field_map = array(

	);
    
    public function get_years_scope() {
        $q = 'SELECT date FROM issues ORDER BY date LIMIT 1';
        $sth = $this->model->db->prepare($q);
		$sth->execute();
		
		$date = $sth->fetchColumn();
		if ($res === false) {
			return array();
        }
		$first = (int)date('Y', (int)$date);
        $last = (int)date('Y');
		
		$years = array();
		for ($year = $first; $year <= $last; $year++) {
			$years[] = (string) $year;
        }
		return $years;
    }
}
