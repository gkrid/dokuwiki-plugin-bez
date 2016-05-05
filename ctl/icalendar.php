<?php

include_once DOKU_PLUGIN."bez/models/tasks.php";

$tasko = new Tasks();
$task_id = (int)$nparams['tid'];
$task = $tasko->getone($task_id);
$task_joined = $tasko->join($task);

$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Grupa Konsultingowa RID Anna i Robert Olewniczak s.c./NONSGML v1.0//EN
BEGIN:VEVENT
UID:z$nparams[tid]@$_SERVER[SERVER_NAME]
DTSTAMP:" . date('Ymd').'T'. date('His'). "\n";

$task_datestamp = strtotime($task['plan_date']);

if ($task['all_day_event'] == '0') {
	$start_timestamp = strtotime($task['start_time']);
	$finish_timestamp = strtotime($task['finish_time']);
	$ical .= "DTSTART:". date('Ymd', $task_datestamp).'T'. date('His', $start_timestamp)."\n";
	$ical .= "DTEND:". date('Ymd', $task_datestamp).'T'. date('His', $finish_timestamp)."\n";
} else {
	$ical .= "DTSTART:". date('Ymd', $task_datestamp)."\n";
}

$url=strtok($_SERVER['REQUEST_URI'],'?');
$cause_id = $task['cause'];
$issue_id = $task['issue'];

if ($cause_id == '')
	$attch_id = "?id=bez:issue_task:id:$issue_id:tid:$task_id";
else
	$attch_id = "?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$task_id";

$full_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . 
$_SERVER['HTTP_HOST'] . $url . $attch_id;

$ical .= "DESCRIPTION:$task[task]
SUMMARY: #z$nparams[tid] ".lcfirst($task_joined['action'])." (".lcfirst($task_joined['state'])."); ".lcfirst($bezlang['executor']).": ".$task_joined['executor']."
LOCATION: $_SERVER[SERVER_NAME]
ATTACH: $full_url
END:VEVENT
END:VCALENDAR";

//set correct content-type-header
header('Content-type: text/calendar; charset=utf-8');
header("Content-Disposition: inline; filename=z$nparams[tid].ics");
echo $ical;
exit;
