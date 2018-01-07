<?php

namespace dokuwiki\plugin\bez\mdl;

use Assetic\Exception\Exception;

class ThreadFactory extends Factory {

    public function get_table_view() {
        return 'thread_view';
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

    public function users_involvement() {
        $sql = 'SELECT user_id,
                       SUM(original_poster),
                       SUM(coordinator),
                       SUM(commentator),
                       SUM(task_assignee),
                       COUNT(*)
                       FROM thread_participant
                       GROUP BY user_id
                       ORDER BY user_id';

        $r = $this->model->sqlite->query($sql);
        return $r;
    }

    public function initial_save(Entity $thread, $data) {
        $label_ids = array();
        if (isset($data['label_id']) && $data['label_id'] != '') {
            $label_ids[] = $data['label_id'];
        }
        try {
            $this->beginTransaction();

            parent::initial_save($thread, $data);

            foreach($label_ids as $label_id) {
                $thread->add_label($label_id);
            }

            $thread->set_participant_flags($thread->original_poster, array('original_poster', 'subscribent'));
            if($thread->coordinator != null) {
                $thread->set_participant_flags($thread->coordinator, array('coordinator', 'subscribent'));
            }

            if ($this->model->get_level() >= BEZ_AUTH_LEADER) {
                $private = false;
                if (isset($data['private'])) {
                    $private = true;
                }
                $thread->set_private_flag($private);
            }

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }

        if ($thread->state != 'proposal' && $this->model->user_nick != $thread->coordinator) {
            $thread->mail_inform_coordinator();
        }
    }

    public function update_save(Entity $thread, $data) {
        $prev_coordinator = $thread->coordinator;

        $label_ids = array();
        if (isset($data['label_id']) && $data['label_id'] != '') {
            $label_ids[] = $data['label_id'];
        }
        try {
            $this->beginTransaction();
            parent::update_save($thread, $data);

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

            if ($thread->acl_of('private') >= BEZ_PERMISSION_CHANGE) {
                $private = false;
                if (isset($data['private'])) {
                    $private = true;
                }
                $thread->set_private_flag($private);
            }

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }

        if ($thread->state != 'proposal' && $this->model->user_nick != $thread->coordinator) {
            $thread->mail_inform_coordinator();
        }
    }
}
