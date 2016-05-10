<?php
$tid = $task_joined['id'];
echo "BEGIN:VEVENT
UID:z$tid@$_SERVER[SERVER_NAME]
DTSTAMP:" . date('Ymd').'T'. date('His'). "\n";

$task_datestamp = strtotime($task_joined['plan_date']);

if ($task_joined['all_day_event'] == '0') {
	$start_timestamp = strtotime($task_joined['start_time']);
	$finish_timestamp = strtotime($task_joined['finish_time']);
	echo "DTSTART:". date('Ymd', $task_datestamp).'T'. date('His', $start_timestamp)."\n";
	echo "DTEND:". date('Ymd', $task_datestamp).'T'. date('His', $finish_timestamp)."\n";
} else {
	echo "DTSTART:". date('Ymd', $task_datestamp)."\n";
}

$url=strtok($_SERVER['REQUEST_URI'],'?');
$cause_id = $task_joined['cause'];
$issue_id = $task_joined['issue'];

if ($issue_id == '')
	$attch_id = "?id=bez:show_task:tid:$tid";	
elseif ($cause_id == '')
	$attch_id = "?id=bez:issue_task:id:$issue_id:tid:$tid";
else
	$attch_id = "?id=bez:issue_cause_task:id:$issue_id:cid:$cause_id:tid:$tid";

$full_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . 
$_SERVER['HTTP_HOST'] . $url . $attch_id;

echo "DESCRIPTION:".preg_replace('/\s\s+/', ' ', $task_joined['task'])."\n";
echo "SUMMARY: #z$tid ".lcfirst($task_joined['action'])." (".lcfirst($task_joined['state'])."); ".lcfirst($bezlang['executor']).": ".$task_joined['executor']."
LOCATION: $_SERVER[SERVER_NAME]
ATTACH: $full_url
END:VEVENT\n";

