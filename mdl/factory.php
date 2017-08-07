<?php
 
if(!defined('DOKU_INC')) die();

abstract class BEZ_mdl_Factory {
	protected $model, $dummy_object;
    
    protected $select_query;
	
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
        
    //chek acl
    public function get_all($filters=array()) {
        $dummy = $this->get_dummy_object();
        if ($dummy->acl_of('id') < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }
        
        if ($this->select_query === NULL) {
            throw new Exception('no select query defined');
        }
        
        list($where_q, $execute) = $this->build_where($filters);
		
		$q = $this->select_query . $where_q;
			
		$sth = $this->model->db->prepare($q);
		
		$sth->setFetchMode(PDO::FETCH_CLASS, $this->get_object_class_name(),
				array($this->model));
				
		$sth->execute($execute);
						
		return $sth;
    }
    
    public function count($filters=array()) {
        $dummy = $this->get_dummy_object();
        if ($dummy->acl_of('id') < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }
               
        list($where_q, $execute) = $this->build_where($filters);
        
        $q = 'SELECT COUNT(*) FROM ' . $this->get_table_name() . ' ' . $where_q;
        $sth = $this->model->db->prepare($q);
        $sth->execute($execute);
        
        $count = $sth->fetchColumn();
        return $count;
    }
    
    public function get_one($id) {
        if ($this->select_query === NULL) {
            throw new Exception('no select query defined');
        }
        
		$q = $this->select_query.' WHERE '.$this->get_table_name().'.id = ?';
						
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
		
		$obj = $sth->fetchObject($this->get_object_class_name(),
					array($this->model));
        
        if ($obj === false) {
            throw new Exception('there is no '.$this->get_table_singular().' with id: '.$id);
        }
        
		return $obj;
	}

	public function get_table_name() {
		$class = get_class($this);
		$exp = explode('_', $class);
		$table = lcfirst($exp[2]);
		return $table;
	}
    
    public function get_table_singular() {
        $table = $this->get_table_name();
        $singular = substr($table, 0, -1);
        return $singular;
    }
    
    private function get_singular_object_name() {
        return ucfirst($this->get_table_singular());
    }
    
    private function get_object_class_name() {
        return 'BEZ_mdl_'.$this->get_singular_object_name();
    }
    
    private function get_dummy_object_class_name() {
        return 'BEZ_mdl_Dummy_'.$this->get_singular_object_name();
    }
    
    public function create_object($defaults=array()) {
        $object_name = $this->get_object_class_name();
        
		$obj = new $object_name($this->model, $defaults);
		return $obj;
	}
    
    public function get_dummy_object() {
        if ($this->dummy_object === NULL) {
            $dummy_object_name = $this->get_dummy_object_class_name();
            $this->dummy_object = new $dummy_object_name($this->model);
        }
        return $this->dummy_object;
	}
    
	public function save($obj) {
        //if user can change id, he can modify record
        $this->model->acl->can_change($obj, 'id');
        
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
