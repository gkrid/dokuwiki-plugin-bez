<?php

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

abstract class Entity {

    /** @var  Model */
    protected $model;

    /** @var Validator */
    protected $validator;

    /** @var Acl */
    protected $acl;

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
        
        //it slows down the execution and must be solved diffirently
//        if ($this->acl_of($property) < BEZ_PERMISSION_VIEW) {
//            throw new PermissionDeniedException();
//        }

        return $this->$property;

	}
    
    protected function set_property($property, $value) {
        if ($this->acl_of($property) < BEZ_PERMISSION_CHANGE) {
            throw new PermissionDeniedException("cannot change field $property");
        }
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
       $fields = $this->acl->get_list();

       if ($filter !== NULL) {
           $fields = array_filter($fields, function ($k) use ($filter) {
                return in_array($k, $filter);
           }, ARRAY_FILTER_USE_KEY);
       }

       return array_keys(array_filter($fields, function ($var) {
           return $var >= BEZ_PERMISSION_CHANGE;
       }));
    }
    
    public function can_be_null($field) {
	    $rule = $this->validator->get_rule($field);
	    $null = $rule[1];
	    if (strtolower($null) == 'null') {
	        return true;
        }

        return false;
    }
        
    public function __construct($model) {
        $this->model = $model;
        $this->validator = new Validator($this->model);

        $this->acl = new Acl($this->model->get_level(), $this->get_select_columns());
    }

    public function acl_of($field) {
        return $this->acl->acl_of($field);
    }

}
