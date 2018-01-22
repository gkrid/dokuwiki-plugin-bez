<?php

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\Mailer;
use dokuwiki\plugin\bez\meta\PermissionDeniedException;
use dokuwiki\plugin\bez\meta\ValidationException;
use dokuwiki\plugin\struct\types\DateTime;

class Task extends Entity {

    protected $id;

	protected $original_poster, $assignee, $closed_by;

	protected $private, $lock;

	protected $state, $type;

	protected $create_date, $last_activity_date, $last_modification_date, $close_date;

	protected $cost, $plan_date, $all_day_event, $start_time, $finish_time;

	protected $content, $content_html;

	protected $thread_id, $thread_comment_id, $task_program_id;

	/** @var \dokuwiki\plugin\bez\mdl\Thread */
	protected $thread;

	/** @var Thread_comment */
	protected $thread_comment;

	//virtual
    protected $task_program_name, $priority, $coordinator;
    
	public static function get_columns() {
		return array('id',
            'original_poster', 'assignee', 'closed_by',
            'private', 'lock',
            'state', 'type',
            'create_date', 'last_activity_date', 'last_modification_date', 'close_date',
            'cost', 'plan_date', 'all_day_event', 'start_time', 'finish_time',
            'content', 'content_html',
            'thread_id', 'thread_comment_id', 'task_program_id');
	}

	public static function get_types() {
	    return array('correction', 'corrective', 'preventive', 'program');
    }

    public static function get_states() {
        return array('opened', 'done');
    }

    public function __get($property) {
        if ($property == 'thread') {
            if ($this->thread_id == null) {
                return null;
            }
            if ($this->thread == null) {
                $this->thread = $this->model->threadFactory->get_one($this->thread_id);
            }
            return $this->thread;

        } elseif($property == 'thread_comment') {
            if ($this->thread_comment_id == null) {
                return null;
            }
            if ($this->thread_comment == null) {
                $this->thread_comment = $this->model->thread_commentFactory->get_one($this->thread_comment_id);
            }
            return $this->thread_comment;

        } elseif($property == 'priority' || $property == 'coordinator' || $property == 'task_program_name') {
            return $this->$property;
        }
        return parent::__get($property);
    }

	public function __construct($model, $defaults=array()) {
		parent::__construct($model, $defaults);

		//virutal ACL columns (not in select)
		$this->acl->add_column('participants');

		$this->validator->set_rules(array(
            'assignee' => array(array('dw_user'), 'NOT NULL'),
            'cost' => array(array('numeric'), 'NULL'),
			'plan_date' => array(array('iso_date'), 'NOT NULL'),
			'all_day_event' => array(array('select', array('0', '1')), 'NOT NULL'), 
			'start_time' => array(array('time'), 'NULL'), 
			'finish_time' => array(array('time'), 'NULL'),
            'content' => array(array('length', 10000), 'NOT NULL'),
            'thread_comment_id' => array(array('numeric'), 'NULL'),
            'task_program_id' => array(array('numeric'), 'NULL')
		));
		
		//we've created empty object
		if ($this->id === NULL) {
            $this->original_poster = $this->model->user_nick;
            $this->create_date = date('c');
            $this->last_activity_date = $this->create_date;
            $this->last_modification_date = $this->create_date;

            $this->state = 'opened';

            if (isset($defaults['thread'])) {
                $this->thread = $defaults['thread'];
                $this->thread_id = $this->thread->id;
                $this->coordinator = $this->thread->coordinator;

                if ($this->thread->private == '1') {
                    $this->private = '1';
                }

                $this->type = 'correction';

                if (isset($defaults['thread_comment'])) {
                    $this->thread_comment = $defaults['thread_comment'];
                    $this->thread_comment_id = $this->thread_comment->id;

                    if ($this->thread_comment->type == 'cause_real') {
                        $this->type = 'corrective';
                    } else {
                        $this->type = 'preventive';
                    }
                }
            } else {
                $this->type = 'program';
            }


            if ($this->thread_id == '') {
                $this->validator->set_rules(array(
                                                'task_program_id' => array(array('numeric'), 'NOT NULL'),
                                            ));
                //this field is unused in program tasks
                $this->validator->delete_rule('thread_comment_id');
            }

            //everyone can report their own program tasks
            if ($this->type == 'program') {
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('plan_date', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('start_time', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('finish_time', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('all_day_event', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('task_program_id', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('cost', BEZ_PERMISSION_CHANGE);
            }

            if ($this->type == 'program' && $this->model->get_level() >= BEZ_AUTH_LEADER) {
                $this->acl->grant('assignee', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('participants', BEZ_PERMISSION_CHANGE);
            }

            if ($this->type != 'program' && $this->coordinator == $this->model->user_nick) {
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('plan_date', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('start_time', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('finish_time', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('all_day_event', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('task_program_id', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('cost', BEZ_PERMISSION_CHANGE);

                $this->acl->grant('assignee', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('participants', BEZ_PERMISSION_CHANGE);
            }

        //we get object form db
		} else {
            if (isset($defaults['thread']) && $this->thread_id == $defaults['thread']->id) {
                $this->thread = $defaults['thread'];
            }

            if (isset($defaults['thread_comment']) && $this->thread_comment_id == $defaults['thread_comment']->id) {
                $this->thread_comment = $defaults['thread_comment'];
            }

            if ($this->thread_id == '') {
                $this->validator->set_rules(array(
                                                'task_program_id' => array(array('numeric'), 'NOT NULL'),
                                            ));
                //this field is unused in program tasks
                $this->validator->delete_rule('thread_comment_id');
            }

            //private tasks
            if ($this->model->level < BEZ_AUTH_ADMIN && $this->private == '1') {
                if ($this->get_participant($this->model->user_nick) === false &&
                    ($this->thread_id != '' && $this->__get('thread')->get_participant($this->model->user_nick) === false)) {
                    $this->acl->revoke(self::get_select_columns(), BEZ_AUTH_LEADER);
                    return;
                }
            }

            //user can close their tasks
            if ($this->assignee == $this->model->user_nick || $this->model->get_level() >= BEZ_AUTH_LEADER) {
                $this->acl->grant('state', BEZ_PERMISSION_CHANGE);
            }

            if ($this->type == 'program' && $this->original_poster == $this->model->user_nick) {
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('plan_date', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('start_time', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('finish_time', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('all_day_event', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('task_program_id', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('cost', BEZ_PERMISSION_CHANGE);
            }

            if (($this->type != 'program' && $this->coordinator == $this->model->user_nick) ||
                ($this->model->get_level() >= BEZ_AUTH_LEADER)) {
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('plan_date', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('start_time', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('finish_time', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('all_day_event', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('task_program_id', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('cost', BEZ_PERMISSION_CHANGE);

                $this->acl->grant('assignee', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('participants', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('state', BEZ_PERMISSION_CHANGE);
            }
        }
    }

    public function update_virutal() {
        if ($this->state == 'done') {
            $this->priority = '';
        } else {
            $now = date('Y-m-d');
            $plus_1_month = date('Y-m-d', strtotime('+1 month'));

            if ($this->plan_date >= $plus_1_month) {
                $this->priority = '0';
            } elseif ($this->plan_date >= $now) {
                $this->priority = '1';
            } else {
                $this->priority = '2';
            }
        }
    }
	
	public function set_data($post, $filter=NULL) {
        //all day event
        if (!isset($post['all_day_event'])) {
            $post['all_day_event'] = '0';
        }

        parent::set_data($post);

        $this->content_html = p_render('xhtml',p_get_instructions($this->content), $ignore);

        if (!isset($post['assignee'])) {
            $this->assignee = $this->model->user_nick;
        }

        //update dates
        $this->last_modification_date = date('c');
        $this->last_activity_date = $this->last_modification_date;

        //update virtual
        $this->update_virutal();

		return true;
	}

    public function set_state($state) {
	    if ($this->acl_of('state') < BEZ_PERMISSION_CHANGE) {
	        throw new PermissionDeniedException();
        }

        if (!in_array($state, array('opened', 'done'))) {
	        throw new ValidationException('task', array('sholud be opened or done'));
        }

        //nothing to do
        if ($state == $this->state) {
	        return;
        }

        if ($state == 'done') {
            $this->model->sqlite->query("UPDATE {$this->get_table_name()} SET state=?, closed_by=?, close_date=? WHERE id=?",
                $state,
                $this->model->user_nick,
                date('c'),
                $this->id);
        //reopen the task
        } else {
            $this->model->sqlite->query("UPDATE {$this->get_table_name()} SET state=? WHERE id=?", $state, $this->id);
        }

        $this->state = $state;
    }

    public function update_last_activity() {
        $this->last_activity_date = date('c');
        $this->model->sqlite->query('UPDATE task SET last_activity_date=? WHERE id=?',
                                    $this->last_activity_date, $this->id);
    }

    public function can_add_comments() {
        if ($this->thread_id != '' && $this->thread->state == 'closed') {
            return false;
        }

        if ($this->state == 'opened' ||
            ($this->state == 'done' &&
                $this->acl_of('state') >= BEZ_PERMISSION_CHANGE)) {
            return true;
        }

        return false;
    }

    public function can_add_participants() {
        return in_array($this->state, array('opened'));
    }

    public function get_participants($filter='') {
        if ($this->id === NULL) {
            return array();
        }

        $sql = 'SELECT * FROM task_participant WHERE';
        $possible_flags = array('original_poster', 'assignee', 'commentator', 'subscribent');
        if ($filter != '') {
            if (!in_array($filter, $possible_flags)) {
                throw new \Exception("unknown flag $filter");
            }
            $sql .= " $filter=1 AND";
        }
        $sql .= ' task_id=? ORDER BY user_id';

        $r = $this->model->sqlite->query($sql, $this->id);
        $pars = $this->model->sqlite->res2arr($r);
        $participants = array();
        foreach ($pars as $par) {
            $participants[$par['user_id']] = $par;
        }

        return $participants;
    }

    public function get_participant($user_id) {
        if ($this->id === NULL) {
            return array();
        }

        $r = $this->model->sqlite->query('SELECT * FROM task_participant WHERE task_id=? AND user_id=?', $this->id, $user_id);
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

        $possible_flags = array('original_poster', 'assignee', 'commentator', 'subscribent');
        if (array_intersect($flags, $possible_flags) != $flags) {
            throw new \Exception('unknown flags');
        }

        $set = implode(',', array_map(function ($v) { return "$v=0"; }, $flags));

        $sql = "UPDATE task_participant SET $set WHERE task_id=? AND user_id=?";
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

        $possible_flags = array('original_poster', 'assignee', 'commentator', 'subscribent');
        if (array_intersect($flags, $possible_flags) != $flags) {
            throw new \Exception('unknown flags');
        }

        $participant = $this->get_participant($user_id);
        if ($participant == false) {
            $participant = array_fill_keys($possible_flags, 0);

            $participant['task_id'] = $this->id;
            $participant['user_id'] = $user_id;
            $participant['added_by'] = $this->model->user_nick;
            $participant['added_date'] = date('c');
        }
        $values = array_merge($participant, array_fill_keys($flags, 1));

        $keys = join(',', array_keys($values));
        $vals = join(',', array_fill(0,count($values),'?'));

        $sql = "REPLACE INTO task_participant ($keys) VALUES ($vals)";
        $this->model->sqlite->query($sql, array_values($values));
    }

    public function invite($client) {
        $this->set_participant_flags($client, array('subscribent'));
        $this->mail_notify_invite($client);
    }
    
    public function mail_notify($content, $users=false, $attachedImages=array()) {
        $mailer = new \Mailer();
        $mailer->setBody($content, array(), array(), $content, false);
        
        if ($users === FALSE) {
            $users = $this->get_participants('subscribent');
            
            //don't notify current user
            unset($users[$this->model->user_nick]);
        }
        
        $emails = array_map(function($user) {
            if (is_array($user)) {
                $user = $user['user_id'];
            }
            return $this->model->userFactory->get_user_email($user);
        }, $users);

        $mailer->to($emails);
        $mailer->subject('#z'.$this->id. ' ' . $this->task_program_name);

        //add images
        foreach ($attachedImages as $img) {
            $mailer->attachFile($img['path'], $img['mime'], $img['name'], $img['embed']);
        }

        $send = $mailer->send();
        if ($send === false) {
            //this may mean empty $emails
            //throw new Exception("can't send email");
        }
    }

    public function mail_task_box(&$attachedImages) {
        $tpl = $this->model->action->get_tpl();

        //render style
        $less = new \lessc();
        $less->addImportDir(DOKU_PLUGIN . 'bez/style/');
        $style = $less->compileFile(DOKU_PLUGIN . 'bez/style/task.less');

        //render content for mail
        $old_content_html = $this->content_html;
        $this->content_html = p_render('bez_xhtmlmail', p_get_instructions($this->content), $info);
        $attachedImages = array_merge($attachedImages, $info['img']);

        $tpl->set('task', $this);
        $tpl->set('style', $style);
        $tpl->set('no_actions', true);
        $task_box = $this->model->action->bez_tpl_include('task_box', true);

        $this->content_html = $old_content_html;

        return $task_box;
    }

    public function mail_task(&$attachedImages) {
        $tpl = $this->model->action->get_tpl();

        $task_box = $this->mail_task_box($attachedImages);
        $tpl->set('content', $task_box);
        $content = $this->model->action->bez_tpl_include('mail/task', true);

        return $content;
    }

    public function mail_notify_assignee() {
        $tpl = $this->model->action->get_tpl();

        //we don't want who
        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', 'mail_task_assignee');
        $attachedImages = array();
        $content = $this->mail_task($attachedImages);
        $this->mail_notify($content, array($this->assignee), $attachedImages);
    }

    public function mail_notify_remind($users=false) {
        $tpl = $this->model->action->get_tpl();

        //we don't want who
        $tpl->set('who', '');
        $tpl->set('action', 'mail_task_remind');
        $attachedImages = array();
        $content = $this->mail_task($attachedImages);
        $this->mail_notify($content, $users, $attachedImages);
    }
    
    public function mail_notify_invite($client) {       
        $users = array($client);
        $tpl = $this->model->action->get_tpl();

        //we don't want who
        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', 'mail_task_invite');
        $attachedImages = array();
        $content = $this->mail_task($attachedImages);
        $this->mail_notify($content, $users, $attachedImages);
    }

    public function mail_notify_change_state($action='') {
        $tpl = $this->model->action->get_tpl();

        $tpl->set('who', $this->model->user_nick);
        $tpl->set('action', $action);
        $attachedImages = array();
        $content = $this->mail_task($attachedImages);
        $this->mail_notify($content, false, $attachedImages);
    }

}
