<?php
 
if(!defined('DOKU_INC')) die();

require_once 'factory.php';

class BEZ_mdl_Timeline extends BEZ_mdl_Factory {
		
	public function get_all($days_back = 30) {
        //validate
        $days_back = (int)$days_back;
        
		if ($this->model->acl->get_level() < BEZ_AUTH_USER) {
            throw new PermissionDeniedException();
        }
		
		$q = "SELECT * FROM (
            SELECT  id,
                    id AS issue,
                    title,
                    coordinator AS author,
                    description_cache AS desc,
                    strftime('%Y-%m-%d', date, 'unixepoch') AS date,
                    strftime('%H:%M', date, 'unixepoch') AS time,
                    'issue_created' AS class FROM issues
            UNION ALL
            SELECT  id,
                    issue,
                    '' AS title,
                    executor AS author,
                    task_cache AS desc,
                    strftime('%Y-%m-%d', date, 'unixepoch') AS date,
                    strftime('%H:%M', date, 'unixepoch') AS time,
                    'task_opened' AS class FROM tasks
            UNION ALL
            SELECT  id,
                    issue,
                    '' AS title,
                    reporter AS author,
                    content_cache AS desc,
                    strftime('%Y-%m-%d', datetime) AS date,
                    strftime('%H:%M', datetime) AS time,
                    (CASE
						WHEN type = '0' THEN 'comment'
                        ELSE 'cause'
                    END) AS class FROM commcauses
        )   WHERE date >= datetime('now', '-".$days_back." days')
            ORDER BY date DESC";
        		
		$sth = $this->model->db->prepare($q);
		$sth->setFetchMode(PDO::FETCH_OBJ);

		$sth->execute();
		return $sth;
	}	
}
