<?php

include_once DOKU_PLUGIN."bez/models/tasks.php";

$tasko = new Tasks();
$task_id = (int)$nparams['tid'];
$task = $tasko->getone($task_id);
$task_joined = $tasko->join($task, array('task', 'reason'));

$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Grupa Konsultingowa RID Anna i Robert Olewniczak s.c./NONSGML v1.0//EN
";

ob_start();
include "ical_task.php";
$ical .= ob_get_contents();
ob_end_clean();

$ical .= "END:VCALENDAR";

//set correct content-type-header
header('Content-type: text/calendar; charset=utf-8');
header("Content-Disposition: inline; filename=z$nparams[tid].ics");
echo $ical;
exit;
