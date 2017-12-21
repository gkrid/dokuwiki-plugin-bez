<?php

namespace dokuwiki\plugin\bez\mdl;

class TaskFactory extends Factory {

    public function get_table_view() {
        return 'task_view';
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

    public function users_involvement() {
        $sql = 'SELECT user_id,
                       SUM(original_poster),
                       SUM(assignee),
                       SUM(commentator),
                       COUNT(*)
                       FROM task_participant
                       GROUP BY user_id
                       ORDER BY user_id';

        $r = $this->model->sqlite->query($sql);
        return $r;
    }

    public function initial_save(Entity $task, $data) {
        parent::initial_save($task, $data);

        $task->set_data($data);
        try {
            $this->beginTransaction();
            $this->save($task);

            if ($task->thread) {
                $task->thread->set_participant_flags($task->assignee, array('subscribent', 'task_assignee'));
                $task->thread->update_last_activity();
            }

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }

        //$task->mail_notify_add();
    }

    public function update_save(Entity $task, $data) {
        parent::update_save($task, $data);

        $task->set_data($data);
        try {
            $this->beginTransaction();
            $this->save($task);

            if ($task->thread) {
            }

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }
}