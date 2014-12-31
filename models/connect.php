<?php
class Connect {
	protected $db, $helper;
	static protected $lastid;
	protected function errquery($query)
	{
		global $errors;

		$r = $this->db->query($query);
		if ($this->db->error) 
			$errors['mysql'] = "MySQL error(".$this->db->errno."): ". $this->db->error;
		return $r;
	}

	public function lastid()
	{
		return $this->lastid;
	}

	protected function mul_errinsert($data, $table)
	{
		global $errors;
		if (count($errors) > 0)
			return;
		if (!is_array($data[0]))
			return;

		$fields = implode(',', array_keys($data[0]));
		$values = '';
		foreach ($data as $row) {
			$values .= '(';
			foreach ($row as $v)
				$values .= "'".$this->db->real_escape_string($v)."',";
			$values = substr($values, 0, -1);
			$values .= '),';
		}
		$values = substr($values, 0, -1);
		$this->errquery("INSERT INTO $table ($fields) VALUES $values");
		
		$this->lastid = $this->db->insert_id;
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
	protected function errdelete($table, $id)
	{
		global $errors;
		if (count($errors) > 0)
			return;
		$this->errquery("DELETE FROM $table WHERE id=$id");

		/*nie zawsze trafimy, ale często i to nam wystarczy :)*/
		$this->lastid = $id-1;
	}
	protected function sort_by_days($res, $field) {
		$days = 0;
		$last_day = -1;
		$rt = array();
		$today24 = mktime(24, 0, 0);
		foreach ($res as $issue) {
			$issue = $this->join($issue);
			$day = ceil(($today24 - (int)$issue[$field])/(24*60*60)) - 1;
			if ($last_day != $day) {
				$rt[$day] = array();
				$days++;
				$last_day = $day;
			}
			$rt[$day][] = $issue;
			if ($days >= 7)
				break;
		}
		return $rt;
	}
	protected function fetch_assoc($q)
	{
		global $errors;

		$r = $this->errquery($q);
		if (isset($errors['mysql']))
			return array();

		return $r->fetch_all(MYSQLI_ASSOC);
	}
	public function __construct()
	{
		global $errors;
		$this->db = new mysqli('localhost', 'root', '', 'bez');
		if ($this->db->connect_errno) 
			$errors[] = "Failed to connect to MySQL: ". $this->db->connect_error;

		$this->helper = plugin_load('helper', 'bez');
	}
}
