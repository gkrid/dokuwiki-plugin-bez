<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/causes.php";

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

	public function report($filters, $issues_date='issues.last_mod', $tasks_date='tasks.close_date') {
		global $bezlang, $conf;

		$lang = 'pl';
		if ($conf['lang'] != 'pl')
			$lang = 'en';


		$isso = new Issues();
		$where = $this->where($issues_date, $filters);

		$report['issues'] = $this->fetch_assoc("SELECT issuetypes.$lang as type, COUNT(DISTINCT issues.id) AS number,
												SUM(tasks.cost) as totalcost,
												AVG(issues.last_mod-issues.date) AS average
												FROM issues JOIN issuetypes ON type = issuetypes.id
												LEFT JOIN tasks ON issues.id = tasks.issue
												WHERE issues.state = 1 $where
												GROUP BY type
												ORDER BY issues.type");
		$report['issues'] = $isso->join_all($report['issues']);
		foreach ($report['issues'] as &$issue) { 
			$issue['average'] = $this->helper->days((int)$issue['average']);
		}
		$a = $this->fetch_assoc("SELECT COUNT(DISTINCT issues.id) AS total, SUM(cost) AS totalcost
								FROM issues LEFT JOIN tasks ON issues.id = tasks.issue
								WHERE issues.state = 1 $where");
		$report['issues_total'] = $a[0]['total'];
		$report['issues_totalcost'] = $a[0]['totalcost'];
								
		$a = $this->fetch_assoc("SELECT AVG(issues.last_mod-issues.date) AS average
								FROM issues 
								WHERE issues.state = 1 $where");
		$report['issues_average'] = $this->helper->days((int)$a[0]['average']);


		$where = $this->where($tasks_date, $filters);
		$tasko = new Tasks();
		$report['tasks'] = $this->fetch_assoc("SELECT 
							(CASE	WHEN tasks.cause IS NULL OR tasks.cause = '' THEN 0
									WHEN causes.potential = 0 THEN 1
									ELSE 2 END) AS naction,
									tasks.action, COUNT(*) AS number, SUM(cost) AS totalcost,
									AVG(tasks.close_date - tasks.date) AS average
										FROM tasks JOIN issues ON tasks.issue = issues.id
										LEFT JOIN causes ON tasks.cause = causes.id
										WHERE tasks.state = 1 $where
										GROUP BY naction
										ORDER BY naction");
		$report['tasks'] = $tasko->join_all($report['tasks']);
		foreach ($report['tasks'] as &$task) { 
			$task['average'] = $this->helper->days((int)$task['average']);
		}
		$a = $this->fetch_assoc("SELECT COUNT(*) AS total, SUM(cost) AS totalcost, AVG(tasks.close_date - tasks.date) AS average
								FROM tasks JOIN issues ON tasks.issue = issues.id
								WHERE tasks.state = 1 $where");

		$report['tasks_total'] = $a[0]['total'];
		$report['tasks_totalcost'] = $a[0]['totalcost'];
		$report['tasks_average'] = $this->helper->days((int)$a[0]['average']);


		$where = $this->where($issues_date, $filters);

		$caso = new Causes();
		$report['causes'] = $this->fetch_assoc("SELECT rootcause, COUNT(*) AS number
												FROM causes JOIN issues ON causes.issue = issues.id
												WHERE issues.state = 1 $where
												GROUP BY rootcause
												ORDER BY number DESC, rootcause");
		$report['causes'] = $caso->join_all($report['causes']);
		$a = $this->fetch_assoc("SELECT COUNT(*) AS total
									FROM causes JOIN issues ON causes.issue = issues.id
									WHERE issues.state = 1 $where");
		$report['causes_total'] = $a[0]['total'];


		$where = $this->where($issues_date, $filters);
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
		$report['priorities_average'] = $this->helper->days((int)$a[0]['average']);

		return $report;
	}
	public function report_open($filters, $issues_date='issues.date', $tasks_date='tasks.date') {
		global $bezlang, $conf;

		$lang = 'pl';
		if ($conf['lang'] != 'pl')
			$lang = 'en';


		$isso = new Issues();
		$where = $this->where($issues_date, $filters, 'WHERE');

		$report['issues'] = $this->fetch_assoc("SELECT issuetypes.$lang as type, COUNT(DISTINCT issues.id) AS number,
												SUM(tasks.cost) as totalcost
												FROM issues JOIN issuetypes ON type = issuetypes.id
												LEFT JOIN tasks ON issues.id = tasks.issue
												$where
												GROUP BY type
												ORDER BY issues.type");
		$report['issues'] = $isso->join_all($report['issues']);
		$a = $this->fetch_assoc("SELECT COUNT(DISTINCT issues.id) AS total, SUM(cost) AS totalcost
								FROM issues LEFT JOIN tasks ON issues.id = tasks.issue
								$where");
		$report['issues_total'] = $a[0]['total'];
		$report['issues_totalcost'] = $a[0]['totalcost'];
								

		$where = $this->where($tasks_date, $filters, 'WHERE');

		$tasko = new Tasks();
		/*
		 *
						(CASE WHEN tasks.cause = NULL OR  tasks.cause = '' THEN 0 ELSE
						CASE WHEN causes.potential = 0 THEN 1 ELSE 2) as action
		 */
		$report['tasks'] = $this->fetch_assoc("SELECT
									(CASE	WHEN tasks.cause IS NULL OR tasks.cause='' THEN 0
											WHEN causes.potential = 0 THEN 1
											ELSE 2 END) AS naction,
												COUNT(*) AS number, SUM(cost) AS totalcost
												FROM tasks JOIN issues ON tasks.issue = issues.id
												LEFT JOIN causes ON tasks.cause = causes.id
												$where
												GROUP BY naction
												ORDER BY naction");
		$report['tasks'] = $tasko->join_all($report['tasks']);
		$a = $this->fetch_assoc("SELECT COUNT(*) AS total, SUM(cost) AS totalcost
								FROM tasks JOIN issues ON tasks.issue = issues.id
								$where");

		$report['tasks_total'] = $a[0]['total'];
		$report['tasks_totalcost'] = $a[0]['totalcost'];


		$where = $this->where($issues_date, $filters, 'WHERE');

		$caso = new Causes();
		$report['causes'] = $this->fetch_assoc("SELECT rootcause, COUNT(*) AS number
												FROM causes JOIN issues ON causes.issue = issues.id
												$where
												GROUP BY rootcause
												ORDER BY number DESC, rootcause");
		$report['causes'] = $caso->join_all($report['causes']);
		$a = $this->fetch_assoc("SELECT COUNT(*) AS total
									FROM causes JOIN issues ON causes.issue = issues.id
									$where");
		$report['causes_total'] = $a[0]['total'];

		return $report;
	}
}
