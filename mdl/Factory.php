<?php

namespace dokuwiki\plugin\bez\mdl;

abstract class Factory {
    /** @var Model */
	protected $model;

	protected function filter_field_map($field) {
	    return $field;
    }

    protected abstract function select_query();

    protected function build_where($filters=array()) {
		$execute = array();
		$where_q = array();
		foreach ($filters as $filter => $value) {
            $field = $this->filter_field_map($filter);
			
            //parser
			$operator = '=';
            $function = '';
            $function_args = array();
			if (is_array($value)) {
                $operators = array('!=', '<', '>', '<=', '>=', 'LIKE', 'BETWEEN', 'OR');
                $functions = array('', 'date');
                
                $operator = $value[0];
                $function = isset($value[2]) ? $value[2] : '';
                
                if (is_array($function)) {
                    $function = $function[0];
                    $function_args = array_slice($value[2], 1);
                }
                
                $value = $value[1];
                
				if (!in_array($operator, $operators)) {
                    throw new \Exception('unknown operator: '.$operator);
                }
                
                if (!in_array($function, $functions)) {
                    throw new \Exception('unknown function: '.$function);
                }
			}
            
            //builder
            if ($operator === 'BETWEEN') {
                if (count($value) < 2) {
                    throw new \Exception('wrong BETWEEN argument. provide two values');
                }
                if ($function !== '') {
                    array_unshift($function_args, $field);
                    $where_q[] = "$function(".implode(',', $function_args).") BETWEEN :${filter}_start AND :${filter}_end";
                } else {
                    $where_q[] = "$field BETWEEN :${filter}_start AND :${filter}_end";
                }
                $execute[":${filter}_start"] = $value[0];
                $execute[":${filter}_end"] = $value[1];
            } elseif ($operator === 'OR') {
                if (!is_array($value)) {
                    throw new \Exception('$data should be an array');
                }
                
                $where_array = array();
                
                foreach ($value as $k => $v) {
                    $exec = ":${filter}_$k";
                    $where_array[] = "$field = $exec";
                    $execute[$exec] = $v;
                }
                $where_q[] = '('.implode('OR', $where_array).')';
                
                
            } else {
                if ($function !== '') {
                    array_unshift($function_args, $field);
                    $where_q[] = "$function(".implode(',', $function_args).") $operator :$filter";
                } else {
                    if ($value === NULL && $operator === '=') {
                        $operator = 'IS';
                    }
                    $where_q[] = "$field $operator :$filter";
                }
                $execute[":$filter"] = $value;
            }
		}
		
		$where = '';
		if (count($where_q) > 0) {
			$where = ' WHERE '.implode(' AND ', $where_q);
		}	
		return array($where, $execute);
	}
	
	public function __construct(Model $model) {
		$this->model = $model;
	}

	public function acl_static($field) {
        return $this->model->acl->check_static_field($this->get_table_name(), $field);
    }
        
    //chek acl
    public function get_all($filters=array()) {
//        $dummy = $this->get_dummy_object();
//        if ($dummy->acl_of('id') < BEZ_PERMISSION_VIEW) {
//            throw new PermissionDeniedException();
//        }
//
//        if ($this->select_query === NULL) {
//            throw new \Exception('no select query defined');
//        }
        
        list($where_q, $execute) = $this->build_where($filters);
		
		$q = $this->select_query() . $where_q;

		$sth = $this->model->db->prepare($q);

		$sth->setFetchMode(\PDO::FETCH_CLASS, $this->get_object_class_name(),
				array($this->model));
				
		$sth->execute($execute);
						
		return $sth;
    }
    
    public function count($filters=array()) {
//        $dummy = $this->get_dummy_object();
//        if ($dummy->acl_of('id') < BEZ_PERMISSION_VIEW) {
//            throw new PermissionDeniedException();
//        }

        if ($this->acl_static('id') < BEZ_PERMISSION_VIEW) {
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
//        if ($this->select_query === NULL) {
//            throw new \Exception('no select query defined');
//        }
        
		$q = $this->select_query().' WHERE '.$this->get_table_name().'.id = ?';
						
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
		
		$obj = $sth->fetchObject($this->get_object_class_name(),
					array($this->model));
        
        if ($obj === false) {
            throw new \Exception('there is no '.$this->get_table_name().' with id: '.$id);
        }
        
		return $obj;
	}

	public function get_table_name() {
        $class = (new \ReflectionClass($this))->getShortName();
        return lcfirst(str_replace('Factory', '', $class));
	}

    public function get_object_class_name() {
        $class = (new \ReflectionClass($this))->getName();
        return str_replace('Factory', '', $class);
    }
//
//    public function get_table_singular() {
//        $table = $this->get_table_name();
//        $singular = substr($table, 0, -1);
//        return $singular;
//    }
//
//    private function get_singular_object_name() {
//        return ucfirst($this->get_table_singular());
//    }
//
//    private function get_object_class_name() {
//        return 'BEZ_mdl_'.$this->get_singular_object_name();
//    }
    
//    private function get_dummy_object_class_name() {
//        return 'BEZ_mdl_Dummy_'.$this->get_singular_object_name();
//    }
    
    public function create_object($defaults=array()) {
        $object_name = $this->get_object_class_name();
        
		$obj = new $object_name($this->model, $defaults);
		return $obj;
	}

	public function beginTransaction() {
        $this->model->sqlite->query('BEGIN TRANSACTION');
    }

    public function commitTransaction() {
        $this->model->sqlite->query('COMMIT TRANSACTION');
    }

    public function rollbackTransaction() {
        $this->model->sqlite->query('ROLLBACK');
    }
    
//    public function get_dummy_object() {
//        if ($this->dummy_object === NULL) {
//            $dummy_object_name = $this->get_dummy_object_class_name();
//            $this->dummy_object = new $dummy_object_name($this->model);
//        }
//        return $this->dummy_object;
//	}
    
	public function save(Entity $obj) {
        //if user can change id, he can modify record
        //$this->model->acl->can_change($obj, 'id');
        
		$set = array();
		$execute = array();
		$columns = array();
		foreach ($obj->get_columns() as $column) {
            if ($obj->$column === null) continue;
            //id is special -> when null we insert new row
//		    if ($column == 'id' && $obj->id == NULL) continue;

//            if ($obj->$column === null) {
//                throw new \Exception('cannot save object becouse it has uninitialized parameter: '.$column);
//            }
			$set[] = ":$column";
			$columns[] = $column;
            $value = $obj->$column;
            if ($value === '') {
                $execute[':'.$column] = null;
            } else {
                $execute[':'.$column] = $value;
            }
		}
				
		$query = 'REPLACE INTO '.$this->get_table_name().'
							('.implode(',', $columns).')
							VALUES ('.implode(',', $set).')';

		$sth = $this->model->db->prepare($query);
		$sth->execute($execute);

        //new object is created
        if ($obj->id === NULL) {
            $reflectionClass = new \ReflectionClass($obj);
            $reflectionProperty = $reflectionClass->getProperty('id');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($obj, $this->model->db->lastInsertId());
        }
//            $id = $this->model->db->lastInsertId();
//            $obj->set_id($id);

//        }
        
//		return $id;
	}
	
	protected function delete_from_db($id) {
		$q = 'DELETE FROM '.$this->get_table_name().' WHERE id = ?';
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
	}
	
	public function delete(Entity $obj) {
        //if user can change id, he can delete record
        $this->model->acl->can_change($obj, 'id');
		$this->delete_from_db($obj->id);
	}
}
