<?php
/**
 * Created by PhpStorm.
 * User: ghi
 * Date: 12.12.17
 * Time: 11:58
 */

namespace dokuwiki\plugin\bez\mdl;

class TaskFactory extends Factory {
    protected function select_query() {
        return "SELECT task.*, task_program.name AS task_program_name
                  FROM task LEFT JOIN task_program ON task.task_program_id = task_program.id";
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