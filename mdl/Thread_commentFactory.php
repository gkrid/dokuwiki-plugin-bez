<?php

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\ConsistencyViolationException;

class Thread_commentFactory extends Factory {

    public function get_table_view() {
        return 'thread_comment_view';
    }

    public function get_from_thread(Thread $thread, $filters=array(), $orderby='', $desc=true, $limit=false) {
        $filters['thread_id'] = $thread->id;
        return $this->get_all($filters, $orderby, $desc, array('thread' => $thread), $limit);
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
            }

            if ($data['fn'] == 'thread_close') {
                $thread_comment->thread->set_state('closed');
            } elseif ($data['fn'] == 'thread_reject') {
                $thread_comment->thread->set_state('rejected');
            } elseif ($data['fn'] == 'thread_reopen') {
                $thread_comment->thread->set_state('opened');
            }

            $thread_comment->thread->update_last_activity();

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }

        $thread_comment->mail_notify_add();
    }

    public function update_save(Entity $thread_comment, $data) {
        try {
            $this->beginTransaction();
            parent::update_save($thread_comment, $data);

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

            $this->commitTransaction();
        } catch(Exception $exception) {
            $this->rollbackTransaction();
        }
    }
}