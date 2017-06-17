<?php
 
if(!defined('DOKU_INC')) die();

require_once 'entity.php';


class BEZ_mdl_Dummy_Issue extends BEZ_mdl_Dummy_Entity  {
    function get_table_name() {
        return 'issues';
    }
}

class BEZ_mdl_Issue extends BEZ_mdl_Entity {
	
	//meta
	protected $reporter, $date, $last_mod, $last_activity,
				$participants, $subscribents, $coordinator;
	
	
	//data
	protected $title, $description, $type;
	
	//state
	protected $state, $opinion;
	
	//virtual
	protected $participants_array, $subscribents_array,
				$assigned_tasks_count, $opened_tasks_count,
				$priority;
    
    
    public function get_table_name() {
        return 'issues';
    }
	
	protected $parse_int = array('assigned_tasks_count', 'opened_tasks_count');
	
	public function get_columns() {
		return array('id',
					'reporter', 'date', 'last_mod', 'last_activity',
					'participants', 'subscribents', 'coordinator',
					'title', 'description', 'description_cache', 'type',
					'state', 'opinion', 'opinion_cache');
	}
	
	public function get_virtual_columns() {
		return array('participants_array', 'subscribents_array',
					'assigned_tasks_count',	'opened_tasks_count',
					'priority');
	}
    
    public function full_state() {
        if (strpos($this->coordinator, '-') === 0) {
            return $this->coordinator;
        } else {
            return $this->state;
        }
    }
    
    public function user_is_coordinator() {
        if ($this->coordinator === $this->model->user_nick ||
           $this->model->acl->get_level() >= BEZ_AUTH_ADMIN) {
            return true;
        }
    }
	
	public function __construct($model, $defaults=array()) {
		parent::__construct($model);
		
		$this->validator->set_rules(array(
			'title' => array(array('length', 200), 'NOT NULL'),
			'description' => array(array('length', 10000), 'NOT NULL'),
			'state' => array(array('select', array('0', '1', '2')), 'NULL'),
			'opinion' => array(array('length', 10000), 'NOT NULL'),
			'type' => array(array('numeric'), 'NULL'),
			'coordinator' => array(array('dw_user'), 'NOT NULL'),
			'reporter' => array(array('dw_user'), 'NOT NULL'),
			'date' => array(array('unix_timestamp'), 'NOT NULL'),
			'last_mod' => array(array('unix_timestamp'), 'NULL'),
			'last_activity' => array(array('sqlite_datetime'), 'NOT NULL')
		));
		
		//we've created empty object (new record)
		if ($this->id === NULL) {
			$this->reporter = $this->model->user_nick;
			$this->date = time();
			
			$this->update_last_activity();
			
			$this->state = '0';
            
            $input = array('title', 'description', 'type');
            if ($this->model->acl->get_level() >= BEZ_AUTH_LEADER) {
				$input[] = 'coordinator';
			}
            
            $val_data = $this->validator->validate($defaults, $input);
            if ($val_data === false)  {
                throw new ValidationException('issues',	$this->validator->get_errors());
            }
            
            $this->set_property_array($val_data);
            
            if (!isset($val_data['coordinator'])) {
                $this->coordinator = '-proposal';
            }
            


			
//			$input = array('title', 'description', 'type');
//			if ($this->auth->get_level() >= 20) {
//				$input[] = 'coordinator';
//			}
//			
//			$val_data = $this->validator->validate($defaults, $input);
//			
//			if ($val_data === false) {
//				throw new ValidationException('issues', $this->validator->get_errors());
//			}
//			
//			$this->set_property_array($val_data);
            
            
            
			$this->description_cache = $this->helper->wiki_parse($this->description);
			
			$this->add_participant($this->reporter);
			$this->add_subscribent($this->reporter);
            if ($this->coordinator !== '-proposal') {
                $this->add_participant($this->coordinator);
                $this->add_subscribent($this->coordinator);
            }
			
//			if ($this->auth->get_level() >= 20) {
//				$this->coordinator = $val_data['coordinator'];
//				if ($val_data['coordinator'] !== '-proposal') {
//					$this->add_participant($val_data['coordinator']);
//					$this->add_subscribent($val_data['coordinator']);
//				}
//			} else {
//				$this->coordinator = '-proposal';
//			}
		}
		
		//$this->auth->set_coordinator($this->coordinator);
		
		$this->participants_array = array();
		if ($this->participants !== NULL) {
			$exp_part = explode(',', $this->participants);
			foreach ($exp_part as $participant) {
				$this->participants_array[$participant] = $participant;
			}
		}
		
		$this->subscribents_array = array();
		if ($this->subscribents !== NULL) {
			$exp_part = explode(',', $this->subscribents);
			foreach ($exp_part as $subscribent) {
				$this->subscribents_array[$subscribent] = $subscribent;
			}
		}
	}
	
	public function set_data($data) {
//		if ($this->auth->get_level() < 15) {
//			throw new PermissionDeniedException();
//		}
//
//		$input = array('title', 'description', 'opinion', 'type');
//		if ($this->auth->get_level() >= 20) {
//			$input[] = 'coordinator';
//		}
//		$val_data = $this->validator->validate($data, $input); 
//        
//		if ($val_data === false) {
//			throw new ValidationException('issues',	$this->validator->get_errors());
//		}
//		
//		
        
        $input = array('title', 'description', 'opinion', 'type', 'coordinator');
        $val_data = $this->validator->validate($data, $input); 
        
		if ($val_data === false) {
			throw new ValidationException('issues',	$this->validator->get_errors());
		}
        
        //change coordinator at the end
        $val_coordiantor = $val_data['coordinator'];
        unset($val_data['coordinator']);
        
        $this->set_property_array($val_data);    
        $this->set_property('coordinator', $val_coordiantor);
        
        
//        if (count($this->validator->get_errors()) > 0)  {
//			throw new ValidationException('issues',	$this->validator->get_errors());
//		}
		
		//!!! don't update activity on issue update
		
		$this->description_cache = $this->helper->wiki_parse($this->description);
		$this->opinion_cache = $this->helper->wiki_parse($this->opinion);
	}
    
//    public function update_cache() {
//		$this->description_cache = $this->helper->wiki_parse($this->description);
//		$this->opinion_cache = $this->helper->wiki_parse($this->opinion);
//	}
	
	public function set_state($data) {
//		if ($this->auth->get_level() < 15) {
//			throw new PermissionDeniedException();
//		}
//
//		$input = array('opinion', 'state');
//		$val_data = $this->validator->validate($data, $input); 
//		if ($val_data === false) {
//			throw new ValidationException('issues', $this->validator->get_errors());
//		}
//
//		
//		$this->set_property_array($val_data);
        
        $input = array('state', 'opinion');
        $val_data = $this->validator->validate($data, $input); 
        
		if ($val_data === false) {
			throw new ValidationException('issues',	$this->validator->get_errors());
		}
        
        $this->set_property_array($val_data);
        
        if (count($this->validator->get_errors()) > 0)  {
			throw new ValidationException('issues',	$this->validator->get_errors());
		}
		
		//update activity on state update
		$this->last_mod = time();
		$this->update_last_activity();
		$this->opinion_cache = $this->helper->wiki_parse($this->opinion);
	}
    
    public function update_last_activity() {
        $this->last_activity = $this->sqlite_date();
    }
		
	public function add_participant($participant) {
		if (! (	$this->user_is_coordinator() ||
				$participant === $this->model->user_nick || 
                $participant === $this->coordinator) //dodajemy nowego koordynatora
			) {
			throw new PermissionDeniedException();
		}
		if ($this->model->users->exists($participant)) {
			$this->participants_array[$participant] = $participant;
			$this->participants = implode(',', $this->participants_array);
		}
	}
	
	public function add_subscribent($subscribent) {
		if (! (	$this->user_is_coordinator() ||
				$subscribent === $this->model->user_nick ||
                $subscribent === $this->coordinator) //dodajemy nowego koordynatora)
			) {
			throw new PermissionDeniedException();
		}
        //var_dump($subscribent, $this->model->users->exists($subscribent));
		if ($this->model->users->exists($subscribent) &&
            !in_array($subscribent, $this->subscribents_array)) {
			$this->subscribents_array[$subscribent] = $subscribent;
			$this->subscribents = implode(',', $this->subscribents_array);
            return true;
		}
        return false;
	}
	
	public function remove_subscribent($subscribent) {
		if (! (	$this->user_is_coordinator() ||
				$subscribent === $this->model->user_nick)
			) {
			throw new PermissionDeniedException();
		}
		unset($this->subscribents_array[$subscribent]);
		$this->subscribents = implode(',', $this->subscribents_array);
	}
    
    public function get_subscribents() {
        return $this->subscribents_array;
    }
	
	public function get_participants() {
		$full_names = [];
       
        $involved = array_merge($this->subscribents_array, $this->participants_array);
		foreach ($involved as $par) {
			$name = $this->model->users->get_user_full_name($par);
			if ($name == '') {
				$full_names[$par] = $par;
			} else {
				$full_names[$par] = $name;
			}
		}
		//coordinator on top
		uksort($full_names, function ($a, $b) use($full_names) {
			if ($a === $this->coordinator) {
				return -1;
			} else if ($b === $this->coordinator) {
				return 1;
			}
			return $full_names[$a] > $full_names[$b];
		});
		
		return $full_names;
	}
    
    public function total_cost() {
        $sth = $this->model->db->prepare('SELECT SUM(cost) FROM tasks
										WHERE issue=:issue AND state=1');
		$sth->execute(array(':issue' => $this->id));
		$cost = $sth->fetchColumn();
        
        return $cost;
    }
	
	public function is_subscribent($user=NULL) {
		if ($user === NULL) {
			$user = $this->model->user_nick;
		}
		if (in_array($user, $this->subscribents_array)) {
			return true;
		}
		return false;
	}
	
	public function is_task_executor($user=NULL) {
		if ($user === NULL) {
			$user = $this->model->user_nick;
		}
		$sth = $this->model->db->prepare('SELECT COUNT(*) FROM tasks
										WHERE issue=:issue AND executor=:executor');
		$sth->execute(array(':issue' => $this->id, ':executor' => $user));
		$fetch = $sth->fetch();
		if ($fetch[0] === '0') {
			return false;
		} else {
			return true;
		}
	}
	
	public function is_commentator($user=NULL) {
		if ($user === NULL) {
			$user = $this->model->user_nick;
		}
		$sth = $this->model->db->prepare('SELECT COUNT(*) FROM commcauses
										WHERE issue=:issue AND reporter=:reporter');
		$sth->execute(array(':issue' => $this->id, ':reporter' => $user));
		$fetch = $sth->fetch();
		if ($fetch[0] === '0') {
			return false;
		} else {
			return true;
		}
	}
    
    private $causes_without_tasks = -1;
	public function causes_without_tasks_count() {
        if ($this->causes_without_tasks === -1) {
            $sth = $this->model->db->prepare('SELECT COUNT(*) FROM
                (SELECT tasks.id
                    FROM commcauses LEFT JOIN tasks ON commcauses.id = tasks.cause
                    WHERE commcauses.type > 0 AND commcauses.issue = ?
                    GROUP BY commcauses.id)
                WHERE id IS NULL');
            $sth->execute(array($this->id));
            $count = $sth->fetchColumn();

            $this->causes_without_tasks = (int)$count;
        }
        return $this->causes_without_tasks;
	}
    
    //http://data.agaric.com/capture-all-sent-mail-locally-postfix
    //https://askubuntu.com/questions/192572/how-do-i-read-local-email-in-thunderbird
    public function mail_notify($replacements=array(), $emails=false) {
        $plain = io_readFile($this->model->action->localFN('issue-notification'));
        $html = io_readFile($this->model->action->localFN('issue-notification', 'html'));
                
        $issue_link =  DOKU_URL . 'doku.php?id='.$this->model->action->id('issue', 'id', $this->id);
        $issue_unsubscribe = DOKU_URL . 'doku.php?id='.$this->model->action->id('issue', 'id', $this->id, 'action', 'unsubscribe');
        
        $wiki_name = $this->model->conf['title'];
        $issue_reps = array(    'wiki_name' => $wiki_name,
                                'issue_id' => $this->id,
                                'issue_link' => $issue_link,
                                'issue_unsubscribe' => $issue_unsubscribe,
                                'custom_content' => false
                           );
        
        //$replacements can override $issue_reps
        $rep = array_merge($issue_reps, $replacements);
        //auto title
        if (!isset($rep['subject'])) {
            $rep['subject'] =  '#'.$this->id. ' ' .$this->title;
        }
        if (!isset($rep['content_html'])) {
            $rep['content_html'] = $rep['content'];
        }
        if (!isset($rep['who_full_name'])) {
            $rep['who_full_name'] =
                $this->model->users->get_user_full_name($rep['who']);
        }
        
        //format when
        $rep['when'] =  $this->date_format($rep['when']);
        
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
        if ($emails === FALSE) {
            $emails = array_map(function($user) {
                return $this->model->users->get_user_email($user);
            }, $this->subscribents_array);
        }

        $mailer->to($emails);
        $mailer->subject('[' . $wiki_name . '][BEZ] ' . $rep['subject']);

        $send = $mailer->send();
        if ($send === false) {
            //this may mean empty $emails
            //throw new Exception("can't send email");
        }
    }
    
    public function mail_notify_change_state() {
        $this->mail_notify(array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_notify_change_state_action'),
            //'subject' => $this->model->action->getLang('mail_mail_notify_change_state_subject') . ' #'.$this->id,
            'custom_content' => true,
            'content_html' => ''
        ));
    }
    
    public function mail_notify_invite($client) {
        $email = $this->model->users->get_user_email($client);
        
        $this->mail_notify(array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_notify_invite_action'),
            //'subject' => $this->model->action->getLang('mail_mail_notify_invite_subject') . ' #'.$this->id,
            'custom_content' => true,
            'content_html' => ''
        ), array($email));
    }
    
    public function mail_inform_coordinator() {
        $email = $this->model->users->get_user_email($this->coordinator);
        
        $this->mail_notify(array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_inform_coordinator_action'),
            //'subject' => $this->model->action->getLang('mail_mail_inform_coordinator_subject') . ' #'.$this->id,
            'custom_content' => true,
            'content_html' => ''
        ), array($email));
    }
}
