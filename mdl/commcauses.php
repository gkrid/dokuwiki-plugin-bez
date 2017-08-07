<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';
require_once 'commcause.php';

class BEZ_mdl_Commcauses extends BEZ_mdl_Factory {

	public function __construct($model) {
		parent::__construct($model);
        $this->select_query =
                    "SELECT commcauses.id, commcauses.issue, commcauses.datetime,
                            commcauses.reporter, commcauses.type, commcauses.content,
                            commcauses.content_cache, issues.coordinator,
                            (SELECT COUNT(*) FROM tasks
                                WHERE tasks.cause = commcauses.id) AS tasks_count
                    FROM commcauses JOIN issues ON commcauses.issue = issues.id";
	}

	protected $filter_field_map = array(
		'type' 		=> 'commcauses.type',
        'issue'     => 'commcauses.issue'
	);
	
	public function get_causes_ids($issue) {		
		$q = "SELECT commcauses.id
				FROM commcauses JOIN issues ON commcauses.issue = issues.id
				WHERE issue = ? AND commcauses.type != '0'";
		
		$sth = $this->model->db->prepare($q);
		$sth->setFetchMode(PDO::FETCH_NUM);

		$sth->execute(array($issue));
		$arr = $sth->fetchAll();
		
		return array_map(function($elm) { return $elm[0]; }, $arr);
	}
}
