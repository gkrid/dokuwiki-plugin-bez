<?php
class Connect {
	protected $db;
	protected function errquery($query)
	{
		global $errors;
		if (count($errors) > 0)
			break;
		$this->db->query($query);
		if ($this->db->error) 
			$errors[] = "MySQL error(".$this->db->errno."): ". $this->db->error;
	}
	public function __construct()
	{
		global $errors;
		$this->db = new mysqli('localhost', 'root', '', 'bez');
		if ($this->db->connect_errno) 
			$errors[] = "Failed to connect to MySQL: ". $this->db->connect_error;
	}
}
