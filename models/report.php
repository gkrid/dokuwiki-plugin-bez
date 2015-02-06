<?php
include_once DOKU_PLUGIN."bez/models/connect.php";
include_once DOKU_PLUGIN."bez/models/entities.php";
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

		$ento = new Entities();
		if ($filters['entity'] == '-all' || in_array($filters['entity'], $ento->get_list()))
			$data['entity'] = $filters['entity'];
		else
			$data['entity'] = '-all';	

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
		$entity = $vfilters['entity'];
		$year = $vfilters['year'];
		$month = $vfilters['month'];

		if ($entity != '-all') {
			$where[] = "issues.entity = '".$this->db->real_escape_string($entity)."'";
		}
		
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
		global $bezlang;


		$isso = new Issues();
		$where = $this->where('issues.last_mod', $filters);

		$report['issues'] = $this->fetch_assoc("SELECT issues.type, COUNT(DISTINCT issues.id) AS number,
												SUM(tasks.cost) as totalcost
												FROM issues LEFT JOIN tasks ON issues.id = tasks.issue
												WHERE issues.state = 1 $where
												GROUP BY type
												ORDER BY issues.type");
		$report['issues'] = $isso->join_all($report['issues']);
		$a = $this->fetch_assoc("SELECT COUNT(DISTINCT issues.id) AS total, SUM(cost) AS totalcost
								FROM issues LEFT JOIN tasks ON issues.id = tasks.issue
								WHERE issues.state = 1 $where");
		$report['issues_total'] = $a[0]['total'];
		$report['issues_totalcost'] = $a[0]['totalcost'];


		$where = $this->where('tasks.close_date', $filters);
		$tasko = new Tasks();
		$report['tasks'] = $this->fetch_assoc("SELECT tasks.action, COUNT(*) AS number, SUM(cost) AS totalcost
												FROM tasks JOIN issues ON tasks.issue = issues.id
												WHERE tasks.state = 1 $where
												GROUP BY action
												ORDER BY action");
		$report['tasks'] = $tasko->join_all($report['tasks']);
		$a = $this->fetch_assoc("SELECT COUNT(*) AS total, SUM(cost) AS totalcost
								FROM tasks JOIN issues ON tasks.issue = issues.id
								WHERE tasks.state = 1 $where");

		$report['tasks_total'] = $a[0]['total'];
		$report['tasks_totalcost'] = $a[0]['totalcost'];


		$where = $this->where('issues.last_mod', $filters);

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


		$where = $this->where('issues.last_mod', $filters);
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
}
