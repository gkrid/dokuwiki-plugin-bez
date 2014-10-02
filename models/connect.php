<?php
class Connect {
	protected $db;
	static protected $lastid;
	protected function errquery($query)
	{
		global $errors;

		$r = $this->db->query($query);
		if ($this->db->error) 
			$errors[] = "MySQL error(".$this->db->errno."): ". $this->db->error;
		return $r;
	}

	public function lastid()
	{
		return $this->lastid;
	}

	protected function errinsert($data, $table)
	{
		global $errors;
		if (count($errors) > 0)
			return;

		$fields = implode(',', array_keys($data));
		$values = '';
		foreach ($data as $v)
			$values .= "'".$this->db->real_escape_string($v)."',";
		$values = substr($values, 0, -1);
		$this->errquery("INSERT INTO $table ($fields) VALUES ($values)");
		
		$this->lastid = $this->db->insert_id;
	}
	protected function errupdate($data, $table, $id)
	{
		global $errors;
		if (count($errors) > 0)
			return;

		$values = '';
		foreach ($data as $k => $v)
			$values .= "$k='".$this->db->real_escape_string($v)."',";
		$values = substr($values, 0, -1);
		$this->errquery("UPDATE $table SET $values WHERE id=$id");

		$this->lastid = $id;
	}
	protected function fetch_assoc($q)
	{
		global $errors;

		$r = $this->errquery($q);
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
