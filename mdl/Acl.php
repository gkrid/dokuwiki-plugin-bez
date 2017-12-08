<?php
 
//if(!defined('DOKU_INC')) die('meh.');
//
//include_once DOKU_PLUGIN."bez/models/tokens.php";

namespace dokuwiki\plugin\bez\mdl;

//ACL level defines
use dokuwiki\plugin\bez\meta\PermissionDeniedException;

define('BEZ_AUTH_NONE', 0);
define('BEZ_AUTH_VIEWER', 2);
define('BEZ_AUTH_USER', 5);
define('BEZ_AUTH_LEADER', 10);
define('BEZ_AUTH_ADMIN', 20);

define('BEZ_PERMISSION_UNKNOWN', -1);
define('BEZ_PERMISSION_NONE', 0);
define('BEZ_PERMISSION_VIEW', 1);
define('BEZ_PERMISSION_CHANGE', 2);

class Acl {
    /** @var  Model */
    private $model;
    
    private $level = BEZ_AUTH_NONE;
    
//    private $threads = array();
//    private $commcauses = array();
//    private $tasks = array();
    
    private function update_level($level) {
		if ($level > $this->level) {
			$this->level = $level;
		}
	}
    
    public function get_level() {
        return $this->level;
    }
    
    public function __construct(Model $model) {
        $this->model = $model;
        
		$userd = $this->model->dw_auth->getUserData($this->model->user_nick); 
		if ($userd !== false && is_array($userd['grps'])) {
			$grps = $userd['grps'];
			if (in_array('admin', $grps ) || in_array('bez_admin', $grps )) {
				$this->update_level(BEZ_AUTH_ADMIN);
            } elseif (in_array('bez_leader', $grps )) {
                $this->update_level(BEZ_AUTH_LEADER);
			} else {
				$this->update_level(BEZ_AUTH_USER);
			}
        } elseif (isset($_GET['t'])) {
//            $page_id = $this->model->action->page_id();
//            $toko = new Tokens();
//
//            if ($toko->check(trim($_GET['t']), $page_id)) {
//                $this->update_level(BEZ_AUTH_VIEWER);
//            }
        }
    }

    private function static_thread() {
        $acl = array_fill_keys(Thread::get_columns(), BEZ_PERMISSION_NONE);

        //virtual columns
        $acl['participants'] = BEZ_PERMISSION_NONE;
        $acl['labels'] = BEZ_PERMISSION_NONE;

        //BEZ_AUTH_VIEWER is also token viewer
        if ($this->level >= BEZ_AUTH_VIEWER) {
            //user can display everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_VIEW;
            }, $acl);
        }

        if ($this->level >= BEZ_AUTH_ADMIN) {
            //user can edit everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_CHANGE;
            }, $acl);

            return $acl;
        }
    }
        

    private function check_thread(Thread $thread) {
//        $acl = array(
//                'id'            => BEZ_PERMISSION_NONE,
//                'title'         => BEZ_PERMISSION_NONE,
//                'description'   => BEZ_PERMISSION_NONE,
//                'state'         => BEZ_PERMISSION_NONE,
//                'opinion'       => BEZ_PERMISSION_NONE,
//                'type'          => BEZ_PERMISSION_NONE,
//                'coordinator'   => BEZ_PERMISSION_NONE,
//                'reporter'      => BEZ_PERMISSION_NONE,
//                'date'          => BEZ_PERMISSION_NONE,
//                'last_mod'      => BEZ_PERMISSION_NONE,
//                'last_activity' => BEZ_PERMISSION_NONE,
//                'participants'  => BEZ_PERMISSION_NONE,
//                'subscribents'  => BEZ_PERMISSION_NONE,
//                'description_cache' => BEZ_PERMISSION_NONE,
//                'opinion_cache' => BEZ_PERMISSION_NONE
//        );

        $acl = $this->static_thread();
        
        //we create new issue
        if ($thread->id === NULL) {
            if ($this->level >= BEZ_AUTH_USER) {
                $acl['title'] = BEZ_PERMISSION_CHANGE;
                $acl['content'] = BEZ_PERMISSION_CHANGE;
                //$acl['type'] = BEZ_PERMISSION_CHANGE;
            }
            
            if ($this->level >= BEZ_AUTH_LEADER) {
                $acl['coordinator'] = BEZ_PERMISSION_CHANGE;
            }
            
            return $acl;
        }
        
        if ($thread->state === 'proposal' &&
            $thread->original_poster === $this->model->user_nick) {
            $acl['title'] = BEZ_PERMISSION_CHANGE;
            $acl['content'] = BEZ_PERMISSION_CHANGE;
            //$acl['type'] = BEZ_PERMISSION_CHANGE;
        }
        
        if ($thread->coordinator === $this->model->user_nick) {
            $acl['title'] = BEZ_PERMISSION_CHANGE;
            $acl['content'] = BEZ_PERMISSION_CHANGE;
            //$acl['type'] = BEZ_PERMISSION_CHANGE;
            
            //coordinator can change coordinator
            $acl['coordinator'] = BEZ_PERMISSION_CHANGE;
            
            $acl['state'] = BEZ_PERMISSION_CHANGE;
            //$acl['opinion'] = BEZ_PERMISSION_CHANGE;
        }
                
        return $acl;
    }
    
    //if user can chante id => he can delete record
    private function check_task($task) {
        $acl = array(
                'id'             => BEZ_PERMISSION_NONE,
                'task'           => BEZ_PERMISSION_NONE,
                'state'          => BEZ_PERMISSION_NONE,
                'tasktype'       => BEZ_PERMISSION_NONE,
                'executor'       => BEZ_PERMISSION_NONE,
                'cost'           => BEZ_PERMISSION_NONE,
                'reason'         => BEZ_PERMISSION_NONE,
                'reporter'       => BEZ_PERMISSION_NONE,
                'date'           => BEZ_PERMISSION_NONE,
                'close_date'     => BEZ_PERMISSION_NONE,
                'cause'          => BEZ_PERMISSION_NONE,
                'plan_date'      => BEZ_PERMISSION_NONE,
                'all_day_event'  => BEZ_PERMISSION_NONE,
                'start_time'     => BEZ_PERMISSION_NONE,
                'finish_time'    => BEZ_PERMISSION_NONE,
                'issue'          => BEZ_PERMISSION_NONE,
                'task_cache'     => BEZ_PERMISSION_NONE,
                'reason_cache'   => BEZ_PERMISSION_NONE,
                'subscribents'   => BEZ_PERMISSION_NONE
        );
        
        if ($this->level >= BEZ_AUTH_VIEWER) {
            //user can display everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_VIEW;
            }, $acl);
        }
        
        if ($this->level >= BEZ_AUTH_ADMIN) {
            //admin can edit everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_CHANGE;
            }, $acl);
            
            return $acl;
        }
        
        //we create new task
        if ($task->id === NULL) {
            
            if ($task->coordinator === $this->model->user_nick ||
               ($task->issue === '' && $this->level >= BEZ_AUTH_LEADER)) {
                $acl['task'] = BEZ_PERMISSION_CHANGE;
                $acl['tasktype'] = BEZ_PERMISSION_CHANGE;
                $acl['executor'] = BEZ_PERMISSION_CHANGE;
                $acl['cost'] = BEZ_PERMISSION_CHANGE;
                $acl['plan_date'] = BEZ_PERMISSION_CHANGE;
                $acl['all_day_event'] = BEZ_PERMISSION_CHANGE;
                $acl['start_time'] = BEZ_PERMISSION_CHANGE;
                $acl['finish_time'] = BEZ_PERMISSION_CHANGE;
            }
            
            //przypisujemy zadanie programowe samemu sobie
            //no executor
            if ($task->issue === '') {
                $acl['task'] = BEZ_PERMISSION_CHANGE;
                $acl['tasktype'] = BEZ_PERMISSION_CHANGE;
                $acl['cost'] = BEZ_PERMISSION_CHANGE;
                $acl['plan_date'] = BEZ_PERMISSION_CHANGE;
                $acl['all_day_event'] = BEZ_PERMISSION_CHANGE;
                $acl['start_time'] = BEZ_PERMISSION_CHANGE;
                $acl['finish_time'] = BEZ_PERMISSION_CHANGE;
            }
            
            return $acl;
        }
        
        //user can change state
        if ($task->executor === $this->model->user_nick) {
            $acl['reason'] = BEZ_PERMISSION_CHANGE;
            $acl['state'] = BEZ_PERMISSION_CHANGE;            
        }
        
        //reporters can add subscribents to programme task
        if ($task->reporter === $this->model->user_nick) {
            $acl['subscribents'] = BEZ_PERMISSION_CHANGE;          
        }
               
        if ($task->coordinator === $this->model->user_nick ||
            ($task->issue === '' && $this->level >= BEZ_AUTH_LEADER)) {
                
            $acl['reason'] = BEZ_PERMISSION_CHANGE;
            $acl['state'] = BEZ_PERMISSION_CHANGE; 
            
            //we can chante cause
            $acl['cause'] =  BEZ_PERMISSION_CHANGE;
            
            $acl['task'] = BEZ_PERMISSION_CHANGE;
            $acl['tasktype'] = BEZ_PERMISSION_CHANGE;
            $acl['executor'] = BEZ_PERMISSION_CHANGE;
            $acl['cost'] = BEZ_PERMISSION_CHANGE;
            $acl['reason'] = BEZ_PERMISSION_CHANGE;
            $acl['plan_date'] = BEZ_PERMISSION_CHANGE;
            $acl['all_day_event'] = BEZ_PERMISSION_CHANGE;
            $acl['start_time'] = BEZ_PERMISSION_CHANGE;
            $acl['finish_time'] = BEZ_PERMISSION_CHANGE;
            
                            
            //leaders can add subscribents to programme tasks
            $acl['subscribents'] = BEZ_PERMISSION_CHANGE;
        }
        
        if ($task->issue === '' &&
            $task->reporter === $this->model->user_nick &&
            $task->executor === $this->model->user_nick) {
            $acl['reason'] = BEZ_PERMISSION_CHANGE;
            $acl['state'] = BEZ_PERMISSION_CHANGE;  
            
            $acl['task'] = BEZ_PERMISSION_CHANGE;
            $acl['tasktype'] = BEZ_PERMISSION_CHANGE;
            //no executor
            $acl['cost'] = BEZ_PERMISSION_CHANGE;
            $acl['reason'] = BEZ_PERMISSION_CHANGE;
            $acl['plan_date'] = BEZ_PERMISSION_CHANGE;
            $acl['all_day_event'] = BEZ_PERMISSION_CHANGE;
            $acl['start_time'] = BEZ_PERMISSION_CHANGE;
            $acl['finish_time'] = BEZ_PERMISSION_CHANGE;
        }
        

        return $acl;
        
    }

    private function static_thread_comment() {
        $acl = array_fill_keys(Thread_comment::get_columns(), BEZ_PERMISSION_NONE);

        //virtual columns

        //BEZ_AUTH_VIEWER is also token viewer
        if ($this->level >= BEZ_AUTH_VIEWER) {
            //user can display everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_VIEW;
            }, $acl);
        }

        if ($this->level >= BEZ_AUTH_ADMIN) {
            //user can edit everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_CHANGE;
            }, $acl);

            return $acl;
        }
    }
    
    private function check_commcause($commcause) {
        $acl = array(
            'id'            => BEZ_PERMISSION_NONE,
            'issue'         => BEZ_PERMISSION_NONE,
            'datetime'      => BEZ_PERMISSION_NONE,
            'reporter'      => BEZ_PERMISSION_NONE,
            'type'          => BEZ_PERMISSION_NONE,
            'content'       => BEZ_PERMISSION_NONE,
            'content_cache'   => BEZ_PERMISSION_NONE
        );
        
        if ($this->level >= BEZ_AUTH_VIEWER) {
            //user can display everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_VIEW;
            }, $acl);
        }
        
        if ($this->level >= BEZ_AUTH_ADMIN) {
            //admin can edit everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_CHANGE;
            }, $acl);
            
            return $acl;
        }
        //we create new commcause
        if ($commcause->id === NULL) {        
            if ($this->level >= BEZ_USER) {
                $acl['content'] = BEZ_PERMISSION_CHANGE;
            }
            
            if ($commcause->coordinator === $this->model->user_nick) {
                $acl['type'] = BEZ_PERMISSION_CHANGE;
                $acl['content'] = BEZ_PERMISSION_CHANGE;
            }
            
            return $acl;
        }


        if ($commcause->coordinator === $this->model->user_nick) {
            $acl['type'] = BEZ_PERMISSION_CHANGE;
            $acl['content'] = BEZ_PERMISSION_CHANGE;
            
            //we can only delete records when there is no tasks subscribed to issue
            if ($commcause->tasks_count === 0) {
                 $acl['id'] = BEZ_PERMISSION_CHANGE;
            }
            
        }
        
        //jeżeli ktoś zmieni typ z komentarza na przyczynę, tracimy możliwość edycji
        if ($commcause->reporter === $this->model->user_nick &&
            $commcause->type === '0') {
            $acl['content'] = BEZ_PERMISSION_CHANGE;
            
            //we can only delete records when there is no tasks subscribed to issue
            if ($commcause->tasks_count === 0) {
                 $acl['id'] = BEZ_PERMISSION_CHANGE;
            }
        }
        
        return $acl;
        
    }

    private function static_label() {
        $acl = array_fill_keys(Label::get_columns(), BEZ_PERMISSION_NONE);

        if ($this->level >= BEZ_AUTH_USER) {
            //user can display everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_VIEW;
            }, $acl);
        }
        
        if ($this->level >= BEZ_AUTH_ADMIN) {
            //admin can edit everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_CHANGE;
            }, $acl);
        }
        
        return $acl;
    }

    private function check_label(Label $label) {
        return $this->static_label();
    }

   private function check_tasktype($tasktype) {
        $acl = array(
            'id'            => BEZ_PERMISSION_NONE,
            'pl'         => BEZ_PERMISSION_NONE,
            'en'      => BEZ_PERMISSION_NONE
        );
        
        if ($this->level >= BEZ_AUTH_USER) {
            //user can display everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_VIEW;
            }, $acl);
        }
        
        if ($this->level >= BEZ_AUTH_ADMIN) {
            //admin can edit everythig
            $acl = array_map(function($value) {
                return BEZ_PERMISSION_CHANGE;
            }, $acl);
        }
        
        return $acl;
    }

    /*returns array */
    public function check(Entity $obj) {
        $method = 'check_'.$obj->get_table_name();
        if (!method_exists($this, $method)) {
            throw new \Exception('no acl rules set for table: '.$obj->get_table_name());
        }
        return $this->$method($obj);
    }
    
    public function check_field(Entity $obj, $field) {
        $acl = $this->check($obj);

        if (isset($acl[$field])) {
            return $acl[$field];
        }
        return BEZ_PERMISSION_UNKNOWN;
    }

    public function check_static($table) {
        $method = 'static_'.$table;
        if (!method_exists($this, $method)) {
            throw new \Exception('no acl rules set for table: '.$table);
        }
        return $this->$method($table);
    }

    public function check_static_field($table, $field) {
        $acl = $this->check_static($table);
        return $acl[$field];
    }

    
    public function can_change(Entity $obj, $field) {
        if ($this->check_field($obj, $field) < BEZ_PERMISSION_CHANGE) {
            $table = $obj->get_table_name();
            $id = $obj->id;
            throw new PermissionDeniedException('user cannot change field "'.$field.'" in table "'.$table.', rowid: "'.$id.'"');
        }
    }
}
