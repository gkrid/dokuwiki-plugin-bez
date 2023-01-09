<?php

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\ConsistencyViolationException;
use dokuwiki\plugin\bez\meta\PermissionDeniedException;
use dokuwiki\plugin\bez\meta\ValidationException;

class Thread extends Entity {

    protected $id;

    protected $original_poster, $coordinator, $closed_by;

    protected $private, $lock;

    protected $type, $state;

    protected $create_date, $last_activity_date, $last_modification_date, $close_date;

    protected $title, $content, $content_html;

    protected $task_count, $task_count_closed, $task_sum_cost;

    protected $cause_count, $risk_count, $opportunity_count;

    public static function get_columns() {
        return array('id',
                     'original_poster', 'coordinator', 'closed_by',
                     'private', 'lock',
                     'type', 'state',
                     'create_date', 'last_activity_date', 'last_modification_date', 'close_date',
                     'title', 'content', 'content_html',
                     'task_count', 'task_count_closed', 'task_sum_cost');
    }

    public static function get_select_columns() {
        $cols = parent::get_select_columns();
        array_push($cols, 'label_id', 'label_name', 'cause_count', 'risk_count', 'opportunity_count');
        return $cols;
    }

    public static function get_states() {
        return array('proposal', 'opened', 'done', 'closed', 'rejected');
    }

    public function __get($property) {
        if($property == 'priority') {
            return $this->$property;
        }
        return parent::__get($property);
    }

    public function user_is_coordinator() {
        if ($this->coordinator === $this->model->user_nick ||
           $this->model->get_level() >= BEZ_AUTH_ADMIN) {
            return true;
        }
        return false;
    }

	public function __construct($model, $defaults=array()) {
		parent::__construct($model);

        $this->validator->set_rules(array(
            'coordinator' => array(array('dw_user'), 'NULL'),
            'title' => array(array('length', 200), 'NOT NULL'),
            'content' => array(array('length', 10000), 'NOT NULL'),
            'type' => array(array('select', array('issue', 'project')), 'NULL')
        ));

		//we've created empty object (new record)
		if ($this->id === NULL) {
			$this->original_poster = $this->model->user_nick;
			$this->create_date = date('c');
			$this->last_activity_date = $this->create_date;
            $this->last_modification_date = $this->create_date;

			$this->state = 'proposal';

			$this->acl->grant('title', BEZ_PERMISSION_CHANGE);
            $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
            $this->acl->grant('type', BEZ_PERMISSION_CHANGE);


            if ($this->model->get_level() >= BEZ_AUTH_LEADER) {

                $this->state = 'opened';

                $this->acl->grant('coordinator', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('label_id', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('private', BEZ_PERMISSION_CHANGE);
            }

		} else {
            //private threads
            if ($this->model->level < BEZ_AUTH_ADMIN && $this->private == '1') {
                if ($this->get_participant($this->model->user_nick) === false) {
                    $this->acl->revoke(self::get_select_columns(), BEZ_AUTH_LEADER);
                    return;
                }
            }

		    if ($this->state == 'proposal' && $this->original_poster == $this->model->user_nick) {
                $this->acl->grant('title', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('type', BEZ_PERMISSION_CHANGE);
            }

            if ($this->coordinator == $this->model->user_nick) {
                $this->acl->grant('title', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('coordinator', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('label_id', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('private', BEZ_PERMISSION_CHANGE);

                $this->acl->grant('state', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('type', BEZ_PERMISSION_CHANGE);
            }
        }
	}

	public function set_data($data, $filter=NULL) {
        parent::set_data($data, $filter=NULL);

        if (isset($data['coordinator']) && $this->state == 'proposal') {
            $this->state = 'opened';
        }

        //update cache
		$this->purge();

        //update dates
        $this->last_modification_date = date('c');
        $this->last_activity_date = $this->last_modification_date;
    }

    public function set_state($state) {
        if ($this->acl_of('state') < BEZ_PERMISSION_CHANGE) {
            throw new PermissionDeniedException();
        }

        if (!in_array($state, array('opened', 'closed', 'rejected'))) {
            throw new ValidationException('thread', array('state should be opened, closed or rejected'));
        }

        //nothing to do
        if ($state == $this->state) {
            return;
        }

        if ($state == 'closed' || $state == 'rejected') {
            $this->state = $state;
            $this->closed_by = $this->model->user_nick;
            $this->close_date = date('c');

            $this->model->sqlite->query("UPDATE {$this->get_table_name()} SET state=?, closed_by=?, close_date=? WHERE id=?",
                $state,
                $this->closed_by,
                $this->close_date,
                $this->id);
            //reopen the task
        } else {
            $this->state = $state;

            $this->model->sqlite->query("UPDATE {$this->get_table_name()} SET state=? WHERE id=?", $state, $this->id);
        }

        $this->state = $state;
    }

    public function set_private_flag($flag) {
        $private = '0';
        if ($flag) {
            $private = '1';
        }

        if ($private == $this->private) {
            return;
        }

        //update thread
        $this->model->sqlite->query("UPDATE {$this->get_table_name()} SET private=? WHERE id=?", $private, $this->id);

        //update task
        $this->model->sqlite->query("UPDATE task SET private=? WHERE thread_id=?", $private, $this->id);
    }

	public function update_last_activity() {
        $this->last_activity_date = date('c');
        $this->model->sqlite->query('UPDATE thread SET last_activity_date=? WHERE id=?',
                                    $this->last_activity_date, $this->id);
    }

    public function get_participants($filter='') {
        if ($this->id === NULL) {
            return array();
        }

        $sql = 'SELECT * FROM thread_participant WHERE';
        $possible_flags = array('original_poster', 'coordinator', 'commentator', 'task_assignee', 'subscribent');
        if ($filter != '') {
            if (!in_array($filter, $possible_flags)) {
                throw new \Exception("unknown flag $filter");
            }
            $sql .= " $filter=1 AND";
        }
        $sql .= ' thread_id=? AND removed=0 ORDER BY user_id';

        $r = $this->model->sqlite->query($sql, $this->id);
        $pars = $this->model->sqlite->res2arr($r);
        $participants = array();
        foreach ($pars as $par) {
            $participants[$par['user_id']] = $par;
        }

        return $participants;
    }

    public function get_participant($user_id, $can_be_removed=false) {
        if ($this->id === NULL) {
            return array();
        }

        $q = 'SELECT * FROM thread_participant WHERE thread_id=? AND user_id=?';
        if (!$can_be_removed) {
            $q .= ' AND removed=0';
        }
        $r = $this->model->sqlite->query($q, $this->id, $user_id);
        $par = $this->model->sqlite->res2row($r);
        if (!is_array($par)) {
            return false;
        }

        return $par;
    }

    public function is_subscribent($user_id=null) {
        if ($user_id == null) {
            $user_id = $this->model->user_nick;
        }
        $par = $this->get_participant($user_id);
        if ($par['subscribent'] == 1) {
            return true;
        }
        return false;
    }

    public function remove_participant_flags($user_id, $flags) {
        //thread not saved yet
        if ($this->id === NULL) {
            throw new \Exception('cannot remove flags from not saved thread');
        }

        $participant = $this->get_participant($user_id, true);
        if ($participant === false) {
            throw new ConsistencyViolationException("$user_id isn't participant");
        }

        $possible_flags = array('original_poster', 'coordinator', 'commentator', 'task_assignee', 'subscribent');
        if (array_intersect($flags, $possible_flags) != $flags) {
            throw new \Exception('unknown flags');
        }

        $set = implode(',', array_map(function ($v) { return "$v=0"; }, $flags));

        $sql = "UPDATE thread_participant SET $set WHERE thread_id=? AND user_id=?";
        $this->model->sqlite->query($sql, $this->id, $user_id);

    }

	public function set_participant_flags($user_id, $flags=array()) {
        //thread not saved yet
        if ($this->id === NULL) {
            throw new \Exception('cannot add flags to not saved thread');
        }

        //validate user
        if (!$this->model->userFactory->exists($user_id)) {
            throw new \Exception("$user_id isn't dokuwiki user");
        }

        $possible_flags = array('original_poster', 'coordinator', 'commentator', 'task_assignee', 'subscribent');
        if (array_intersect($flags, $possible_flags) != $flags) {
            throw new \Exception('unknown flags');
        }

        $participant = $this->get_participant($user_id, true);
        if ($participant == false) {
            $participant = array_fill_keys($possible_flags, 0);

            $participant['thread_id'] = $this->id;
            $participant['user_id'] = $user_id;
            $participant['added_by'] = $this->model->user_nick;
            $participant['added_date'] = date('c');

            $values = array_merge($participant, array_fill_keys($flags, 1));

            $this->model->sqlite->storeEntry('thread_participant', $values);
        } else {
            $set = implode(',', array_map(function($flag) { return "$flag=1"; }, $flags));

            if ($participant['removed'] == '1') {
                $set .= ',removed=0';
            }

            $q = "UPDATE thread_participant SET $set WHERE thread_id=? AND user_id=?";
            $this->model->sqlite->query($q, $this->id, $user_id);
        }

	}

	public function remove_participant($user_id) {
        //thread not saved yet
        if ($this->id === NULL) {
            throw new \Exception('cannot remove flags from not saved thread');
        }

        $participant = $this->get_participant($user_id);
        if ($participant === false) {
            throw new ConsistencyViolationException("$user_id isn't participant");
        }

        if ($participant['coordinator'] == '1') {
            throw new ConsistencyViolationException("cannot remove coordinator");
        }

        if ($participant['task_assignee'] == '1') {
            throw new ConsistencyViolationException("cannot remove task_assignee");
        }

        $q = "UPDATE thread_participant SET removed=1 WHERE thread_id=? AND user_id=?";
        $this->model->sqlite->query($q, $this->id, $user_id);

    }

    public function invite($client) {
        $this->set_participant_flags($client, array('subscribent'));
        $this->mail_notify_invite($client);
    }

    public function get_labels() {
        //record not saved
        if ($this->id === NULL) {
           return array();
        }

        $labels = array();
        $r = $this->model->sqlite->query('SELECT * FROM label JOIN thread_label ON label.id = thread_label.label_id
                                            WHERE thread_label.thread_id=?', $this->id);
        $arr = $this->model->sqlite->res2arr($r);
        foreach ($arr as $label) {
            $labels[$label['id']] = $label;
        }

        return $labels;
    }

    public function add_label($label_id) {
         //issue not saved yet
        if ($this->id === NULL) {
            throw new \Exception('cannot add labels to not saved thread. use initial_save() instead');
        }

        $r = $this->model->sqlite->query('SELECT id FROM label WHERE id=?', $label_id);
        $label_id = $this->model->sqlite->res2single($r);
        if (!$label_id) {
            throw new \Exception("label($label_id) doesn't exist");
        }


        $this->model->sqlite->storeEntry('thread_label',
                                         array('thread_id' => $this->id,
                                               'label_id' => $label_id));

    }

    public function remove_label($label_id) {
        //issue not saved yet
        if ($this->id === NULL) {
            throw new \Exception('cannot remove labels from not saved thread. use initial_save() instead');
        }

        /** @var \PDOStatement $r */
        $r = $this->model->sqlite->query('DELETE FROM thread_label WHERE thread_id=? AND label_id=?',$this->id, $label_id);
        if ($r->rowCount() != 1) {
            throw new \Exception('label was not assigned to this thread');
        }

    }

    public function get_causes() {
        $r = $this->model->sqlite->query("SELECT id FROM thread_comment WHERE (type='cause' OR type='risk' OR type='opportunity') AND thread_id=?",
                                         $this->id);
        $arr = $this->model->sqlite->res2arr($r);
        $causes = array();
        foreach ($arr as $cause) {
            $causes[] = $cause['id'];
        }

        return $causes;
    }

    public function can_add_comments() {
        return in_array($this->state, array('proposal', 'opened', 'done'));
    }

    public function can_add_causes() {
        return $this->type == 'issue' && in_array($this->state, array('opened', 'done'));
    }

    public function can_add_tasks() {
        return in_array($this->state, array('opened', 'done'));
    }

    public function can_add_participants() {
        return in_array($this->state, array('opened', 'done'));
    }

    public function count_opened_nopreventive_tasks() {
        $res = $this->model->sqlite->query("SELECT state FROM task WHERE thread_id = ?
                                                                        AND type != 'preventive'
                                                                        AND state = 'opened'", $this->id);
        return $this->model->sqlite->res2count($res);
    }

    public function can_be_closed() {
        $res = $this->model->sqlite->query("SELECT thread_comment.id FROM thread_comment
                               LEFT JOIN task ON thread_comment.id = task.thread_comment_id
                               WHERE thread_comment.thread_id = ? AND
                                     thread_comment.type = 'cause' AND task.id IS NULL", $this->id);
        $causes_without_tasks = $this->model->sqlite->res2count($res);

        return !in_array($this->state, array('closed', 'rejected'))
                && $this->task_count > 0
                && $this->count_opened_nopreventive_tasks() == 0
                && $causes_without_tasks == 0;
    }

    public function can_be_rejected() {
        return $this->state != 'rejected' && $this->task_count == 0;
    }

    public function can_be_reopened() {
        return in_array($this->state, array('closed', 'rejected'));
    }

    public function closing_comment() {
        $r = $this->model->thread_commentFactory->get_from_thread($this, array(), 'id DESC', 1);
        $thread_comment = $r->fetch();

        return $thread_comment->content_html;
    }

    protected function html_link_url() {
        $tpl = $this->model->action->get_tpl();
        return $tpl->url('thread', 'id', $this->id);
    }

    protected function html_link_content() {
        return '#' . $this->id;
    }

    protected function getMailSubject()
    {
        return parent::getMailSubject() . ' #'.$this->id. ' ' .$this->title;
    }

    public function mail_thread_box(&$attachedImages) {
        $tpl = $this->model->action->get_tpl();

        //render style
        $less = new \lessc();
        $less->addImportDir(DOKU_PLUGIN . 'bez/style/');
        $style = $less->compileFile(DOKU_PLUGIN . 'bez/style/thread.less');

        //render content for mail
        $old_content_html = $this->content_html;
        $this->content_html = p_render('bez_xhtmlmail', p_get_instructions($this->content), $info);
        $attachedImages = array_merge($attachedImages, $info['img']);

        $tpl->set('thread', $this);
        $tpl->set('style', $style);
        $tpl->set('no_actions', true);
        $thread_box = $this->model->action->bez_tpl_include('thread_box', true);

        $this->content_html = $old_content_html;

        return $thread_box;
    }

    public function mail_thread(&$attachedImages) {
        $tpl = $this->model->action->get_tpl();

        $thread_box = $this->mail_thread_box($attachedImages);

        $tpl->set('content', $thread_box);
        $content = $this->model->action->bez_tpl_include('mail/thread', true);

        return $content;
    }

    public function mail_notify_change_state($action='') {
        if (!$action) {
            $action = 'mail_mail_notify_change_state_action';
        }
        $tpl = $this->model->action->get_tpl();

        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', $action);
        $attachedImages = array();
        $content = $this->mail_thread($attachedImages);
        $this->mail_notify($content, false, $attachedImages);
    }

    public function mail_notify_invite($client) {
        $tpl = $this->model->action->get_tpl();

        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', 'mail_mail_notify_invite_action');
        $attachedImages = array();
        $content = $this->mail_thread($attachedImages);
        $this->mail_notify($content, array($client), $attachedImages);
    }

    public function mail_inform_coordinator() {
        $tpl = $this->model->action->get_tpl();

        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', 'mail_mail_inform_coordinator_action');
        $attachedImages = array();
        $content = $this->mail_thread($attachedImages);
        $this->mail_notify($content, array($this->coordinator), $attachedImages);
    }

    public function mail_inform_admins() {
        $tpl = $this->model->action->get_tpl();

        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', 'mail_mail_inform_admins_action');
        $attachedImages = array();
        $content = $this->mail_thread($attachedImages);
        $this->mail_notify($content, $this->model->userFactory->users_of_group(array('admin', 'bez_admin')), $attachedImages);
    }

    public function mail_notify_inactive($users=false) {
        $tpl = $this->model->action->get_tpl();

        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', 'mail_mail_notify_issue_inactive');
        $attachedImages = array();
        $content = $this->mail_thread($attachedImages);
        $this->mail_notify($content, $users, $attachedImages);
    }

    public function mail_notify_task_added(Task $task) {
        $tpl = $this->model->action->get_tpl();

        //we don't want who
        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', 'mail_thread_task_added');
        $attachedImages = array();
        $task_box = $task->mail_task_box($attachedImages);

        $tpl->set('thread', $this);
        $tpl->set('content', $task_box);
        $content = $this->model->action->bez_tpl_include('mail/thread', true);

        $this->mail_notify($content, false, $attachedImages);
    }

    public function mail_notify_task_state_changed(Task $task) {
        $tpl = $this->model->action->get_tpl();

        if ($task->state == 'done') {
            $action = 'mail_thread_task_done';
        } else {
            $action = 'mail_thread_task_reopened';
        }

        //we don't want who
        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', $action);
        $attachedImages = array();
        $task_box = $task->mail_task_box($attachedImages);

        $tpl->set('thread', $this);
        $tpl->set('content', $task_box);
        $content = $this->model->action->bez_tpl_include('mail/thread', true);

        $this->mail_notify($content, false, $attachedImages);
    }

    public function can_be_removed() {
        $r = $this->model->sqlite->query("SELECT COUNT(*) FROM thread_comment WHERE thread_id=?",
                                         $this->id);
        $comments_count = $this->model->sqlite->res2single($r);
        return $this->task_count == 0 && $comments_count == 0;
    }
}
