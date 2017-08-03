<?php

class Tpl {
    
    private $lang, $action, $params, $lang_code;
    
    private $variables = array();
    
    //form values from $_POST or from database
    private $values = array();
    
    public function __construct($lang, $action, $params, $lang_code='') {
        $this->lang = $lang;
        
        $this->action = $action;
        $this->params = $params;
        
        $this->lang_code = $lang_code;
    }
    
    public function action() {
        return $this->action;
    }
    
    public function param($id) {
        return (isset($this->params[$id]) ? $this->params[$id] : '');
    }
    
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
        return (isset($this->lang[$id]) ? $this->lang[$id] : '');
    }
    
    public function getLangJs($id) {
        return (isset($this->lang['js'][$id]) ? $this->lang['js'][$id] : '');
    }
    
    public function id() {
		$args = func_get_args();
		array_unshift($args, 'bez');
        
		if ($this->lang_code !== '') {
			array_unshift($args, $this->lang_code);
        }
        
		return implode(':', $args);
    }
    
    public function url() {
        $args = func_get_args();
        $id = call_user_func_array(array($this, 'id'), $args);
        
        return '?id=' . $id;
    }
}
