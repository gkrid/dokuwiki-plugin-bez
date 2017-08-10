<?php
 
if(!defined('DOKU_INC')) die();


//~ abstract class BEZ_mdl_Dummy_Entity {
    
    //~ protected $model;
    
    //~ protected $id = NULL;
    
    //~ public function __get($property) {
		//~ if ($property === 'id') {
            //~ return $this->id;
        //~ }
	//~ }
    
    //~ public function get_table_singular() {
        //~ $class = get_class($this);
		//~ $exp = explode('_', $class);
		//~ $singular = array_pop($exp);
		//~ return lcfirst($singular);
    //~ }
    
    //~ public function get_table_name() {
		//~ $singlar = $this->get_table_singular();
		//~ return $singular.'s';
	//~ }
    
    //~ public function acl_of($field) {
        //~ return $this->model->acl->check_field($this, $field);
    //~ }
    
    //~ public function __construct($model) {
		//~ $this->model = $model;
	//~ }
//~ }

/*
 * All fields are stored in object as strings.
 * NULLs are converted to empty string.
 * If any attribute in object === NULL -> it means that it was not initialized 
 * But we always inserts NULLs instead of empty strings.
 * https://stackoverflow.com/questions/1267999/mysql-better-to-insert-null-or-empty-string 
 **/

abstract class BEZ_mdl_Entity {// extends BEZ_mdl_Dummy_Entity {	
	
    protected $model, $validator;
    
    protected $columns_demendencies = array();
	
	protected $parse_int = array();
    
	public function get_columns() {
        return array();
    }
    
	public function get_virtual_columns() {
        return array();
    }
    
    private function is_dummy() {
        if (strstr(get_class(), 'Dummy') === false) {
            return false;
        }
        return true;
    }
    
    private function not_for_dummies() {
        if ($this->is_dummy()) {
            throw new Exception('dummy object doesn\'t contains data.');
        }
    }
	
	public function get_assoc($filter=NULL) {
        $this->not_for_dummies();
        
		$assoc = array();
		$columns = array_merge($this->get_columns(), $this->get_virtual_columns());
        
        if ($filter !== NULL) {
            $columns = array_intersect($columns, $filter);
        }
        
		foreach ($columns as $col) {
			$assoc[$col] = $this->$col;
		}
		return $assoc;
	}
    
    public function get_table_singular() {
        $class = get_class($this);
		$exp = explode('_', $class);
		$singular = array_pop($exp);
		return lcfirst($singular);
    }
    
    public function get_table_name() {
		$singlar = $this->get_table_singular();
		return $singular.'s';
	}
    
    private function create_DateTime_from_column($column) {
        $cols = array_merge($this->get_columns(), $this->get_virtual_columns());
        if (!in_array($column, $cols)) {
            throw new Exception("$column is not a column");
        }
        $dt = $this->$column;
        if (is_numeric($dt)) {
            return new DateTime('@'.$dt);
        }
        return new DateTime($dt);
    }
    
    public function date_format($column) {
        $date = $this->create_DateTime_from_column($column);
        
        return $date->format('Y-m-d');
    }
    
    public function datetime_format($column, $format='comment') {
        $date = $this->create_DateTime_from_column($column);;
        
        if ($format === 'comment') {
            return $dt->format('j') . ' ' .
                    $this->model->action->getLang('mon'.$dt->format('n').'_a') . ' ' .
                    ($dt->format('Y') === date('Y') ? '' : $dt->format('Y') . ' ') .
                    $this->model->action->getLang('at_hour') . ' ' .
                    $dt->format('G:i');
        }
    }
    
    public function days_ago($column, $first_date = null) {
        if ($first_date === null) {
            $first_date = new DateTime();
        } elseif (is_string($first_date)) {
            $first_date = $this->create_DateTime_from_column($first_date);
        }
        $interval = $first_date->diff($this->create_DateTime_from_column($column));
        $sign = $interval->format('%R');
        $days = $interval->format('%a');
        if ($sign === '-') {
            return $days . ' ' . $this->model->action->getLang('days') . ' ' .
                    $this->model->action->getLang('ago');
        }
        return $days . ' ' . $this->model->action->getLang('days');
        
    }
    
    public function float_localized($column) {
        $cols = array_merge($this->get_columns(), $this->get_virtual_columns());
        if (!in_array($column, $cols)) {
            throw new Exception("$column is not a column");
        }
        if ($this->$column === '') {
            return '';
        }
        
        if (!is_numeric($this->$column)) {
            throw new Exception('Column ' . $column . ' isn\'t numeric');
        }
        
        return sprintf('%.2f', $this->$column);
    }
    
    //set id when object is saved in database
    public function set_id($id) {
        $this->not_for_dummies();
         
        if ($this->id === NULL) {
            $this->id = $id;
        } else {
            throw new Exception('id already set for issue #'.$this->id);   
        }
    }
	
	public function sqlite_date($time=NULL) {
		//SQLITE format: https://www.sqlite.org/lang_datefunc.html
		if ($time === NULL) {
			return date('Y-m-d H:i:s');
		} else {
			return date('Y-m-d H:i:s', $time);
		}
	}
	
	public function __get($property) {
        $this->not_for_dummies();
         
		$columns = array_merge($this->get_columns(), $this->get_virtual_columns());
        
        //now only normal db columns has ACL, it should be fixed        
        if (in_array($property, $this->get_columns()) && $this->acl_of($property) < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }
        
		if (property_exists($this, $property) && in_array($property, $columns)) {
			if (in_array($property, $this->parse_int)) {
				return (int)$this->$property;
			} else {
				return $this->$property;
			}
		}
	}
    
    protected function set_property($property, $value) {
        $this->not_for_dummies();
         
        if (!in_array($property, $this->get_columns())) {
            throw new Exception('trying to set unexisting column');
        }
        
        //throws ValidationException
        $this->validator->validate_field($property, $value);
        
        //throws PermissionDeniedException
        $this->model->acl->can_change($this, $property);
        
        $this->$property = $value;
        
        if (isset($this->columns_demendencies[$property])) {
            $method = $this->columns_demendencies[$property];
            //convention -> run the method with the same name as $property
            if (!method_exists($this, $method)) {
                throw new Exception("denepndency method: $property not implemented");
            }
            $this->$method();
        }
    }
    
    protected function set_property_array($array) {
        foreach ($array as $k => $v) {
            $this->set_property($k, $v);
        }
    }
    
    //by default do nothing
    private function update_virtual_columns() {
    }
    
    public function set_data($post, $filter=NULL) {
        $input = array_intersect($this->changable_fields($filter), array_keys($post), array_keys($this->validator->get_rules()));
       
        $val_data = $this->validator->validate($post, $input);
		if ($val_data === false) {
			throw new ValidationException($this->get_table_name(), $this->validator->get_errors());
		}

		$this->set_property_array($val_data);
        
        $this->update_virtual_columns();
    }
    
    public function changable_fields($filter=NULL) {
       $fields = $this->model->acl->check($this);
       
       if ($filter !== NULL) {
           $fields = array_filter($fields, function ($k) use ($filter) {
                return in_array($k, $filter);
           }, ARRAY_FILTER_USE_KEY);
       }
       
       return array_keys(array_filter($fields, function ($var) {
           return $var >= BEZ_PERMISSION_CHANGE;
       }));
    }
    
    public function acl_of($field) {
        return $this->model->acl->check_field($this, $field);
    }
        
    public function __construct($model, $defaults=array()) {
        //by convention all defaults must be strings
        foreach ($defaults as $val) {
            if (!is_string($val)) {
                throw new Exception('all defaults must be strings');
            }
        }
		$this->model = $model;
        
        if (!$this->is_dummy()) {
            $this->validator = new BEZ_mdl_Validator($this->model);
        }
		//$this->helper = plugin_load('helper', 'bez');
	}
}
