<?php

namespace dokuwiki\plugin\bez\meta;

use dokuwiki\plugin\bez\mdl\Entity;

class Tpl {

    /** @var \action_plugin_bez */
    private $action;

    private $conf;
    
    private $variables = array();
    
    //form values from $_POST or from database
    private $values = array();
    
    public function __construct(\action_plugin_bez_base $action) {
        
        $this->action = $action;
        $this->conf = $action->getGlobalConf();
        
        //constas
        $this->set('client', $this->model->user_nick);
        
        $info = $action->getInfo();
        $this->set('version', $info['date']);

        $this->set('wiki_title', $this->conf['title']);
        
        //common one
        $this->set('users', $this->action->get_model()->userFactory->get_all());
        $this->set('groups', $this->action->get_model()->userFactory->get_groups());
    }
    
    public function action($default=null) {
        $action = $this->action->get_action();
        if ($action == '' && !is_null($default)) {
            return $default;
        }
        return $action;
    }
    
    public function param($id, $default='') {
        return $this->action->get_param($id, $default);
    }
    
    public function url() {
        return call_user_func_array(array($this->action, 'url'), func_get_args());
    }

    public function mailto($to, $subject, $body) {
        return 'mailto:'.$to.'?subject='.rawurlencode($subject).'&body='.rawurlencode($body);
    }

    public function factory($table) {
        return $this->action->get_model()->factory($table);
    }
    
    /*users info function for shorten the code*/
    public function user_name($login=false) {
        if (!$login) {
            $login = $this->current_user();
        }
        $name = $this->action->get_model()->userFactory->get_user_full_name($login);
        if ($name === '') {
            return $login;
        }
        return $name;
    }
    
    public function user_email($login=false) {
        if (!$login) {
            $login = $this->current_user();
        }
        return $this->action->get_model()->userFactory->get_user_email($login);
    }
    /*end users info functions*/

    public function set($id, $value) {
        $this->variables[$id] = $value;
    }
    
    public function get($id, $default='') {
        $arr = explode(' ', $id);
        $var = $this->variables;
        foreach($arr as $item) {
            if (isset($var[$item])) {
                $var = $var[$item];
            } else {
                return $default;
            }
        }
        return $var;
    }
    
    public function set_values($values) {
        foreach ($values as $name => $value) {
            $this->values[$name] = $value;
        }
    }
    
    public function value($name) {
        return (isset($this->values[$name]) ? $this->values[$name] : '');
    }
    
    public function getLang($id) {
        return $this->action->getLang($id);
    }

    /**
     * @return mixed
     */
    public function current_user() {
        return $this->action->get_model()->user_nick;
    }

    public function user_acl_level() {
        return $this->action->get_model()->get_level();
    }

    public function date($date) {
        return dformat(strtotime($date), '%Y-%m-%d');
    }

    public function datetime($datetime) {
        return dformat(strtotime($datetime), '%Y-%m-%d %H:%M');
    }

    public function date_fuzzy_age($datetime) {
        return datetime_h(strtotime($datetime));
    }

    public function date_diff_days($rDate, $lDate='now', $format='%R%a') {
        $interval = date_diff(date_create($lDate), date_create($rDate));
        return $interval->format("$format ".$this->getLang('days'));
    }

    public function date_diff_hours($rDate, $lDate='now') {
        $interval = date_diff(date_create($lDate), date_create($rDate));
        return $interval->format('%h:%I');
    }

    public function time_to_float($time) {
        list($hour, $minute) = explode(':', $time);
        $hour = (float) $hour;
        $minute = (float) $minute;

        return $hour + $minute/60;
    }

    public function float_to_time($float) {
        $hours = floor($float);
        $minutes = ($float - $hours) * 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }
}
