<?php

namespace dokuwiki\plugin\bez\mdl;

class Task_commentFactory extends Factory {

    public function get_from_task(Task $task) {
        return $this->get_all(array('task_id' => $task->id), $orderby='', $desc=true, array('task' => $task));
    }

    /**
     * @param Thread_comment $thread_comment
     * @param                $data
     * @throws \Exception
     */
    public function initial_save(Entity $task_comment, $data) {
        try {
            $this->beginTransaction();

            //if empty content and task_do, do not save the comment
            if ($data['fn'] == 'comment_add' || $data['content'] != '') {
                parent::initial_save($task_comment, $data);
                $task_comment->task->set_participant_flags($task_comment->author, array('subscribent', 'commentator'));
            }

            if ($data['fn'] == 'task_do') {
                $task_comment->task->set_state('done');
            } elseif ($data['fn'] == 'task_reopen') {
                $task_comment->task->set_state('opened');
            }

            if ($task_comment->task->thread_id != '') {
                $task_comment->task->thread->update_last_activity();
            }

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }

        $task_comment->mail_notify_add();
    }

    public function update_save(Entity $task_comment, $data) {
        try {
            $this->beginTransaction();
            parent::update_save($task_comment, $data);

            $task_comment->task->update_last_activity();

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }

    public function delete(Entity $obj) {
        try {
            $this->beginTransaction();

            parent::delete($obj);
            $obj->task->update_last_activity();

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }
}