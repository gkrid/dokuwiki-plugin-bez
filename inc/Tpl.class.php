<?php

class Tpl {
    
    private $action, $conf;
    
    private $variables = array();
    
    //form values from $_POST or from database
    private $values = array();
    
    public function __construct(action_plugin_bez $action, $conf) {
        
        $this->action = $action;
        $this->conf = $conf;
        
        //constas
        $this->set('client', $this->model->user_nick);
        
        $info = $action->getInfo();
        $this->set('version', $info['date']);
        
        //common one
        $this->set('users', $this->action->get_model_of('users')->get_all());
        $this->set('groups', $this->action->get_model_of('users')->get_groups());
    }
    
    public function action() {
        return $this->action->get_action();
    }
    
    public function param($id) {
        return $this->action->get_param($id);
    }
    
    public function url() {
        return call_user_func_array(array($this->action, 'url'), func_get_args());
    }
        
    public function get_dummy_of($name) {
        return $this->action->get_model_of($name)->get_dummy_object();
    }
    
    /*users info function for shorten the code*/
    public function user_name($login=NULL) {
        return $this->action->get_model_of('users')->get_user_full_name($login);
    }
    
    public function user_email($login=NULL) {
        return $this->action->get_model_of('users')->get_user_email($login);  
    }
    /*end users info functions*/
    
    public function prevent_rendering() {
        
    }
    
    public function set($id, $value) {
        $this->variables[$id] = $value;
    }
    
    public function get($id) {
        return $this->variables[$id];
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
}
