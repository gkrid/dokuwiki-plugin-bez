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

	public function report() {
		global $bezlang;

		$isso = new Issues();
		$report['issues'] = $this->fetch_assoc("SELECT issues.type, COUNT(DISTINCT issues.id) AS number,
												SUM(tasks.cost) as totalcost
												FROM issues LEFT JOIN tasks ON issues.id = tasks.issue GROUP BY type
												ORDER BY issues.type");
		$report['issues'] = $isso->join_all($report['issues']);
		$a = $this->fetch_assoc("SELECT COUNT(DISTINCT issues.id) AS total, SUM(cost) AS totalcost
								FROM issues LEFT JOIN tasks ON issues.id = tasks.issue");
		$report['issues_total'] = $a[0]['total'];
		$report['issues_totalcost'] = $a[0]['totalcost'];

		$tasko = new Tasks();
		$report['tasks'] = $this->fetch_assoc("SELECT action, COUNT(*) AS number, SUM(cost) AS totalcost FROM tasks GROUP BY action
												ORDER BY action");
		$report['tasks'] = $tasko->join_all($report['tasks']);
		$a = $this->fetch_assoc("SELECT COUNT(*) AS total, SUM(cost) AS totalcost FROM tasks");
		$report['tasks_total'] = $a[0]['total'];
		$report['tasks_totalcost'] = $a[0]['totalcost'];


		$caso = new Causes();
		$report['causes'] = $this->fetch_assoc("SELECT rootcause, COUNT(*) AS number FROM causes GROUP BY rootcause
												ORDER BY number DESC, rootcause");
		$report['causes'] = $caso->join_all($report['causes']);
		$a = $this->fetch_assoc("SELECT COUNT(*) AS total FROM causes");
		$report['causes_total'] = $a[0]['total'];

		$report['priorities'] = $this->fetch_assoc("SELECT priority, COUNT(*) AS number, AVG(last_mod-date) AS average
													FROM issues
													WHERE state = 1
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
								WHERE state = 1");
		$report['priorities_total'] = $a[0]['total'];
		$report['priorities_average'] = $this->helper->days((int)$a[0]['average']);

		return $report;
	}
}
