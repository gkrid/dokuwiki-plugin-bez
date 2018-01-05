<?php

namespace dokuwiki\plugin\bez\mdl;

use dokuwiki\plugin\bez\meta\PermissionDeniedException;

define('BEZ_TABLE_PERMISSION_UNKNOWN', -1);
define('BEZ_TABLE_PERMISSION_SELECT', 0);
define('BEZ_TABLE_PERMISSION_INSERT', 1);

abstract class Factory {
    /** @var Model */
	protected $model;

	public function permission() {
	    return BEZ_TABLE_PERMISSION_INSERT;
    }

	protected function filter_field_map($field) {
        $table = $this->get_table_view();
        if (!$table) {
            $table = $this->get_table_name();
        }

	    return "$table.$field";
    }

    protected function select_query() {
	    $table = $this->get_table_view();
	    if (!$table) {
	        $table = $this->get_table_name();
        }
        return "SELECT * FROM $table";
    }

    public function get_table_view() {
	    return false;
    }

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
                    $execute[":$filter"] = $value;
                } elseif (empty($value)) {
                    $where_q[] = "($field IS NULL OR $field = '')";
                } else {
                    $where_q[] = "$field $operator :$filter";
                    $execute[":$filter"] = $value;
                }

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

    public function get_all($filters=array(), $orderby='', $desc=true, $defaults=array(), $limit=false) {

        list($where_q, $execute) = $this->build_where($filters);
		
		$q = $this->select_query() . $where_q;

		if ($orderby != '') {
		    $fields = call_user_func(array($this->get_object_class_name(), 'get_columns'));
		    if (!in_array($orderby, $fields)) {
		        throw new \Exception('unknown field '.$orderby);
            }
		    $q .= " ORDER BY $orderby";
		    if ($desc) {
		        $q .= " DESC";
            }
        }

        if (is_int($limit)) {
		    $q .= " LIMIT $limit";
        }

		$sth = $this->model->db->prepare($q);

        $sth->setFetchMode(\PDO::FETCH_CLASS, $this->get_object_class_name(),
                           array($this->model, $defaults));
				
		$sth->execute($execute);
						
		return $sth;
    }
    
    public function count($filters=array()) {
        $table = $this->get_table_view();
        if (!$table) {
            $table = $this->get_table_name();
        }

        list($where_q, $execute) = $this->build_where($filters);
        
        $q = "SELECT COUNT(*) FROM $table " . $where_q;
        $sth = $this->model->db->prepare($q);
        $sth->execute($execute);
        
        $count = $sth->fetchColumn();
        return $count;
    }

    public function get_one($id, $defaults=array()) {
        $table = $this->get_table_view();
        if (!$table) {
            $table = $this->get_table_name();
        }

		$q = $this->select_query()." WHERE $table.id = ?";
						
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
		
		$obj = $sth->fetchObject($this->get_object_class_name(),
					array($this->model, $defaults));
        
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
    
    public function create_object($defaults=array()) {
        $object_name = $this->get_object_class_name();
        
		$obj = new $object_name($this->model, $defaults);
		return $obj;
	}

	protected function beginTransaction() {
        $this->model->sqlite->query('BEGIN TRANSACTION');
    }

    protected function commitTransaction() {
        $this->model->sqlite->query('COMMIT TRANSACTION');
    }

    protected function rollbackTransaction() {
        $this->model->sqlite->query('ROLLBACK');
    }

	protected function insert(Entity $obj) {
        if ($obj->id != NULL) {
            throw new \Exception('row already saved');
        }

        $set = array();
        $execute = array();
        $columns = array();
        foreach ($obj->get_columns() as $column) {
            if ($obj->$column === null) continue;
            $set[] = ":$column";
            $columns[] = $column;
            $value = $obj->$column;
            if ($value === '') {
                $execute[':'.$column] = null;
            } else {
                $execute[':'.$column] = $value;
            }
        }

        $query = 'INSERT INTO '.$this->get_table_name().'
							('.implode(',', $columns).')
							VALUES ('.implode(',', $set).')';

        $sth = $this->model->db->prepare($query);
        $sth->execute($execute);

        $reflectionClass = new \ReflectionClass($obj);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($obj, $this->model->db->lastInsertId());

    }

    protected function update(Entity $obj) {
        if ($obj->id == NULL) {
            throw new \Exception('row not inserted');
        }

        $set = array();
        $execute = array(':id' => $obj->id);
        foreach ($obj->get_columns() as $column) {
            if ($column == 'id') continue;
            $set[] = "$column=:$column";
            $value = $obj->$column;
            if ($value === '') {
                $execute[':'.$column] = null;
            } else {
                $execute[':'.$column] = $value;
            }
        }

        $query = 'UPDATE ' . $this->get_table_name() .
                    ' SET ' . implode(',', $set) .
                    ' WHERE id=:id';

        $sth = $this->model->db->prepare($query);
        $sth->execute($execute);
    }

    public function initial_save(Entity $obj, $data) {
	    $obj->set_data($data);
	    $this->insert($obj);
    }

    public function update_save(Entity $obj, $data) {
	    $obj->set_data($data);
	    $this->update($obj);
    }

    public function save(Entity $obj) {
	    if ($obj->id == NULL) {
	        $this->insert($obj);
        } else {
	        $this->update($obj);
        }
    }
	
	protected function delete_from_db($id) {
		$q = 'DELETE FROM '.$this->get_table_name().' WHERE id = ?';
		$sth = $this->model->db->prepare($q);
		$sth->execute(array($id));
	}
	
	public function delete(Entity $obj) {
        if ($obj->acl_of('id') < BEZ_PERMISSION_DELETE) {
            throw new PermissionDeniedException('cannot delete');
        }
		$this->delete_from_db($obj->id);
	}
}
