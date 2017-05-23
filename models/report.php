<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/users.php";

class Report extends Connect {
	public function __construct() {
		global $errors;
		parent::__construct();
	}


	/*waliduje te pola które są brane przy filtrowaniu*/
	public function validate_filters($filters) {
		$data = array();

		$month = (int)$filters['month'];
		if ($filters['month'] == '-all' || ($month > 0 && $month <= 12))
			$data['month'] = $filters['month'];
		else
			$data['month'] = '-all';	

		$year = (int)$filters['year'];
		if ($filters['year'] == '-all' || ($year >= 2013 && $year <= (int)date('Y')))
			$data['year'] = $filters['year'];
		else
			$data['year'] = '-all';	

		return $data;
	}

	public function where($data_field, $filters, $word='AND') {

		$vfilters = $this->validate_filters($filters);
		$year = $vfilters['year'];
		$month = $vfilters['month'];

		if ($year != '-all') {
			$year = (int)$year;
			if ($month == '-all') {
				$year_from = $year;
				$year_to = $year+1;
				$month_from = 1;
				$month_to = 1;
			} else if ($month < 12) {
				$month = (int)$month;
				$year_from = $year;
				$year_to = $year;
				$month_from = $month;
				$month_to = $month+1;
			} else {
				$month = (int)$month;
				$year_from = $year;
				$year_to = $year+1;
				$month_from = 12;
				$month_to = 1;
			}
			$where[] = "$data_field >= ".mktime(0,0,0,$month_from,1,$year_from);
			$where[] = "$data_field < ".mktime(0,0,0,$month_to,1,$year_to);
		}

		if (count($where) > 0)
			return $word.' '.implode(' AND ', $where);
		return '';
	}

	public function report($filters) {
		global $bezlang, $conf;
		
		$issues_date='issues.date';
		$tasks_date='tasks.date';
		
		$lang = 'pl';
		if ($conf['lang'] != 'pl')
			$lang = 'en';


		$isso = new Issues();
		$where = $this->where($issues_date, $filters);

		$issues_open = $this->fetch_assoc("SELECT issuetypes.$lang as type, COUNT(DISTINCT issues.id) AS number_of_open,
												SUM(tasks.cost) as cost_of_open
												FROM issues JOIN issuetypes ON type = issuetypes.id
												LEFT JOIN tasks ON issues.id = tasks.issue
												WHERE issues.coordinator != '-proposal' AND issues.coordinator != '-rejected' $where
												GROUP BY type
												ORDER BY issues.type");
												
		$issues = $this->fetch_assoc("SELECT issuetypes.$lang as type, COUNT(DISTINCT issues.id) AS number_of_close,
												SUM(tasks.cost) as cost_of_close,
												AVG(issues.last_mod-issues.date) AS average
												FROM issues JOIN issuetypes ON type = issuetypes.id
												LEFT JOIN tasks ON issues.id = tasks.issue
												WHERE issues.state = 1 $where AND
												issues.coordinator != '-proposal' AND
												issues.coordinator != '-rejected'
												GROUP BY type
												ORDER BY issues.type");
		
		$iss_close = array();
		foreach ($issues as $issue) { 
			$iss_close[$issue['type']] = $issue;
		}
		
		foreach ($issues_open as &$issue) { 
			if (isset($iss_close[$issue['type']])) {
				$issue['number_of_close'] = $iss_close[$issue['type']]['number_of_close'];
				$issue['cost_of_close'] = $iss_close[$issue['type']]['cost_of_close'];
				$issue['average'] = $this->helper->days((int)$iss_close[$issue['type']]['average']);
			} else {
				$issue['number_of_close'] = 0;
				$issue['cost_of_close'] = 0;
				$issue['average'] = '---';
			}
		}

		$report['issues'] = $issues_open ;
								
		$a = $this->fetch_assoc("SELECT AVG(issues.last_mod-issues.date) AS average
								FROM issues 
								WHERE issues.state = 1 $where");
		$report['issues_average'] = $this->helper->days((int)$a[0]['average']);

		/*Tasks*/
		
		$where = $this->where($tasks_date, $filters);
		
		$tasko = new Tasks();
		$tasks_open = $this->fetch_assoc("SELECT 
							(CASE	WHEN tasks.issue IS NULL THEN '3'
									WHEN tasks.cause IS NULL OR tasks.cause = '' THEN 0
									WHEN commcauses.type = 1 THEN 1
									ELSE 2 END) AS naction,
									COUNT(*) AS number_of_open, SUM(cost) AS cost_of_open
										FROM tasks LEFT JOIN issues ON tasks.issue = issues.id
										LEFT JOIN commcauses ON tasks.cause = commcauses.id
										WHERE 1 == 1 $where
										GROUP BY naction
										ORDER BY naction");
										
		$tasks = $this->fetch_assoc("SELECT 
							(CASE	WHEN tasks.issue IS NULL THEN '3'
									WHEN tasks.cause IS NULL OR tasks.cause = '' THEN 0
									WHEN commcauses.type = 1  THEN 1
									ELSE 2 END) AS naction,
									COUNT(*) AS number_of_closed_on_time
										FROM tasks LEFT JOIN issues ON tasks.issue = issues.id
										LEFT JOIN commcauses ON tasks.cause = commcauses.id
										WHERE tasks.state = 1 AND date(tasks.close_date, 'unixepoch') <= tasks.plan_date $where
										GROUP BY naction
										ORDER BY naction");
									
		$tsk_closed_on_time = array();
		foreach ($tasks as $task) { 
			$tsk_closed_on_time[$task['naction']] = $task;
		}
		
		
		$tasks = $this->fetch_assoc("SELECT 
							(CASE	WHEN tasks.issue IS NULL THEN '3'
									WHEN tasks.cause IS NULL OR tasks.cause = '' THEN 0
									WHEN commcauses.type = 1  THEN 1
									ELSE 2 END) AS naction,
									COUNT(*) AS number_of_closed_off_time
										FROM tasks LEFT JOIN issues ON tasks.issue = issues.id
										LEFT JOIN commcauses ON tasks.cause = commcauses.id
										WHERE tasks.state = 1 AND date(tasks.close_date, 'unixepoch') > tasks.plan_date $where
										GROUP BY naction
										ORDER BY naction");
									
		$tsk_closed_off_time = array();
		foreach ($tasks as $task) { 
			$tsk_closed_off_time[$task['naction']] = $task;
		}
		
		
		$tasks = $this->fetch_assoc("SELECT 
							(CASE	WHEN tasks.issue IS NULL THEN '3'
									WHEN tasks.cause IS NULL OR tasks.cause = '' THEN 0
									WHEN commcauses.type = 1 THEN 1
									ELSE 2 END) AS naction,
									SUM(cost) AS cost_of_close,
									AVG(tasks.close_date - tasks.date) AS average
										FROM tasks LEFT JOIN issues ON tasks.issue = issues.id
										LEFT JOIN commcauses ON tasks.cause = commcauses.id
										WHERE tasks.state = 1 $where
										GROUP BY naction
										ORDER BY naction");
									
		$tsk_closed = array();
		foreach ($tasks as $task) { 
			$tsk_closed[$task['naction']] = $task;
		}
						
		
		foreach ($tasks_open as &$task) {
			if (isset($tsk_closed_on_time[$task['naction']])) {
				$task['number_of_closed_on_time'] = $tsk_closed_on_time[$task['naction']]['number_of_closed_on_time'];
			} else {
				$task['number_of_closed_on_time'] = 0;
			}
			
			if (isset($tsk_closed_off_time[$task['naction']])) {
				$task['number_of_closed_off_time'] = $tsk_closed_off_time[$task['naction']]['number_of_closed_off_time'];
			} else {
				$task['number_of_closed_off_time'] = 0;
			}
			
			if (isset($tsk_closed[$task['naction']])) {
				$task['cost_of_close'] = $tsk_closed[$task['naction']]['cost_of_close'];
				$task['average'] = $this->helper->days((int)$tsk_closed[$task['naction']]['average']);
			} else {
				$task['cost_of_close'] = 0;
				$task['average'] = '---';
			}
		}

		$report['tasks'] = $tasko->join_all($tasks_open);

		$a = $this->fetch_assoc("SELECT AVG(tasks.close_date - tasks.date) AS average
								FROM tasks JOIN issues ON tasks.issue = issues.id
								WHERE tasks.state = 1 $where");

		$report['tasks_average'] = $this->helper->days((int)$a[0]['average']);


		/*causes*/

		$where = $this->where("tasks.date", $filters);

		$caso = new Causes();
		//onlyt causes with closed tasks
		$report['causes'] = $this->fetch_assoc("
							SELECT tasktypes.$lang as type, COUNT(*) AS number,
							MAX(tasks.close_date - tasks.date) AS average,
							SUM(tasks.cost) as cost 
							FROM tasks JOIN tasktypes ON tasks.tasktype = tasktypes.id
							WHERE tasks.issue IS NOT NULL AND
								  tasks.tasktype IS NOT NULL AND
								  tasks.state = 1
								  $where
							GROUP BY tasks.tasktype");

		
		/*$a = $this->fetch_assoc("SELECT MAX(tasks.close_date - tasks.date) AS average
									FROM causes JOIN tasks ON tasks.cause = causes.id
									WHERE tasks.state == 1 $where");
		$report['causes_avarage'] = $this->helper->days((int)$a[0]['average']);*/
		
		/*foreach ($report['causes']  as &$cause) {
			$cause['average'] = $this->helper->days((int)$cause['average']);
			//$cause['number'] = $cas[$cause['rootcause']]['number'];
		}*/
		//$report['causes'] = $report['causes'];//$caso->join_all($report['causes']);
				
		

		/*$where = $this->where($issues_date, $filters);
		$report['priorities'] = $this->fetch_assoc("SELECT priority, COUNT(*) AS number, AVG(last_mod-date) AS average
													FROM issues
													WHERE state = 1 $where
													GROUP BY priority
													ORDER BY priority DESC");

		$priorities = array(
			'0' => $bezlang['priority_marginal'],
			'1' => $bezlang['priority_important'],
			'2' => $bezlang['priority_crucial'],
		);
		foreach ($report['priorities'] as &$pri) { 
			$pri['priority_nr'] = $pri['priority'];
			$pri['priority'] = $priorities[$pri['priority']];
			$pri['average'] = $this->helper->days((int)$pri['average']);
		}

		$a = $this->fetch_assoc("SELECT COUNT(*) as total, AVG(last_mod-date) AS average
								FROM issues
								WHERE state = 1 $where");
		$report['priorities_total'] = $a[0]['total'];
		$report['priorities_average'] = $this->helper->days((int)$a[0]['average']);*/

		return $report;
	}
	
	public function activity_report($filters) {
		$report = array();
		
		$issue_where = $this->where('issues.date', $filters);
		
		$report['involvement'] = array();
		
		$usro = new Users();
		$dw_users = $usro->get();
		
		
		foreach ($dw_users as $nick => $name) {
			$report['involvement'][$nick] =
				array(	'name' => $name,
						'reporter' => 0,
						'coordinator' => 0,
						'commentator' => 0,
						'executor' => 0,
						'total' => 0);
		}
		
	
		
		//ilość zgłoszonych problemów
		$result = $this->fetch_assoc("SELECT issues.reporter, COUNT(*) AS stat
										FROM issues WHERE 1 $issue_where
										GROUP BY issues.reporter");
		
		foreach ($result as $v) {
			$report['involvement'][$v['reporter']]['reporter'] = $v['stat'];
			$report['involvement'][$v['reporter']]['total'] += $v['stat'];
		}
		
			
		
	//ilość zgłoszonych problemów
		$result = $this->fetch_assoc("SELECT issues.coordinator, COUNT(*) AS stat
										FROM issues WHERE
										issues.coordinator != '-proposal' AND
										issues.coordinator != '-rejected'
										$issue_where
										GROUP BY issues.coordinator");
		
		foreach ($result as $v) {
			$report['involvement'][$v['coordinator']]['coordinator'] = $v['stat'];
			$report['involvement'][$v['coordinator']]['total'] += $v['stat'];
		}

		foreach ($dw_users as $nick => $name) {
			//ilość komentatorów problemów
			$result = $this->fetch_assoc("SELECT COUNT(*) AS stat
							FROM issues JOIN commcauses ON issues.id = commcauses.issue
							WHERE 	
									commcauses.reporter = '$nick'
									$issue_where
										");
										
			$count = (int)$result[0]['stat'];
			$report['involvement'][$nick]['commentator'] = $count;
			$report['involvement'][$nick]['total'] += $count;
		}
		
		foreach ($dw_users as $nick => $name) {
			//ilość wykonawców
			$result = $this->fetch_assoc("SELECT COUNT(*) AS stat
							FROM issues JOIN tasks ON issues.id = tasks.issue
							WHERE 	
									tasks.executor = '$nick'
									$issue_where
										");
										
			$count = (int)$result[0]['stat'];
			$report['involvement'][$nick]['executor'] = $count;
			$report['involvement'][$nick]['total'] += $count;
		}
		
		$sort = function ($a, $b) {
			if ($a['total'] < $b['total']) {
				return true;
			} else if ($a['total'] > $b['total']) {
				return false;
			} else {
				return $a['name'] > $b['name'];
			}
		};
		
		uasort($report['involvement'], $sort);
		
		$report['tasks'] = array();
		
		foreach ($dw_users as $nick => $name) {
			$report['tasks'][$nick] =
				array(	'name' => $name,
						'opened_tasks' => 0,
						'closed_tasks' => 0,
						'rejected_tasks' => 0,
						'total' => 0);
		}
		
		$tasks_where = $this->where('tasks.date', $filters);
		
		$result = $this->fetch_assoc("SELECT tasks.executor, COUNT(*) AS stat
										FROM tasks
										WHERE tasks.state = 0 $tasks_where
										GROUP BY tasks.executor");
		
		foreach ($result as $v) {
			$report['tasks'][$v['executor']]['opened_tasks'] = $v['stat'];
			$report['tasks'][$v['executor']]['total'] += $v['stat'];
		}
		
		$result = $this->fetch_assoc("SELECT tasks.executor, COUNT(*) AS stat
										FROM tasks
										WHERE tasks.state = 1 $tasks_where
										GROUP BY tasks.executor");
		
		foreach ($result as $v) {
			$report['tasks'][$v['executor']]['closed_tasks'] = $v['stat'];
			$report['tasks'][$v['executor']]['total'] += $v['stat'];
		}
		
		
		$result = $this->fetch_assoc("SELECT tasks.executor, COUNT(*) AS stat
										FROM tasks
										WHERE tasks.state = 2 $tasks_where
										GROUP BY tasks.executor");
		
		foreach ($result as $v) {
			$report['tasks'][$v['executor']]['rejected_tasks'] = $v['stat'];
			$report['tasks'][$v['executor']]['total'] += $v['stat'];
		}
		
		uasort($report['tasks'], $sort);
		
		return $report;
	}
}
