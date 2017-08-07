<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'issuetype.php';

class BEZ_mdl_Issuetypes extends BEZ_mdl_Factory {
	
    public function __construct($model) {
		parent::__construct($model);
        
        if (empty($this->model->conf['lang'])) {
            $lang_code = 'en';
        } else {
            $lang_code = $this->model->conf['lang'];
        }
        
		$this->select_query = 'SELECT *,
            '.$lang_code.' as type,
            (SELECT COUNT(*) FROM issues WHERE issues.type=issuetypes.id) AS refs
            FROM issuetypes';
	}
	    
    public function delete($obj) {
		if ($obj->refs > 0) {
			throw new Exception('you cannot delete isssetype that has any references');
		}
		parent::delete($obj);
	}
	
}
