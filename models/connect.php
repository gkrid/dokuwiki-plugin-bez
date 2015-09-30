<?php
class Connect {
	protected $helper = NULL;
	static protected $lastid;
	protected $noclose = NULL;

	public function open() {
		$db = new SQLite3(DOKU_INC . 'data/bez.sqlite');
		$db->busyTimeout(1500);
		return $db;
	}

	public function __construct()
	{
		//jeżeli możemy, wczytujemy helpera
		if (function_exists('plugin_load'))
			$this->helper = plugin_load('helper', 'bez');
	}

	public function escape($s) {
		$db = $this->open();
		$e = $db->escapeString($s);
		$db->close();
		return $e;
	}

	public function errquery($query)
	{
		global $errors;

		$db = $this->open();
		$r = $db->query($query);
		if (!$r) 
			$errors['sqlite'] = "SQLite error(".$db->lastErrorCode()."): ". $db->lastErrorMsg()."\nQuery: $query";
		$db->close();
		unset($db);
		return $r;
	}

	public function noclose_query($query)
	{
		global $errors;

		$this->noclose = $this->open();
		$r = $this->noclose->query($query);
		if (!$r) 
			$errors['sqlite'] = "SQLite error(".$this->noclose->lastErrorCode()."): ". $this->noclose->lastErrorMsg()."\nQuery: $query";
		return $r;
	}

	public function ins_query($query)
	{
		global $errors;

		$db = $this->open();
		$r = $db->query($query);
		if (!$r) 
			$errors['sqlite'] = "SQLite error(".$db->lastErrorCode()."): ". $db->lastErrorMsg()."\nQuery: $query";
		$last = $db->lastInsertRowId();
		$db->close();
		unset($db);
		return $last;
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
		$db = 
		$this->lastid = $this->ins_query("INSERT INTO $table ($fields) VALUES $values");
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
		$this->lastid = $this->ins_query("INSERT INTO $table ($fields) VALUES ($values)");
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

		$r = $this->noclose_query($q);
		if (isset($errors['sqlite']))
			return array();

		$ar = array();
		while ($w = $r->fetchArray(SQLITE3_ASSOC)) {
			$ar[] = $w;
		}

		$this->noclose->close();
		unset($this->noclose);
		return $ar;
	}
}
