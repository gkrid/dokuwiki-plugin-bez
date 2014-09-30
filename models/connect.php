<?php
class Connect {
	protected $db;
	protected function errquery($query)
	{
		global $errors;
		if (count($errors) > 0)
			return;
		$r = $this->db->query($query);
		if ($this->db->error) 
			$errors[] = "MySQL error(".$this->db->errno."): ". $this->db->error;
		return $r;
	}
	protected function errinsert($data)
	{
		$fields = implode(',', array_keys($data));
		$values = '';
		foreach ($data as $v)
			$values .= "'".$this->db->real_escape_string($v)."',";
		$values = substr($values, 0, -1);
		$this->errquery("INSERT INTO issues ($fields) VALUES ($values)");
	}
	protected function fetch_assoc($q)
	{
		global $errors;

		$r = $this->errquery($q);
		if (count($errors) > 0)
			return array();

		return $r->fetch_all(MYSQLI_ASSOC);
	}
	public function __construct()
	{
		global $errors;
		$this->db = new mysqli('localhost', 'root', '', 'bez');
		if ($this->db->connect_errno) 
			$errors[] = "Failed to connect to MySQL: ". $this->db->connect_error;
	}
}
