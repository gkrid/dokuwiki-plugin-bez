<?php

class BEZ_mdl_Validator {
	private $rules, $errors, $dw_auth;
	public function __construct($dw_auth) {
		$this->dw_auth = $dw_auth;
	}
	
	public function set_rules($rules) {
		$this->rules = $rules;
	}
	
	public function get_errors() {
		return $this->errors;
	}
	
	public function validate($data) {
		$val_data = array();
		foreach ($data as $key => $value) {
			if (!isset($this->rules[$key])) {
				continue;
			}
			$rules = $this->rules[$key][0];
			$null = $this->rules[$key][1];
			
			if ($null === 'NOT NULL' && $value === '') {
				$this->errors[$key] = 'is_null';
				continue;
			} else if ($null === 'NULL' && $value === '') {
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
		$wiki_users = $this->dw_auth->retrieveUsers();
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
}
