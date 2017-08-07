<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'tasktype.php';

class BEZ_mdl_Tasktypes extends BEZ_mdl_Factory {
    
    public function __construct($model) {
		parent::__construct($model);
        
        if (empty($this->model->conf['lang'])) {
            $lang_code = 'en';
        } else {
            $lang_code = $this->model->conf['lang'];
        }
        
		$this->select_query = 'SELECT *,
                    '.$lang_code.' as type,
					(SELECT COUNT(*) FROM tasks WHERE tasktype=tasktypes.id) AS refs
					FROM tasktypes';
	}
	    
    public function delete($obj) {
		if ($obj->refs > 0) {
			throw new Exception('you cannot delete tasktype that has any references');
		}
		parent::delete($obj);
	}
	
}
