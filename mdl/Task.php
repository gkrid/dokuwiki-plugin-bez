<?php

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\Mailer;
use dokuwiki\plugin\bez\meta\PermissionDeniedException;
use dokuwiki\plugin\bez\meta\ValidationException;

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
                $this->thread = $this->model->thread_commentFactory->get_one($this->thread_comment_id);
            }
            return $this->thread_comment;

        } elseif($property == 'priority' || $property == 'coordinator' || $property == 'task_program_name') {
            return $this->$property;
        }
        return parent::__get($property);
    }

	public function __construct($model, $defaults=array()) {
		parent::__construct($model, $defaults);

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
        //we get object form db
		} else {

            if (isset($defaults['thread']) && $this->thread_id == $defaults['thread']->id) {
                $this->thread = $defaults['thread'];
            }

            if (isset($defaults['thread_comment']) && $this->thread_comment_id == $defaults['thread_comment']->id) {
                $this->thread_comment = $defaults['thread_comment'];
            }

        }

		if ($this->thread_id == '') {
			$this->validator->set_rules(array(
				'task_program_id' => array(array('numeric'), 'NOT NULL'),
			));
		    //this field is unused in program tasks
            $this->validator->delete_rule('thread_comment_id');
        }
	}
	
	
	public function set_data($post, $filter=NULL) {        
        parent::set_data($post);

        $this->content_html = p_render('xhtml',p_get_instructions($this->content), $ignore);

        //update dates
        $this->last_modification_date = date('c');
        $this->last_activity_date = $this->last_modification_date;

        //all day event
        if (!isset($post['all_day_event'])) {
            $post['all_day_event'] = '0';
        }
			
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

    public function get_participants($filter='') {
        if ($this->acl_of('participants') < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }
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
        if ($this->acl_of('participants') < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }
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
        if ($this->acl_of('participants') < BEZ_PERMISSION_CHANGE) {
            throw new PermissionDeniedException();
        }

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
        if ($this->acl_of('participants') < BEZ_PERMISSION_CHANGE) {
            throw new PermissionDeniedException();
        }

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
    
    private function mail_notify($replacements=array(), $users=false) {        
        $plain = io_readFile($this->model->action->localFN('task-notification'));
        $html = io_readFile($this->model->action->localFN('task-notification', 'html'));
        
        $task_link = $this->model->action->url('task', 'tid', $this->id);
        
        $reps = array(
                        'task_id' => $this->id,
                        'task_link' => $task_link,
                        'who' => $this->original_poster
                     );
        
        //$replacements can override $reps
        $rep = array_merge($reps, $replacements);

        if (!isset($rep['who_full_name'])) {
            $rep['who_full_name'] =
                $this->model->userFactory->get_user_full_name($rep['who']);
        }
        
        //auto title
        if (!isset($rep['subject'])) {
//            if (isset($rep['content'])) {
//                $rep['subject'] =  array_shift(explode('.', $rep['content'], 2));
//            }
            $rep['subject'] = '#z'.$this->id. ' ' . $this->task_program_name;
        }
       
        //we must do it manually becouse Mailer uses htmlspecialchars()
        $html = str_replace('@TASK_TABLE@', $rep['task_table'], $html);
        
        $mailer = new Mailer();
        $mailer->setBody($plain, $rep, $rep, $html, false);
        
        if ($users === FALSE) {
            $users = $this->get_participants('subscribent');
            
            //don't notify current user
            unset($users[$this->model->user_nick]);
        }
        
        $emails = array_map(function($user) {
            return $this->model->userFactory->get_user_email($user);
        }, $users);

        $mailer->to($emails);
        $mailer->subject($rep['subject']);

        $send = $mailer->send();
        if ($send === false) {
            //this may mean empty $emails
            //throw new Exception("can't send email");
        }
    }

    protected function bez_html_array_to_style_list($arr) {
        $output = '';
        foreach ($arr as $k => $v) {
            $output .= $k.': '. $v . ';';
        }
        return $output;
    }

    protected function bez_html_irrtable($style) {
        $argv = func_get_args();
        $argc = func_num_args();
        if (isset($style['table'])) {
            $output = '<table style="'.self::bez_html_array_to_style_list($style['table']).'">';
        } else {
            $output = '<table>';
        }

        $tr_style  = '';
        if (isset($style['tr'])) {
            $tr_style = 'style="'.self::bez_html_array_to_style_list($style['tr']).'"';
        }

        $td_style  = '';
        if (isset($style['td'])) {
            $td_style = 'style="'.self::bez_html_array_to_style_list($style['td']).'"';
        }

        $row_max = 0;

        for ($i = 1; $i < $argc; $i++) {
            $row = $argv[$i];
            $c = count($row);
            if ($c > $row_max) {
                $row_max = $c;
            }
        }

        for ($j = 1; $j < $argc; $j++) {
            $row = $argv[$j];
            $output .= '<tr '.$tr_style.'>' . NL;
            $c = count($row);
            for ($i = 0; $i < $c; $i++) {
                //last element
                if ($i === $c - 1 && $c < $row_max) {
                    $output .= '<td '.$td_style.' colspan="' . ( $row_max - $c + 1 ) . '">' . NL;
                } else {
                    $output .= '<td '.$td_style.'>' . NL;
                }
                $output .= $row[$i] . NL;
                $output .= '</td>' . NL;
            }
            $output .= '</tr>' . NL;
        }
        $output .= '</table>' . NL;
        return $output;
    }
    
    public function mail_notify_task_box($users=false, $replacements=array()) {
       $top_row = array(
            '<strong>'.$this->model->action->getLang('executor').': </strong>' . 
            $this->model->userFactory->get_user_full_name($this->assignee),

            '<strong>'.$this->model->action->getLang('reporter').': </strong>' . 
            $this->model->userFactory->get_user_full_name($this->original_poster)
        );

        if ($this->task_program_name != '') {
            $top_row[] =
                '<strong>'.$this->model->action->getLang('task_type').': </strong>' . 
                $this->task_program_name;
        }

        if ($this->cost != '') {
            $top_row[] =
                '<strong>'.$this->model->action->getLang('cost').': </strong>' . 
                $this->cost;
        }

        //BOTTOM ROW
        $bottom_row = array(
            '<strong>'.$this->model->action->getLang('plan_date').': </strong>' . 
            $this->plan_date
        );			

        if ($this->all_day_event == '0') {
            $bottom_row[] =
                '<strong>'.$this->model->action->getLang('start_time').': </strong>' . 
                $this->start_time;
            $bottom_row[] =
                '<strong>'.$this->model->action->getLang('finish_time').': </strong>' . 
                $this->finish_time;
        }
        
        $rep = array(
            'content' => $this->content,
            'content_html' =>
                '<h2 style="font-size: 1.2em;">'.
	               '<a href="'.$this->model->action->url('task', 'tid', $this->id).'">' .
		              '#z'.$this->id . 
	               '</a> ' . 
	lcfirst($this->model->action->getLang('task_type_' . $this->type)) . ' ' .
    '(' . 
        lcfirst($this->model->action->getLang('task_' . $this->state)) .
    ')' .      
                '</h2>' . 
                self::bez_html_irrtable(array(
                    'table' => array(
                        'border-collapse' => 'collapse',
                        'font-size' => '0.8em',
                        'width' => '100%'
                    ),
                    'td' => array(
                        'border-top' => '1px solid #8bbcbc',
                        'border-bottom' => '1px solid #8bbcbc',
                        'padding' => '.3em .5em'
                    )
                ), $top_row, $bottom_row) . $this->content_html,
            'who' => $this->model->user_nick,
            'when' => $this->create_date,
            'custom_content' => true
        );
        
        $rep['action_color'] = '#e4f4f4';
        $rep['action_border_color'] = '#8bbcbc';
        
        //$replacements can override $reps
        $rep = array_merge($rep, $replacements);
        
//        if ($this->thread == NULL) {
//            $this->mail_notify($rep, $users);
//        } else {
//            $this->thread->mail_notify($rep);
//        }
        $this->mail_notify($rep, $users);
    }
    
    public function mail_notify_subscribents($replacements=array()) {
        $this->mail_notify_task_box(false, $replacements);
    }
    
    public function mail_notify_add($users=false, $replacements=array()) {
        $replacements['action'] = $this->model->action->getLang('mail_task_added');
        $this->mail_notify_task_box($users, $replacements);
    }
    
    public function mail_notify_remind($users=false) {
        $replacements = array();
        
        $replacements['action'] = $this->model->action->getLang('mail_task_remind');
        //we don't want any who
        $replacements['who_full_name'] = '';
        
        //$users = array($this->executor);
        $this->mail_notify_task_box($users, $replacements);
    }
    
    public function mail_notify_invite($client) {       
        $replacements = array();
        
        $replacements['action'] = $this->model->action->getLang('mail_task_invite');
        
        $users = array($client);
        $this->mail_notify_task_box($users, $replacements);
    }
}
