<?php

namespace dokuwiki\plugin\bez\mdl;
 
//if(!defined('DOKU_INC')) die();

//require_once 'factory.php';
//require_once 'thread.php';



use Assetic\Exception\Exception;

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

    public function initial_save(Thread $thread, $data, $label_ids=array()) {
        if ($thread->id != NULL) {
            throw new \Exception('row already saved. use update_save');
        }

        $thread->set_data($data);
        $label_ids = array();
        if (isset($data['label_id']) && $data['label_id'] != '') {
            $label_ids[] = $data['label_id'];
        }
        try {
            $this->beginTransaction();
            parent::save($thread);

            foreach($label_ids as $label_id) {
                $thread->add_label($label_id);
            }

            $thread->set_participant_flags($thread->original_poster, array('original_poster', 'subscribent'));
            if($thread->coordinator != null) {
                $thread->set_participant_flags($thread->coordinator, array('coordinator', 'subscribent'));
            }

            $this->model->threadFactory->commitTransaction();
        } catch(Exception $exception) {
            $this->model->threadFactory->rollbackTransaction();
        }
    }

    public function update_save(Thread $thread, $data, $label_ids=array()) {
        if ($thread->id == NULL) {
            throw new \Exception('row not saved. use initial_save()');
        }

        $prev_coordinator = $thread->coordinator;
        $thread->set_data($data);
        $label_ids = array();
        if (isset($data['label_id']) && $data['label_id'] != '') {
            $label_ids[] = $data['label_id'];
        }
        try {
            $this->beginTransaction();
            parent::save($thread);

            $cur_label_ids = array_keys($thread->get_labels());
            $labels_to_add = array_diff($label_ids, $cur_label_ids);
            $labels_to_rem = array_diff($cur_label_ids, $label_ids);

            foreach($labels_to_add as $label_id) {
                $thread->add_label($label_id);
            }

            foreach($labels_to_rem as $label_id) {
                $thread->remove_label($label_id);
            }

            if($thread->coordinator != null && $thread->coordinator != $prev_coordinator) {
                $thread->remove_participant_flags($prev_coordinator, array('coordinator'));
                $thread->set_participant_flags($thread->coordinator, array('subscribent', 'coordinator'));
            }

            $this->model->threadFactory->commitTransaction();
        } catch(Exception $exception) {
            $this->model->threadFactory->rollbackTransaction();
        }
    }
}
