<?php

namespace dokuwiki\plugin\bez\meta;

class ValidationException extends \Exception
{
    protected $validaion_errors = array();
    protected $table = '';
    // Redefine the exception so message isn't optional
    public function __construct($table, $validaion_errors, $code = 0, Exception $previous = null) {
        $this->validaion_errors = $validaion_errors;
        $this->table = $table;
        $message = 'Validation errors';
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    public function get_errors() {
        return $this->validaion_errors;
    }
}