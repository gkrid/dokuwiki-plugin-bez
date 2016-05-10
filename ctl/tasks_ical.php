<?php

include_once DOKU_PLUGIN."bez/models/tasks.php";

$tasko = new Tasks();
/*rekordy parzyste to nagłówki, nieparzyste to ich wartości.*/
/*np. status:1:type:2:podmiot:PCA*/
$value = array('issue' => '-all', 'action' => '-all', 'taskstate' => '-all',
				'executor' => '-all', 'year' => '-all', 'tasktype' => '-all',
				'month' => '-all', 'task' => '', 'reason' => '');
for ($i = 0; $i < count($params); $i += 2)
	$value[urldecode($params[$i])] = urldecode($params[$i+1]);

$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Grupa Konsultingowa RID Anna i Robert Olewniczak s.c./NONSGML v1.0//EN
";
$tasks = $tasko->get_filtered($value, array('task', 'reason'));

ob_start();
foreach ($tasks as $task_joined)
	if ($task_joined['plan_date'] != '')
	include "ical_task.php";
$ical .= ob_get_contents();
ob_end_clean();

$ical .= "END:VCALENDAR";

//set correct content-type-header
header('Content-type: text/calendar; charset=utf-8');
header("Content-Disposition: inline; filename=tasks.ics");
echo $ical;
exit;
