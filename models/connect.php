<?php
class Connect {
	protected $helper;
	static protected $db=NULL, $lastid;

	public function __construct()
	{
		global $errors;
		if ($this->db == NULL) {
			$this->db = new SQLite3(DOKU_INC . 'data/bez.sqlite');
			if (!$this->db) 
				$errors[] = "Failed to open SQLite DB file($file): ". $this->db->lastErrorMsg();
		}

		$this->helper = plugin_load('helper', 'bez');
	}
	protected function escape($s) {
		return $s;
	}

	protected function errquery($query)
	{
		global $errors;

		$r = $this->db->query($query);
		if (!$r) 
			$errors['sqlite'] = "SQLite error(".$this->db->lastErrorCode()."): ". $this->db->lastErrorMsg()."\nQuery: $query";
		return $r;
	}

	public function lastid()
	{
		return $this->lastid;
	}
	
	public function join_all($a) {
		foreach ($a as &$v)
			$v = $this->join($v);
		return $a;
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
				$values .= "'".$this->escape($v)."',";
			$values = substr($values, 0, -1);
			$values .= '),';
		}
		$values = substr($values, 0, -1);
		$this->errquery("INSERT INTO $table ($fields) VALUES $values");
		
		$this->lastid = $this->db->lastInsertRowId();
	}

	protected function errinsert($data, $table)
	{
		global $errors;
		if (count($errors) > 0)
			return;

		$fields = implode(',', array_keys($data));
		$values = '';
		foreach ($data as $v)
			$values .= "'".$this->escape($v)."',";
		$values = substr($values, 0, -1);
		$this->errquery("INSERT INTO $table ($fields) VALUES ($values)");
		
		$this->lastid = $this->db->lastInsertRowId();
	}
	protected function errupdate($data, $table, $id)
	{
		global $errors;
		if (count($errors) > 0)
			return;

		$values = '';
		foreach ($data as $k => $v)
			$values .= "$k='".$this->escape($v)."',";
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
		foreach ($res as $obj) {
			$obj = $this->join($obj);
			$day = ceil(($today24 - (int)$obj[$field])/(24*60*60)) - 1;
			if ($last_day != $day) {
				$rt[$day] = array();
				$days++;
				$last_day = $day;
			}
			$rt[$day][] = $obj;
			if ($days >= 7)
				break;
		}
		return $rt;
	}
	protected function fetch_assoc($q)
	{
		global $errors;

		$r = $this->errquery($q);
		if (isset($errors['sqlite']))
			return array();

		$ar = array();
		while ($w = $r->fetchArray(SQLITE3_ASSOC)) {
			$ar[] = $w;
		}
		return $ar;
	}
}
