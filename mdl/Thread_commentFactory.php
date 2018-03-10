<?php

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\ConsistencyViolationException;

class Thread_commentFactory extends Factory {

    public function get_table_view() {
        return 'thread_comment_view';
    }

    public function get_from_thread(Thread $thread, $filters=array(), $orderby='', $limit=false) {
        $filters['thread_id'] = $thread->id;
        return $this->get_all($filters, $orderby, array('thread' => $thread), $limit);
    }

    /**
     * @param Thread_comment $thread_comment
     * @param                $data
     * @throws \Exception
     */
    public function initial_save(Entity $thread_comment, $data) {
        try {
            $this->beginTransaction();

            if ($data['fn'] == 'comment_add' ||
                $data['fn'] == 'thread_close' ||
                $data['content'] != '') {
                parent::initial_save($thread_comment, $data);
                $thread_comment->thread->set_participant_flags($thread_comment->author, array('subscribent', 'commentator'));
                $notify = 'comment_added';
            }

            if ($data['fn'] == 'thread_close') {
                $thread_comment->thread->set_state('closed');
                $notify = 'mail_thread_closed';
            } elseif ($data['fn'] == 'thread_reject') {
                $thread_comment->thread->set_state('rejected');
                $notify = 'mail_thread_rejected';
            } elseif ($data['fn'] == 'thread_reopen') {
                $thread_comment->thread->set_state('opened');
                $notify = 'mail_thread_reopened';
            }

            $thread_comment->thread->update_last_activity();

            $this->commitTransaction();

            if ($notify == 'comment_added') {
                $thread_comment->mail_notify_add();
            } elseif (isset($notify)) {
                $thread_comment->thread->mail_notify_change_state($notify);
            }

        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }

    public function update_save(Entity $thread_comment, $data) {
        try {
            $this->beginTransaction();

            $prev_type = $thread_comment->type;

            parent::update_save($thread_comment, $data);

            //update task types
            if ($thread_comment->type != 'comment' && $thread_comment->type != $prev_type) {
                if ($thread_comment->type == 'cause_real') {
                    $task_type = 'corrective';
                } else {
                    $task_type = 'preventive';
                }
                $this->model->sqlite->query('UPDATE task SET type=? WHERE thread_comment_id=?',
                                            $task_type, $thread_comment->id);

            }

            $thread_comment->thread->update_last_activity();

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }

    public function delete(Entity $obj) {

        if ($obj->task_count > 0) {
            throw new ConsistencyViolationException('cannot delete when task are assigned');
        }

        try {
            $this->beginTransaction();

            parent::delete($obj);
            $obj->thread->update_last_activity();
            //remove commentator flag
            if ($this->count(array('thread_id' => $obj->thread_id, 'author' => $obj->author)) == 0) {
                $obj->thread->remove_participant_flags($obj->author, array('commentator'));
            }

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }
}