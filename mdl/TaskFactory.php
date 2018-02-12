<?php

namespace dokuwiki\plugin\bez\mdl;

class TaskFactory extends Factory {

    public function get_table_view() {
        return 'task_view';
    }

    public function get_years_scope() {
        $r = $this->model->sqlite->query('SELECT
                                        MIN(create_date),
                                        MIN(plan_date),
                                        DATE(),
                                        MAX(close_date),
                                        MAX(plan_date)
                                        FROM task');
        $data = $this->model->sqlite->res_fetch_array($r);

        $min_date = min($data[0], $data[1], $data[2]);
        $max_date = max($data[2], $data[3], $data[4]);

        //get only year
        $first =  (int) substr($min_date, 0, strpos($min_date, '-'));
        $last = (int) substr($max_date, 0, strpos($max_date, '-'));

        $years = array();
        for ($year = $first; $year <= $last; $year++) {
            $years[] = (string) $year;
        }
        return $years;
    }

    public function get_from_thread(Thread $thread) {
        $tasks = $this->model->taskFactory->get_all(array('thread_id' => $thread->id),
                                            'thread_comment_id', false, array('thread' => $thread));
        $by_thread_comment = array('corrections' => array());
        foreach ($tasks as $task) {
            if ($task->thread_comment_id == null) {
                $by_thread_comment['corrections'][$task->id] = $task;
                continue;
            }
            if (!isset($by_thread_comment[$task->thread_comment_id])) {
                $by_thread_comment[$task->thread_comment_id] = array();
            }
            $by_thread_comment[$task->thread_comment_id][$task->id] = $task;
        }
        return $by_thread_comment;
    }

    public function get_by_type($thread) {
        $tasks = $this->model->taskFactory->get_all(array('thread_id' => $thread->id),
                                            'thread_comment_id', false, array('thread' => $thread));

        $by_type = array('correction' => array(), 'corrective' => array(), 'preventive' => array());
        foreach ($tasks as $task) {
            $by_type[$task->type][$task->id] = $task;
        }

        return $by_type;
    }

    public function users_involvement($range=array()) {
        if (count($range) > 0) {
            $from = date('Y-m-d', strtotime($range[0]));
            if (count($range) == 1) {
                $to = date('Y-m-d');
            } else {
                $to = date('Y-m-d', strtotime($range[1]));
            }
            $sql = "SELECT task_participant.user_id,
                       SUM(task_participant.original_poster) AS original_poster_sum,
                       SUM(task_participant.assignee) AS assignee_sum,
                       SUM(task_participant.commentator) AS commentator_sum
                       FROM task_participant JOIN task ON task_participant.task_id = task.id
                       WHERE task.create_date BETWEEN ? AND ?
                       GROUP BY user_id
                       ORDER BY user_id";
            $r = $this->model->sqlite->query($sql, $from, $to);
        } else {
            $sql = "SELECT user_id,
                       SUM(original_poster) AS original_poster_sum,
                       SUM(assignee) AS assignee_sum,
                       SUM(commentator) AS commentator_sum
                       FROM task_participant
                       GROUP BY user_id
                       ORDER BY user_id";
            $r = $this->model->sqlite->query($sql);
        }

        return $r;
    }

    public function initial_save(Entity $task, $data) {
        try {
            $this->beginTransaction();
            parent::initial_save($task, $data);

            $task->set_participant_flags($task->original_poster, array('subscribent', 'original_poster'));
            $task->set_participant_flags($task->assignee, array('subscribent', 'assignee'));

            if ($task->thread_id != '') {
                $task->thread->set_participant_flags($task->assignee, array('subscribent', 'task_assignee'));
                $task->thread->update_last_activity();
            }

            $this->commitTransaction();

            //notifications
            if ($this->model->user_nick != $task->assignee) {
                $task->mail_notify_assignee();
            }
            if ($task->thread_id != '') {
                $task->thread->mail_notify_task_added($task);
            }
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }

    public function update_save(Entity $task, $data) {
        try {
            $this->beginTransaction();
            $prev_assignee = $task->assignee;
            parent::update_save($task, $data);

            if($task->assignee != $prev_assignee) {
                $task->remove_participant_flags($prev_assignee, array('assignee'));
                $task->set_participant_flags($task->assignee, array('subscribent', 'assignee'));
            }

            if ($task->thread_id != '' && $task->assignee != $prev_assignee) {
                if ($this->model->taskFactory->count(array(
                    'thread_id' => $task->thread_id,
                    'assignee'  => $prev_assignee)) == 0) {
                    $task->thread->remove_participant_flags($prev_assignee, array('task_assignee'));
                }
                $task->thread->set_participant_flags($task->assignee, array('subscribent', 'task_assignee'));
            }

            $this->commitTransaction();
            //notifications
            if ($prev_assignee != $task->assignee && $this->model->user_nick != $task->assignee) {
                $task->mail_notify_assignee();
            }
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }
}