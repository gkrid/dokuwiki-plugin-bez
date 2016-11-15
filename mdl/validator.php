<?php

class BEZ_mdl_Validator {
	private $rules=array(), $errors, $model;
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function set_rules($rules) {
		$this->rules = array_merge($this->rules, $rules);
	}
	
	public function get_rules() {
		return $this->rules;
	}
	
	public function get_errors() {
		return $this->errors;
	}
	
	public function set_error($field, $code) {
		$this->errors[$field] = $code;
	}
	
	public function validate($data, $fields) {
		$val_data = array();

		foreach ($data as $key => $value) {
			if (!isset($this->rules[$key]) || !in_array($key, $fields)) {
				continue;
			}
			$rules = $this->rules[$key][0];
			$null = $this->rules[$key][1];
			
			if ($null === 'NOT NULL' && $value == '') {
				$this->errors[$key] = 'is_null';
				continue;
			} else if ($null === 'NULL' && $value == '') {
				$val_data[$key] = $value;
				continue;
			}

			$method = $rules[0];
			$validator = 'validate_'.$method;
			if (!method_exists($this, $validator)) {
				throw new Exception("there is no validation function $validator");
			}
			
			$args = $rules;
			$args[0] = $value;
			$result = call_user_func_array(array($this, $validator), $args);
			
			if ($result === false) {
				$this->errors[$key] = $validator;
			} else {
				$val_data[$key] = $value;
			}
		}
		if (count($this->errors) > 0) {
			return false;
		}

		return $val_data;
	}
	
	public function validate_select($value, $options) {
		if (in_array($value, $options)) {
			return true;
		}
		return false;
	}
	
	public function validate_dw_user($user) {
		$wiki_users = $this->model->users->get_all();
		if (array_key_exists($user, $wiki_users)) {
			return true;
		}
		return false;
	}
	
	public function validate_unix_timestamp($stamp) {
		if (is_numeric($stamp)) {
			return true;
		}
		return false;
	}
	
	public function validate_numeric($value) {
		if (is_numeric($value)) {
			return true;
		}
		return false;
	}
	
	public function validate_length($value, $max_length) {
		if (strlen($value) <= $max_length) {
			return true;
		}
		return false;
	}
	
	public function validate_iso_date($date) {
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $parts)) {
			$year  = $parts[1];
			$month = $parts[2];
			$day   = $parts[3];
			if (mktime(0, 0, 0, $month, $day, $year)) {
				return true;
			}
			return false;
		}
		return false;
	}
	
	public function validate_time($time) {
		if (preg_match('/^(\d{1,2}):(\d{1,2})$/', $time, $parts)) {
			$hours  = $parts[1];
			$minutes = $parts[2];
			
			if (mktime($hours, $minutes, 0)) {
				return true;
			}
			return false;
		}
		return false;
	}
	
	public function validate_must_be_empty($value) {
		if ($value == '') {
			return true;
		}
		return false;
	}
}
