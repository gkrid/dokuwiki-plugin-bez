<?php
 
if(!defined('DOKU_INC')) die();

abstract class BEZ_mdl_Factory {
	protected $model;
	
	protected $filter_field_map = array();
	
	protected function build_where($filters=array()) {
		$execute = array();
		$where_q = array();
		foreach ($filters as $filter => $value) {
			if (isset($this->filter_field_map[$filter])) {
				$field = $this->filter_field_map[$filter];
			} else {
				$field = $filter;
			}
			
			$operator = '=';
            $function = '';
			if (is_array($value)) {
                $operators = array('!=', '<', '>', '<=', '>=');
                $functions = array('', 'date');
                
                $operator = $value[0];
                $function = isset($value[2]) ? $value[2] : '';
                
                $value = $value[1];
                
				if (!in_array($operator, $operators)) {
                    throw new Exception('unknown operator: '.$operator);
                }
                
                if (!in_array($function, $functions)) {
                    throw new Exception('unknown function: '.$function);
                }
			}
            
			if ($function !== '') {
                $where_q[] = $field." $operator $function(:$filter)";
            } else {
                $where_q[] = $field." $operator :$filter";
            }
			$execute[":$filter"] = $value;
		}
		
		$where = '';
		if (count($where_q) > 0) {
			$where = ' WHERE '.implode(' AND ', $where_q);
		}	
		return array($where, $execute);
	}
	
	public function __construct($model) {
		$this->model = $model;
	}
	

	public function get_table_name() {
		$class = get_class($this);
		$exp = explode('_', $class);
		$table = lcfirst($exp[2]);
		return $table;
	}
    
	public function save($obj) {
		$set = array();
		$execute = array();
		foreach ($obj->get_columns() as $column) {
			$set[] = ":$column";
			$execute[':'.$column] = $obj->$column;
		}
				
		$query = 'REPLACE INTO '.$this->get_table_name().'
							('.implode(',', $obj->get_columns()).')
							VALUES ('.implode(',', $set).')';
									
		$sth = $this->model->db->prepare($query);
		$sth->execute($execute);

        //new object is created
        if ($obj->id === NULL) {
            $id = $this->model->db->lastInsertId();
            $obj->set_id($id);
        }
        
		return $id;
	}
	
	protected function delete_from_db($id) {
		$q = 'DELETE FROM '.$this->get_table_name().' WHERE id = ?';
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
	}
	
	public function delete($obj) {        
        //if user can change id, he can delete record
        $this->model->acl->can_change($obj, 'id');
		$this->delete_from_db($obj->id);
	}
}
