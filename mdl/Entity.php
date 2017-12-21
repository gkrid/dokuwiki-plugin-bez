<?php
 
//if(!defined('DOKU_INC')) die();


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

namespace dokuwiki\plugin\bez\mdl;
/*
 * All fields are stored in object as strings.
 * NULLs are converted to empty string.
 * If any attribute in object === NULL -> it means that it was not initialized 
 * But we always inserts NULLs instead of empty strings.
 * https://stackoverflow.com/questions/1267999/mysql-better-to-insert-null-or-empty-string 
 **/

use dokuwiki\plugin\bez\meta\PermissionDeniedException;
use dokuwiki\plugin\bez\meta\ValidationException;

abstract class Entity {// extends BEZ_mdl_Dummy_Entity {

    /** @var  Model */
    protected $model;

    /** @var Validator */
    protected $validator;

	abstract public static function get_columns();

	public static function get_select_columns() {
        $class = get_called_class();
	    return $class::get_columns();
    }
	
	public function get_assoc($filter=NULL) {
		$assoc = array();

        $columns = $this->get_select_columns();
        if ($filter !== NULL) {
            $columns = array_intersect($columns, $filter);
        }
        
		foreach ($columns as $col) {
			$assoc[$col] = $this->$col;
		}
		return $assoc;
	}

    public function get_table_name() {
        $class = (new \ReflectionClass($this))->getShortName();
		return lcfirst($class);
	}
	
	public function __get($property) {
        if (!property_exists($this, $property) || !in_array($property, $this->get_columns())) {
            throw new \Exception('there is no column: "'.$property. '"" in table: "' . $this->get_table_name() . '"');
        }
        
        //now only normal db columns has ACL, it should be fixed        
        if ($this->acl_of($property) < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }

        return $this->$property;

	}
    
    protected function set_property($property, $value) {

        if (!in_array($property, $this->get_columns())) {
            throw new \Exception('trying to set not existing column');
        }
        
        //throws ValidationException
        $this->validator->validate_field($property, $value);
        
        //throws PermissionDeniedException
        $this->model->acl->can($this, $property, BEZ_PERMISSION_CHANGE);
        
        $this->$property = $value;
    }
    
    protected function set_property_array($array) {
        foreach ($array as $k => $v) {
            $this->set_property($k, $v);
        }
    }

    public function set_data($post) {
        $val_data = $this->validator->validate($post);
		if ($val_data === false) {
			throw new ValidationException($this->get_table_name(), $this->validator->get_errors());
		}

		$this->set_property_array($val_data);

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
        
    public function __construct($model) {
        $this->model = $model;
        $this->validator = new Validator($this->model);
	}
}
