<?php

namespace dokuwiki\plugin\bez\mdl;

class Label extends Entity {
    protected $id, $name, $count;

    public static function get_columns() {
        return array('id', 'name', 'count');
    }

    public function __construct($model) {
        parent::__construct($model);

        $this->validator->set_rules(array(
            'name' => array(array('length', 100), 'NOT NULL'),
        ));
    }
}