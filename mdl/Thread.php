<?php
 
//if(!defined('DOKU_INC')) die();

//require_once 'entity.php';

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\PermissionDeniedException;
use dokuwiki\plugin\bez\meta\ValidationException;

class Thread extends Entity {

    protected $id;

    protected $original_poster, $coordinator;

    protected $private, $lock;

    protected $type, $state;

    protected $create_date, $last_activity_date, $last_modification_date, $close_date;

    protected $title, $content, $content_html;

    protected $task_count, $task_count_open, $task_sum_cost;

    public static function get_columns() {
        return array('id',
                     'original_poster', 'coordinator',
                     'private', 'lock',
                     'type', 'state',
                     'create_date', 'last_activity_date', 'last_modification_date', 'close_date',
                     'title', 'content', 'content_html',
                     'task_count', 'task_count_open', 'task_sum_cost');
    }

    public static function get_states() {
        return array('proposal', 'opened', 'done', 'closed', 'rejected');
    }
	
    
//    private function state_string() {
//        if ($this->state === '2') {
//            return 'state_rejected';
//        } else if ($this->coordinator === '-proposal') {
//            return 'state_proposal';
//        } else if ( $this->state === '0' &&
//                    (int)$this->assigned_tasks_count > 0 &&
//                    (int)$this->opened_tasks_count === 0) {
//            return 'state_done';
//        } else if ($this->state === '0') {
//            return 'state_opened';
//        } else if ($this->state === '1') {
//            return 'state_closed';
//        }
//    }
//
//    private function type_string() {
//        if ($this->type === '') {
//            return '';
//        }
//        $issuetype = $this->model->issuetypes->get_one($this->type)->get_assoc();
//        return $issuetype[$this->model->conf['lang']];
//    }
//
//    private function priority() {
//        if ($this->state === '2') {
//            return '3';
//        }
//        $min_pr = $this->model->tasks->min_priority(array('issue' => $this->id));
//        if ($min_pr === NULL) {
//            return 'None';
//        }
//        return $min_pr;
//    }
        
    public function user_is_coordinator() {
        if ($this->coordinator === $this->model->user_nick ||
           $this->model->acl->get_level() >= BEZ_AUTH_ADMIN) {
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
		
//		$this->validator->set_rules(array(
//			'title' => array(array('length', 200), 'NOT NULL'),
//			'description' => array(array('length', 10000), 'NOT NULL'),
//			'state' => array(array('select', array('0', '1', '2')), 'NULL'),
//			'opinion' => array(array('length', 10000), 'NOT NULL'),
//			'type' => array(array('numeric'), 'NULL'),
//			'coordinator' => array(array('dw_user'), 'NOT NULL'),
//			'reporter' => array(array('dw_user'), 'NOT NULL'),
//			'date' => array(array('unix_timestamp'), 'NOT NULL'),
//			'last_mod' => array(array('unix_timestamp'), 'NULL'),
//			'last_activity' => array(array('sqlite_datetime'), 'NOT NULL')
//		));

//        $this->validator->set_rules(array(
            //'coordinator' => array(array('dw_user'), 'NULL'),
//            'original_poster' => array(array('dw_user'), 'NOT NULL'),
//			'title' => array(array('length', 200), 'NOT NULL'),
//			'content' => array(array('length', 10000), 'NOT NULL'),
//			'state' => array(array('select', array('0', '1', '2')), 'NULL'),
//			'opinion' => array(array('length', 10000), 'NOT NULL'),
//			'type' => array(array('select'), 'NULL'),

//			'create_date' => array(array('sqlite_datetime'), 'NOT NULL'),
//			'last_mod' => array(array('sqlite_datetime'), 'NULL'),
//			'last_activity' => array(array('sqlite_datetime'), 'NOT NULL')
//		));
		
		//we've created empty object (new record)
		if ($this->id === NULL) {
			$this->original_poster = $this->model->user_nick;
			$this->create_date = date('c');
			$this->last_activity_date = $this->create_date;
            $this->last_modification_date = $this->create_date;

			$this->state = 'proposal';

//			$this->close_date = '';

//			$this->lock = '0';
//			$this->private = '0';
//            $this->type = '1';//type 1 - issue
//            $this->state = '0';//state 0 - proposal
			
			//$this->update_last_activity();
			
			//$this->state = '0';
            
            if ($this->model->acl->get_level() >= BEZ_AUTH_LEADER) {
//                $this->validator->add_rule('cooridnator', array(array('dw_user'), 'NOT NULL'));
                //throws ValidationException
//                $this->coordinator = $this->validator->validate_field('coordinator', $defaults['coordinator']);
                if (!$this->model->userFactory->exists($defaults['coordinator'])) {
                    throw new ValidationException('thread', array('coordinator' => 'is_null'));
                }
                $this->coordinator = $defaults['coordinator'];
                $this->state = 'opened';
            }
//            } else {
//                $this->coordinator = '-proposal';
//            }
            
			
//			$this->add_participant($this->reporter);
//			$this->add_subscribent($this->reporter);
//            if ($this->coordinator !== '-proposal') {
//                $this->add_participant($this->coordinator);
//                $this->add_subscribent($this->coordinator);
//            }
			
		}
        //close_date required	
//		if ($this->state !== 'state_proposal' && $this->state !== 'state_opened') {
//			$this->validator->set_rules(array(
//				'close_date' => array(array('unix_timestamp'), 'NOT NULL')
//			));
//		}
		
		
//		if ($this->participants !== NULL) {
//			$exp_part = explode(',', $this->participants);
//			foreach ($exp_part as $participant) {
//				$this->participants_array[$participant] = $participant;
//			}
//		}
//
//		if ($this->subscribents !== NULL) {
//			$exp_part = explode(',', $this->subscribents);
//			foreach ($exp_part as $subscribent) {
//				$this->subscribents_array[$subscribent] = $subscribent;
//			}
//		}
	}
	
	public function set_data($data, $filter=NULL) {
        $input = array('title', 'content', 'coordinator');
        $val_data = $this->validator->validate($data, $input); 
                
		if ($val_data === false) {
			throw new ValidationException('issues',	$this->validator->get_errors());
        }
        
        
        //change coordinator at the end(!)
        if (isset($val_data['coordinator'])) {
            $val_coordinator = $val_data['coordinator'];
            unset($val_data['coordinator']);
        }
        
        $this->set_property_array($val_data); 
        
        if (isset($val_coordinator)) {
           $this->set_property('coordinator', $val_coordinator); 
        }
        
		//!!! don't update activity on issue update
		$this->content_html = p_render('xhtml',p_get_instructions($this->content), $ignore);
//		$this->opinion_cache = $this->helper->wiki_parse($this->opinion);
        
        //update virtuals
        //$this->update_virtual_columns();
	}
    
    public function get_meta_fields() {
        return array('reporter', 'date', 'last_mod', 'last_activity');
    }
    
    public function set_meta($post) {
        
        if (isset($post['date'])) {
            $unix = strtotime($post['date']);
            //if $unix === false validator will catch it
            if ($unix !== false) {
                $post['date'] = (string)$unix;
            }
        }
        
        if (isset($post['last_mod'])) {
            $unix = strtotime($post['last_mod']);
            //if $unix === false validator will catch it
            if ($unix !== false) {
                $post['last_mod'] = (string)$unix;
            }
        }
        
        parent::set_data($post, $this->get_meta_fields());
    }
    
//    public function update_cache() {
//        if ($this->model->acl->get_level() < BEZ_AUTH_ADMIN) {
//			return false;
//		}
//		$this->description_cache = $this->helper->wiki_parse($this->description);
//		$this->opinion_cache = $this->helper->wiki_parse($this->opinion);
//	}
//
//	public function set_state($data) {
//
//        $input = array('state', 'opinion');
//        $val_data = $this->validator->validate($data, $input);
//
//		if ($val_data === false) {
//			throw new ValidationException('issues',	$this->validator->get_errors());
//		}
//
//        $this->set_property_array($val_data);
//
//        if (count($this->validator->get_errors()) > 0)  {
//			throw new ValidationException('issues',	$this->validator->get_errors());
//		}
//
//		//update activity on state update
//		$this->last_mod = time();
//		$this->update_last_activity();
//		$this->opinion_cache = $this->helper->wiki_parse($this->opinion);
//
//        //update virtuals
//        //$this->update_virtual_columns();
//	}
    
//    public function update_last_activity() {
//        $this->last_activity = $this->sqlite_date();
//    }

    private $participants;
    public function get_participants() {
        if ($this->acl_of('participants') < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }
        if ($this->id === NULL) {
            $this->participants = array();
        }
        if (is_null($this->participants)) {
            $r = $this->model->sqlite->query('SELECT * FROM thread_participant WHERE thread_id=? ORDER BY user_id', $this->id);
            $this->participants = $this->model->sqlite->res2arr($r);
        }
        return $this->participants;
    }

    public function get_participant($user_id) {
        $participants = $this->get_participants();
        foreach ($participants as $participant) {
            if ($participant['user_id'] == $user_id) {
                return $participant;
            }
        }
        return false;
    }
		
	public function set_participant_flags($user_id, $flags=array()) {
        if ($this->acl_of('participants') < BEZ_PERMISSION_CHANGE) {
            throw new PermissionDeniedException();
        }

        $possible_flags = array('original_poster', 'coordinator', 'commentator', 'task_assignee', 'subscribent');
        if (array_intersect($flags, $possible_flags) != $flags) {
            throw new \Exception('unknown flags');
        }

        $participant = $this->get_participant($user_id);
        if ($participant == false) {
            $participant = array_fill_keys($possible_flags, 0);
        }
        $values = array_merge($participant, array_fill_keys($flags, 1));

        $values['thread_id'] = $this->id;
        $values['user_id'] = $user_id;
        $values['added_by'] = $this->model->dw_user;
        $valuse['added_date'] = date('c');

        $keys = join(',', array_keys($values));
        $vals = join(',', array_fill(0,count($values),'?'));

        $sql = "REPLACE INTO thread_participant ($keys) VALUES ($vals)";
        $this->model->sqlite->query($sql, array_values($valuse));



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

	private $labels;
    public function get_labels() {
        if ($this->acl_of('labels') < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }
        if ($this->id === NULL) {
            $this->labels = array();
        }
        if (is_null($this->labels)) {
            $r = $this->model->sqlite->query('SELECT * FROM label JOIN thread_label WHERE thread_label.thread_id=?', $this->id);
            $this->labels = $this->model->sqlite->res2arr($r);
        }
        return $this->labels;
    }

//    public function get_label_id($name) {
//        $labels = $this->get_labels();
//
//        foreach ($labels as $label) {
//            if ($label['name'] == $name) {
//                return $label['label_id'];
//            }
//        }
//        return false;
//    }

    public function add_label($label_id) {
        if ($this->acl_of('labels') < BEZ_PERMISSION_CHANGE) {
            throw new PermissionDeniedException();
        }

        //issue not saved yet
        if ($this->id === NULL) {
            throw new \Exception('cannot add labels to not saved thread. use set_labels() instead');
        }

        //label already assigned, nothing to do
//        if ($this->get_label_id($name)) return;

//        $r = $this->model->sqlite->query('SELECT id FROM label WHERE id=?', $label_id);
//        $label_id = $this->model->sqlite->res2single($r);
//        if (!$label_id) {
//            throw new \Exception('label does not exist');
//        }

        $this->model->sqlite->storeEntry('thread_label',
                                         array('thread_id' => $this->id,
                                               'label_id' => $label_id));

    }

//    public function remove_label($name) {
//        if ($this->acl_of('labels') < BEZ_PERMISSION_CHANGE) {
//            throw new PermissionDeniedException();
//        }
//        //label not assigned
//        $label_id = $this->get_label($name);
//
//        if ($label_id === false) {
//            throw new \Exception('label don not exists');
//        }
//
//        $this->model->sqlite->query('DELETE FROM thread_label WHERE thread_id=?, label_id=?', $this->id, $label_id);
//    }
	
//	public function add_subscribent($subscribent) {
//		if (! (	$this->user_is_coordinator() ||
//				$subscribent === $this->model->user_nick ||
//                $subscribent === $this->coordinator) //dodajemy nowego koordynatora)
//			) {
//			throw new PermissionDeniedException();
//		}
//
//		if ($this->model->users->exists($subscribent) &&
//            !in_array($subscribent, $this->subscribents_array)) {
//			$this->subscribents_array[$subscribent] = $subscribent;
//			$this->subscribents = implode(',', $this->subscribents_array);
//            return true;
//		}
//        return false;
//	}
//
//	public function remove_subscribent($subscribent) {
//		if (! (	$this->user_is_coordinator() ||
//				$subscribent === $this->model->user_nick)
//			) {
//			throw new PermissionDeniedException();
//		}
//		unset($this->subscribents_array[$subscribent]);
//		$this->subscribents = implode(',', $this->subscribents_array);
//	}
//
//    public function get_subscribents() {
//        return $this->subscribents_array;
//    }
	
//	public function get_participants() {
//		$full_names = [];
//
//        $involved = array_merge($this->subscribents_array, $this->participants_array);
//		foreach ($involved as $par) {
//			$name = $this->model->users->get_user_full_name($par);
//			if ($name == '') {
//				$full_names[$par] = $par;
//			} else {
//				$full_names[$par] = $name;
//			}
//		}
//		//coordinator on top
//		uksort($full_names, function ($a, $b) use($full_names) {
//			if ($a === $this->coordinator) {
//				return -1;
//			} else if ($b === $this->coordinator) {
//				return 1;
//			}
//			return $full_names[$a] > $full_names[$b];
//		});
//
//		return $full_names;
//	}
    	
//	public function is_subscribent($user=NULL) {
//		if ($user === NULL) {
//			$user = $this->model->user_nick;
//		}
//		if (in_array($user, $this->subscribents_array)) {
//			return true;
//		}
//		return false;
//	}
//
//	public function is_task_executor($user=NULL) {
//		if ($user === NULL) {
//			$user = $this->model->user_nick;
//		}
//		$sth = $this->model->db->prepare('SELECT COUNT(*) FROM tasks
//										WHERE issue=:issue AND executor=:executor');
//		$sth->execute(array(':issue' => $this->id, ':executor' => $user));
//		$fetch = $sth->fetch();
//		if ($fetch[0] === '0') {
//			return false;
//		} else {
//			return true;
//		}
//	}
//
//	public function is_commentator($user=NULL) {
//		if ($user === NULL) {
//			$user = $this->model->user_nick;
//		}
//		$sth = $this->model->db->prepare('SELECT COUNT(*) FROM commcauses
//										WHERE issue=:issue AND reporter=:reporter');
//		$sth->execute(array(':issue' => $this->id, ':reporter' => $user));
//		$fetch = $sth->fetch();
//		if ($fetch[0] === '0') {
//			return false;
//		} else {
//			return true;
//		}
//	}
//
//    private $causes_without_tasks = -1;
//	public function causes_without_tasks_count() {
//        if ($this->causes_without_tasks === -1) {
//            $sth = $this->model->db->prepare('SELECT COUNT(*) FROM
//                (SELECT tasks.id
//                    FROM commcauses LEFT JOIN tasks ON commcauses.id = tasks.cause
//                    WHERE commcauses.type > 0 AND commcauses.issue = ?
//                    GROUP BY commcauses.id)
//                WHERE id IS NULL');
//            $sth->execute(array($this->id));
//            $count = $sth->fetchColumn();
//
//            $this->causes_without_tasks = (int)$count;
//        }
//        return $this->causes_without_tasks;
//	}
    
    //http://data.agaric.com/capture-all-sent-mail-locally-postfix
    //https://askubuntu.com/questions/192572/how-do-i-read-local-email-in-thunderbird
    public function mail_notify($replacements=array(), $users=false) {
        $plain = io_readFile($this->model->action->localFN('issue-notification'));
        $html = io_readFile($this->model->action->localFN('issue-notification', 'html'));
                
        $issue_link =  DOKU_URL . 'doku.php?id='.$this->model->action->id('issue', 'id', $this->id);
        $issue_unsubscribe = DOKU_URL . 'doku.php?id='.$this->model->action->id('issue', 'id', $this->id, 'action', 'unsubscribe');
        
        $issue_reps = array(
                                'issue_id' => $this->id,
                                'issue_link' => $issue_link,
                                'issue_unsubscribe' => $issue_unsubscribe,
                                'custom_content' => false,
                                'action_border_color' => 'transparent',
                                'action_color' => 'transparent',
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

        $mailer = new BEZ_Mailer();
        $mailer->setBody($plain, $rep, $rep, $html, false);

        if ($users === FALSE) {
            $users = $this->subscribents_array;
            unset($users[$this->model->user_nick]);
        }
        
        $emails = array_map(function($user) {
            return $this->model->users->get_user_email($user);
        }, $users);   
            

        $mailer->to($emails);
        $mailer->subject($rep['subject']);
        
        $send = $mailer->send();
        if ($send === false) {
            //this may mean empty $emails
            //throw new Exception("can't send email");
        }
    }
    
    protected function mail_issue_box_reps($replacements=array()) {
        $replacements['custom_content'] = true;
        
        $html =  '<h2 style="font-size: 1.2em;">';
	    $html .=    '<a style="font-size:115%" href="@ISSUE_LINK@">#@ISSUE_ID@</a> ';
            
        if ( ! empty($this->type_string)) {
            $html .= $this->type_string;
        } else {
            $html .= '<i style="color: #777"> '.
                        $this->model->action->getLang('issue_type_no_specified').
                    '</i>';
        }
        
        $html .= ' ('.$this->state_string.') ';
	
        $html .= '<span style="color: #777; font-weight: normal; font-size: 90%;">';
        $html .= $this->model->action->getLang('coordinator') . ': ';
        $html .= '<span style="font-weight: bold;">';
        
        if ($this->coordinator === '-proposal') {
            $html .= '<i style="font-weight: normal;">' . 
                $this->model->action->getLang('proposal') . 
                '</i>';
        } else {
            $html .= $this->model->users->get_user_full_name($this->coordinator);
        }
        $html .= '</span></span></h2>';
        
        $html .= '<h2 style="font-size: 1.2em;border-bottom: 1px solid @ACTION_BORDER_COLOR@">' . $this->title . '</h2>';
        
        $html .= $this->description_cache;

        if ($this->state !== '0') {
            $html .= '<h3 style="font-size:100%; border-bottom: 1px dotted #bbb">';
                if ($this->state === '1') {
                    $html .= $this->model->action->getLang('opinion');
                } else {
                    $html .= $this->model->action->getLang('reason');
                }
            $html .= '</h3>';
            $html .= $this->opinion_cache;
        }

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
       
        return $replacements;
    }
    
    public function mail_notify_change_state() {
        $this->mail_notify($this->mail_issue_box_reps(array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_notify_change_state_action'),
            //'subject' => $this->model->action->getLang('mail_mail_notify_change_state_subject') . ' #'.$this->id
        )));
    }
    
    public function mail_notify_invite($client) {        
        $this->mail_notify($this->mail_issue_box_reps(array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_notify_invite_action'),
            //'subject' => $this->model->action->getLang('mail_mail_notify_invite_subject') . ' #'.$this->id
        )), array($client));
    }
    
    public function mail_inform_coordinator() {        
        $this->mail_notify($this->mail_issue_box_reps(array(
            'who' => $this->model->user_nick,
            'action' => $this->model->action->getLang('mail_mail_inform_coordinator_action'),
            //'subject' => $this->model->action->getLang('mail_mail_inform_coordinator_subject') . ' #'.$this->id
        )), array($this->coordinator));
    }
    
    public function mail_notify_issue_inactive($users=false) {
        $this->mail_notify($this->mail_issue_box_reps(array(
            'who' => '',
            'action' => $this->model->action->getLang('mail_mail_notify_issue_inactive'),
        )), $users);
    }
}
