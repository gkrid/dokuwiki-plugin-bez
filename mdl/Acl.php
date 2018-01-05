<?php
 
namespace dokuwiki\plugin\bez\mdl;

class Acl {

    protected $acl = array();

    protected $user_level;

    public function __construct($user_level, $columns) {
        $this->user_level = $user_level;

        if ($this->user_level >= BEZ_AUTH_ADMIN) {
            $this->acl = array_fill_keys($columns, BEZ_PERMISSION_DELETE);
        } elseif ($this->user_level >= BEZ_AUTH_VIEWER) {
            $this->acl = array_fill_keys($columns, BEZ_PERMISSION_VIEW);
        } else {
            $this->acl = array_fill_keys($columns, BEZ_PERMISSION_NONE);
        }
    }

    public function grant($columns, $perm) {
        if (!is_array($columns)) $columns = array($columns);

        foreach($columns as $column) {
            if (!array_key_exists($column, $this->acl)) {
                throw new \Exception("column: $column not exists in table");
            }

            if ($this->acl[$column] < $perm) {
                $this->acl[$column] = $perm;
            }
        }
    }

    public function revoke($columns, $level) {
        if (!is_array($columns)) $columns = array($columns);

        foreach ($columns as $column) {
            if ($this->user_level <= $level) {
                $this->acl[$column] = BEZ_PERMISSION_NONE;
            }
        }
    }

    public function acl_of($col) {
        return $this->acl[$col];
    }

    public function get_list() {
        return $this->acl;
    }

    public function add_column($column, $perm=BEZ_PERMISSION_NONE) {
        if (isset($this->acl[$column])) {
            throw new \Exception('column already exists');
        }

        $this->acl[$column] = $perm;
    }
}
