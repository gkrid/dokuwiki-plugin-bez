<?php

/*
 * Task coordinator is taken from tasktypes
 */
//require_once 'entity.php';
//
//class BEZ_mdl_Dummy_Task extends BEZ_mdl_Entity  {
//    protected $coordinator;
//
//    function __construct($model, $defaults=array()) {
//        parent::__construct($model);
//
//        if (isset($defaults['issue'])) {
//            $issue = $this->model->issues->get_one($defaults['issue']);
//            $this->coordinator = $issue->coordinator;
//        } else {
//            $this->coordinator = '';
//        }
//    }
//
//    public function __get($property) {
//		if ($property === 'coordinator') {
//            return $this->coordinator;
//        }
//        parent::__get($property);
//	}
//}

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


//    private function state_string() {
//		switch($this->state) {
//            case '0':         return 'task_opened';
//            case '-outdated': return 'task_outdated';
//            case '1':         return 'task_done';
//            case '2':         return 'task_rejected';
//        }
//	}
//
//	private function action_string() {
//		switch($this->action) {
//			case '0': return 'correction';
//			case '1': return 'corrective_action';
//			case '2': return 'preventive_action';
//			case '3': return 'programme';
//		}
//	}
//
//    public function cost_localized() {
//        if ($this->cost === '') {
//            return '';
//        }
//
//        return sprintf('%.2f', (float)$this->cost);
//    }
//
//    private function update_virtual_columns() {
//		$this->state_string = $this->model->action->getLang($this->state_string());
//        $this->action_string = $this->model->action->getLang($this->action_string());
//        $this->tasktype_string = $this->model->tasktypes->get_one($this->tasktype)->type;
//    }
//
//    public function user_is_executor() {
//        if ($this->executor === $this->model->user_nick ||
//           $this->model->acl->get_level() >= BEZ_AUTH_ADMIN) {
//            return true;
//        }
//    }
		
	//by defaults you can set: cause, tasktype and issue
	//tasktype is required
	public function __construct($model, $defaults=array()) {
		parent::__construct($model, $defaults);

				
		//array(filter, NULL)
		$this->validator->set_rules(array(
//			'reporter' => array(array('dw_user'), 'NOT NULL'),
//			'date' => array(array('unix_timestamp'), 'NOT NULL'),
//			'close_date' => array(array('unix_timestamp'), 'NULL'),
//			'cause' => array(array('numeric'), 'NULL'),
			
//			'executor' => array(array('dw_user'), 'NOT NULL'),
			
//			'issue' => array(array('numeric'), 'NULL'),

            'assignee' => array(array('dw_user'), 'NOT NULL'),
            'cost' => array(array('numeric'), 'NULL'),
			'plan_date' => array(array('iso_date'), 'NOT NULL'),
			'all_day_event' => array(array('select', array('0', '1')), 'NOT NULL'), 
			'start_time' => array(array('time'), 'NULL'), 
			'finish_time' => array(array('time'), 'NULL'),
            'content' => array(array('length', 10000), 'NOT NULL'),
            'thread_comment_id' => array(array('numeric'), 'NULL'),
            'task_program_id' => array(array('numeric'), 'NULL')
			
//			'state' => array(array('select', array('0', '1', '2')), 'NULL'),
//			'reason' => array(array('length', 10000), 'NULL'),
			
//			'coordinator' => array(array('dw_user', array('-none')), 'NOT NULL'),
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

//			//meta
//			$this->reporter = $this->model->user_nick;
//			$this->date = time();
//
//			$this->state = '0';
//			$this->all_day_event = '1';
//
//            //throws ValidationException
//			$this->issue = $this->validator->validate_field('issue', $defaults['issue']);
//
//            if ($this->issue !== '') {
//                $issue = $this->model->issues->get_one($defaults['issue']);
//			    $this->coordinator = $issue->coordinator;
//            } else {
//                $this->coordinator = '';
//            }
//
//			//throws ValidationException
//			$this->validator->validate_field('cause', $defaults['cause']);
//			$this->cause = $defaults['cause'];
//
//            //by default reporter is a executor
//            $this->executor = $this->reporter;
            

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


//        //close_date required
//		if ($this->state !== '0') {
//			$this->validator->set_rules(array(
//				'close_date' => array(array('unix_timestamp'), 'NOT NULL')
//			));
//		}
        
        //explode subscribents
//        if ($this->subscribents !== NULL) {
//			$exp_part = explode(',', $this->subscribents);
//			foreach ($exp_part as $subscribent) {
//				$this->subscribents_array[$subscribent] = $subscribent;
//			}
//		}
//
//		//we've created empty object
//		if ($this->id === NULL) {
//            //throws ValidationException
//			$this->validator->validate_field('tasktype', $defaults['tasktype']);
//			$this->tasktype = $defaults['tasktype'];
//		}
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
		
		//specjalne reguły
//		if ($this->issue === '') {
//			$this->cause = '';
//		}
		
		//set parsed
//		$this->task_cache = $this->helper->wiki_parse($this->task);
//		$this->reason_cache = $this->helper->wiki_parse($this->reason);
        
        //update virtuals
        //$this->update_virtual_columns();
			
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
    
//    public function update_cache() {
//        if ($this->model->acl->get_level() < BEZ_AUTH_ADMIN) {
//			return false;
//		}
//		$this->task_cache = $this->helper->wiki_parse($this->task);
//		$this->reason_cache = $this->helper->wiki_parse($this->reason);
//	}
//
//	public function set_state($data) {
//		//reason is required while changing state
//		if ($data['state'] === '2') {
//			$this->validator->set_rules(array(
//				'reason' => array(array('length', 10000), 'NOT NULL')
//			));
//		}
//
//		$val_data = $this->validator->validate($data, array('state', 'reason'));
//		if ($val_data === false) {
//			throw new ValidationException('tasks', $this->validator->get_errors());
//		}
//
//		//if state is changed
//		if ($this->state != $data['state']) {
//			$this->close_date = time();
//		}
//
//        $this->set_property_array($val_data);
//		$this->reason_cache = $this->helper->wiki_parse($this->reason);
//
//        //update virtuals
//        $this->update_virtual_columns();
//
//		return true;
//	}
//
//    public function get_meta_fields() {
//        return array('reporter', 'date', 'close_date');
//    }
//
//    public function set_meta($post) {
//
//        if (isset($post['date'])) {
//            $unix = strtotime($post['date']);
//            //if $unix === false validator will catch it
//            if ($unix !== false) {
//                $post['date'] = (string)$unix;
//            }
//        }
//
//        if (isset($post['close_date'])) {
//            $unix = strtotime($post['close_date']);
//            //if $unix === false validator will catch it
//            if ($unix !== false) {
//                $post['close_date'] = (string)$unix;
//            }
//        }
//
//        parent::set_data($post, $this->get_meta_fields());
//    }
//
//    public function is_subscribent($user=NULL) {
//		if ($user === NULL) {
//			$user = $this->model->user_nick;
//		}
//		if (in_array($user, $this->subscribents_array)) {
//			return true;
//		}
//		return false;
//	}
//
//    public function get_subscribents() {
//        return $this->subscribents_array;
//    }
//
//    public function get_participants() {
//        $subscribents = array_merge(array($this->reporter, $this->executor),
//                            $this->subscribents_array);
//        $full_names = array();
//        foreach ($subscribents as $par) {
//			$name = $this->model->users->get_user_full_name($par);
//			if ($name == '') {
//				$full_names[$par] = $par;
//			} else {
//				$full_names[$par] = $name;
//			}
//		}
//        ksort($full_names);
//        return $full_names;
//    }
//
//    public function remove_subscribent($subscribent) {
//		if ($subscribent !== $this->model->user_nick &&
//            $this->acl_of('subscribents') < BEZ_PERMISSION_CHANGE) {
//			throw new PermissionDeniedException();
//		}
//
//        if ($this->issue != '') {
//            throw new ConsistencyViolationException('cannot modify subscribents from issue related tasks');
//        }
//
//        if (!isset($this->subscribents_array[$subscribent])) {
//            throw new ConsistencyViolationException('user '.$subscribent.' wasn\'t subscriber so cannot be removed');
//        }
//
//		unset($this->subscribents_array[$subscribent]);
//		$this->subscribents = implode(',', $this->subscribents_array);
//	}
//
//    public function add_subscribent($subscribent) {
//		if ($subscribent !== $this->model->user_nick &&
//            $this->acl_of('subscribents') < BEZ_PERMISSION_CHANGE) {
//			throw new PermissionDeniedException();
//		}
//
//        if ($this->issue != '') {
//            throw new ConsistencyViolationException('cannot add subscribents to issue related tasks');
//        }
//
//		if ($this->model->users->exists($subscribent) &&
//            !in_array($subscribent, $this->subscribents_array)) {
//			$this->subscribents_array[$subscribent] = $subscribent;
//			$this->subscribents = implode(',', $this->subscribents_array);
//
//            return true;
//		}
//
//        return false;
//	}

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



//		if (! (	$this->user_is_coordinator() ||
//				$participant === $this->model->user_nick ||
//                $participant === $this->coordinator) //dodajemy nowego koordynatora
//			) {
//			throw new PermissionDeniedException();
//		}
//		if ($this->model->users->exists($participant)) {
//			$this->participants_array[$participant] = $participant;
//			$this->participants = implode(',', $this->participants_array);
//		}
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
//        if ($issue_obj !== NULL && $issue_obj->id !== $this->issue) {
//            throw new Exception('issue object id and task->issue does not match');
//        }
        
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
