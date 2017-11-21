<?php

namespace dokuwiki\plugin\bez\mdl;
 
//if(!defined('DOKU_INC')) die();

//require_once 'factory.php';
//require_once 'thread.php';



class ThreadFactory extends Factory {
	
//	public function __construct($model) {
//		parent::__construct($model);

		/* state_string:
			0 -> opened
			1 -> closed
			2 -> rejected
			if state = 0 and all tasks are done -> done
		*/
//		$this->select_query = "SELECT *,
//					(CASE
//                        WHEN state = 2
//							THEN '".$this->model->action->getLang('state_rejected')."'
//						WHEN coordinator = '-proposal'
//							THEN '".$this->model->action->getLang('state_proposal')."'
//						WHEN state = 0 	AND assigned_tasks_count > 0
//										AND opened_tasks_count = 0
//							THEN '".$this->model->action->getLang('state_done')."'
//						WHEN state = 0
//							THEN '".$this->model->action->getLang('state_opened')."'
//						WHEN state = 1
//							THEN '".$this->model->action->getLang('state_closed')."'
//					END) AS state_string,
//
//					(CASE
//                        WHEN state = 2
//							THEN '2'
//						WHEN coordinator = '-proposal'
//							THEN '-proposal'
//						WHEN state = 0 	AND assigned_tasks_count > 0
//										AND opened_tasks_count = 0
//							THEN '-done'
//						WHEN state = 0
//							THEN '0'
//						WHEN state = 1
//							THEN '1'
//					END) AS full_state,
//
//					(CASE 	WHEN state = 2 then '3'
//                            WHEN task_priority IS NULL THEN 'None'
//							ELSE task_priority
//					END) AS priority
//
//					FROM (SELECT issues.*,
//							(SELECT COUNT(*) FROM tasks
//								WHERE tasks.issue = issues.id)
//							AS assigned_tasks_count,
//							(SELECT COUNT(*) FROM tasks
//								WHERE tasks.issue = issues.id AND tasks.state = 0)
//							AS opened_tasks_count,
//							(SELECT MIN((CASE	WHEN tasks.state > 0 THEN '3'
//								WHEN tasks.plan_date >= date('now', '+1 month') THEN '2'
//								WHEN tasks.plan_date >= date('now') THEN '1'
//								ELSE '0' END)) FROM tasks WHERE tasks.issue = issues.id)
//							AS task_priority,
//                            (SELECT SUM(tasks.cost) FROM tasks
//								WHERE tasks.issue = issues.id)
//                            AS cost,
//					issuetypes.".$this->model->conf['lang']." AS type_string
//					FROM issues
//						LEFT JOIN issuetypes ON issues.type = issuetypes.id)";
//	}

    protected function select_query() {
        return "SELECT thread.*, label.id AS label_id, label.name AS label_name FROM thread
                        LEFT JOIN thread_label ON thread.id = thread_label.thread_id
                        LEFT JOIN label ON label.id = thread_label.label_id";
    }

    public function get_years_scope() {
        $r = $this->model->sqlite->query('SELECT create_date FROM thread ORDER BY id LIMIT 1');
        $date = $this->model->sqlite->res2single($r);

        //get only year
		$first =  (int) substr($date, 0, strpos($date, '-'));
        $last = (int) date('Y');
		
		$years = array();
		for ($year = $first; $year <= $last; $year++) {
			$years[] = (string) $year;
        }
		return $years;
    }
}
