<?php
/**
 * Created by PhpStorm.
 * User: ghi
 * Date: 14.12.17
 * Time: 09:45
 */

namespace dokuwiki\plugin\bez\mdl;

class Task_program extends Entity {
    protected $id, $name, $count, $added_by, $added_date;

    public static function get_columns() {
        return array('id', 'name', 'count', 'added_by', 'added_date');
    }

    public function __construct($model) {
        parent::__construct($model);

        $this->validator->set_rules(array(
                                        'name' => array(array('length', 100), 'NOT NULL'),
                                    ));

        if ($this->id === NULL) {
            $this->added_by = $this->model->user_nick;
            $this->added_date = date('c');
        }
    }
}