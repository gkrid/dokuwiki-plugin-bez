<?php

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\Mailer;
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
        array_push($cols, 'label_id', 'label_name');
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
    }
	
	public function __construct($model, $defaults=array()) {
		parent::__construct($model);

        $this->validator->set_rules(array(
            'coordinator' => array(array('dw_user'), 'NULL'),
            'title' => array(array('length', 200), 'NOT NULL'),
            'content' => array(array('length', 10000), 'NOT NULL')
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
                }
            }

		    if ($this->state == 'proposal' && $this->original_poster == $this->model->user_nick) {
                $this->acl->grant('title', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
            }

            if ($this->coordinator == $this->model->user_nick) {
                $this->acl->grant('title', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('coordinator', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('label_id', BEZ_PERMISSION_CHANGE);
                $this->acl->grant('private', BEZ_PERMISSION_CHANGE);

                $this->acl->grant('state', BEZ_PERMISSION_CHANGE);
            }
        }
	}
	
	public function set_data($data, $filter=NULL) {
        parent::set_data($data, $filter=NULL);

        if (isset($data['coordinator']) && $this->state == 'proposal') {
            $this->state = 'opened';
        }

		$this->content_html = p_render('xhtml',p_get_instructions($this->content), $ignore);

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

    public function set_private_flag($flag) {
        $private = '0';
        if ($flag) {
            $private = '1';
        }

        if ($private == $this->private) {
            return;
        }

        $this->model->sqlite->query("UPDATE {$this->get_table_name()} SET private=? WHERE id=?", $private, $this->id);

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
        $sql .= ' thread_id=? ORDER BY user_id';

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

        $r = $this->model->sqlite->query('SELECT * FROM thread_participant WHERE thread_id=? AND user_id=?', $this->id, $user_id);
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

        $participant = $this->get_participant($user_id);
        if ($participant == false) {
            $participant = array_fill_keys($possible_flags, 0);

            $participant['thread_id'] = $this->id;
            $participant['user_id'] = $user_id;
            $participant['added_by'] = $this->model->user_nick;
            $participant['added_date'] = date('c');
        }
        $values = array_merge($participant, array_fill_keys($flags, 1));

        $keys = join(',', array_keys($values));
        $vals = join(',', array_fill(0,count($values),'?'));

        $sql = "REPLACE INTO thread_participant ($keys) VALUES ($vals)";
        $this->model->sqlite->query($sql, array_values($values));
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
        $r = $this->model->sqlite->query("SELECT id FROM thread_comment WHERE type LIKE 'cause_%' AND thread_id=?",
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

    public function can_be_closed() {
        $res = $this->model->sqlite->query("SELECT thread_comment.id FROM thread_comment
                               LEFT JOIN task ON thread_comment.id = task.thread_comment_id
                               WHERE thread_comment.thread_id = ? AND
                                     thread_comment.type LIKE 'cause_%' AND task.id IS NULL", $this->id);

        $causes_without_tasks = $this->model->sqlite->res2row($res) ? true : false;
        return $this->state == 'done' &&
            ! $causes_without_tasks;

    }

    public function can_be_rejected() {
        return $this->state != 'rejected' && $this->task_count == 0;
    }

    public function can_be_reopened() {
        return in_array($this->state, array('closed', 'rejected'));
    }

    public function closing_comment() {
        $r = $this->model->thread_commentFactory->get_from_thread($this, array(), 'id', true, 1);
        $thread_comment = $r->fetch();

        return $thread_comment->content_html;
    }

    //http://data.agaric.com/capture-all-sent-mail-locally-postfix
    //https://askubuntu.com/questions/192572/how-do-i-read-local-email-in-thunderbird
    public function mail_notify($replacements=array(), $users=false, $attachedImages=array()) {
        $plain = io_readFile($this->model->action->localFN('thread-notification'));
        $html = io_readFile($this->model->action->localFN('thread-notification', 'html'));

        $thread_reps = array(
                                'thread_id' => $this->id,
                                'thread_link' => $this->model->action->url('thread', 'id', $this->id),
                                'thread_unsubscribe' =>
                                    $this->model->action->url('thread', 'id', $this->id, 'action', 'unsubscribe'),
                                'custom_content' => false,
                                'action_border_color' => 'transparent',
                                'action_color' => 'transparent',
                           );

        //$replacements can override $issue_reps
        $rep = array_merge($thread_reps, $replacements);
        //auto title
        if (!isset($rep['subject'])) {
            $rep['subject'] =  '#'.$this->id. ' ' .$this->title;
        }
        if (!isset($rep['content_html'])) {
            $rep['content_html'] = $rep['content'];
        }
        if (!isset($rep['who_full_name'])) {
            $rep['who_full_name'] =
                $this->model->userFactory->get_user_full_name($rep['who']);
        }

        //format when
        $rep['when'] =  dformat(strtotime($rep['when']), '%Y-%m-%d %H:%M');

        if ($rep['custom_content'] === false) {
            $html = str_replace('@CONTENT_HTML@', '
                <div style="margin: 5px 0;">
                    <strong>@WHO_FULL_NAME@</strong> <br>
                    <span style="color: #888">@WHEN@</span>
                </div>
                @CONTENT_HTML@
            ', $html);
        }

        //we must do it manually becouse Mailer uses htmlspecialchars()
        $html = str_replace('@CONTENT_HTML@', $rep['content_html'], $html);

        $mailer = new Mailer();
        $mailer->setBody($plain, $rep, $rep, $html, false);

        if ($users == FALSE) {

            $users = $this->get_participants('subscribent');
            //don't notify myself
            unset($users[$this->model->user_nick]);
        }

        $emails = array_map(function($user) {
            if (is_array($user)) {
                $user = $user['user_id'];
            }
            return $this->model->userFactory->get_user_email($user);
        }, $users);


        $mailer->to($emails);
        $mailer->subject($rep['subject']);

        foreach ($attachedImages as $img) {
            $mailer->attachFile($img['path'], $img['mime'], $img['name'], $img['embed']);
        }

        $send = $mailer->send();
        if ($send === false) {
            //this may mean empty $emails
            //throw new Exception("can't send email");
        }
    }

    protected function mail_issue_box_reps(&$replacements, &$attachedImages) {
        $replacements['custom_content'] = true;

        $html =  '<h2 style="font-size: 1.2em;">';
	    $html .=    '<a style="font-size:115%" href="@THREAD_LINK@">#@THREAD_ID@</a> ';

        if ( ! empty($this->type_string)) {
            $html .= $this->type_string;
        } else {
            $html .= '<i style="color: #777"> '.
                        $this->model->action->getLang('issue_type_no_specified').
                    '</i>';
        }

        $html .= ' ('. $this->model->action->getLang('state_' . $this->state ) .') ';

        $html .= '<span style="color: #777; font-weight: normal; font-size: 90%;">';
        $html .= $this->model->action->getLang('coordinator') . ': ';
        $html .= '<span style="font-weight: bold;">';

        if ($this->state == 'proposal') {
            $html .= '<i style="font-weight: normal;">' .
                $this->model->action->getLang('proposal') .
                '</i>';
        } else {
            $html .= $this->model->userFactory->get_user_full_name($this->coordinator);
        }
        $html .= '</span></span></h2>';

        $html .= '<h2 style="font-size: 1.2em;border-bottom: 1px solid @ACTION_BORDER_COLOR@">' . $this->title . '</h2>';

        $html .= p_render('bez_xhtmlmail', p_get_instructions($this->content), $info);
        $attachedImages = array_merge($attachedImages, $info['img']);

        $replacements['content_html'] = $html;


         switch ($this->priority) {
            case '0':
                $replacements['action_color'] = '#F8E8E8';
                $replacements['action_border_color'] = '#F0AFAD';
                break;
            case '1':
                $replacements['action_color'] = '#ffd';
                $replacements['action_border_color'] = '#dd9';
                break;
            case '2':
                $replacements['action_color'] = '#EEF6F0';
                $replacements['action_border_color'] = '#B0D2B6';
                break;
            case 'None':
                $replacements['action_color'] = '#e7f1ff';
                $replacements['action_border_color'] = '#a3c8ff';
                break;
            default:
                $replacements['action_color'] = '#fff';
                $replacements['action_border_color'] = '#bbb';
                break;
        }
    }

    public function mail_notify_change_state() {
        $replacements = array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_notify_change_state_action')
        );
        $attachedImages = array();
        $this->mail_issue_box_reps($replacements, $attachedImages);
        $this->mail_notify($replacements, false, $attachedImages);
    }

    public function mail_notify_invite($client) {
        $replacements = array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_notify_invite_action')
        );
        $attachedImages = array();
        $this->mail_issue_box_reps($replacements, $attachedImages);
        $this->mail_notify($replacements, array($client), $attachedImages);
    }

    public function mail_inform_coordinator() {
        $replacements = array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_inform_coordinator_action')
        );
        $attachedImages = array();
        $this->mail_issue_box_reps($replacements, $attachedImages);
        $this->mail_notify($replacements, array($this->coordinator), $attachedImages);
    }

    public function mail_notify_issue_inactive($users=false) {
        $replacements = array(
            'who' => '',
            'action' => $this->model->action->getLang('mail_mail_notify_issue_inactive')
        );
        $attachedImages = array();
        $this->mail_issue_box_reps($replacements, $attachedImages);
        $this->mail_notify($replacements, $users, $attachedImages);
    }

}
