<?php
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";
include_once DOKU_PLUGIN."bez/models/taskstates.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/issues.php";

if	(!$helper->user_viewer()) {
	$errors[] = $bezlang['error_issues'];
	$controller->preventDefault();
} 

$tasko = new Tasks();
$taskao = new Taskactions();
$taskso = new Taskstates();
$usro = new Users();
$isso = new Issues();

if (count($_POST) > 0) {
	$filters = $tasko->validate_filters($_POST);

	$query_uri = '';
	foreach ($filters as $k => $v)
		if ($v != '-all')
			$query_uri .= ':'.urlencode($k).':'.urlencode($v);

	header('Location: ?id='.$this->id('tasks').$query_uri);
}

/*rekordy parzyste to nagłówki, nieparzyste to ich wartości.*/
/*np. status:1:type:2:podmiot:PCA*/
$value = array('issue' => '-all', 'action' => '-all', 'state' => '-all', 'executor' => '-all', 'year' => '-all');
for ($i = 0; $i < count($params); $i += 2)
	$value[urldecode($params[$i])] = urldecode($params[$i+1]);


$template['uri'] = $uri; 

$template['issues'] = $isso->get_ids();
$template['actions'] = $taskao->get();
$template['states'] = $taskso->get();
$template['executors'] = $usro->get();
$template['years'] = $tasko->get_years();

$tasks = $tasko->get_filtered($value);
$template['tasks'] = $tasks;

$template['tasks_stats']['total'] = count($tasks);

$tcost = 0;
foreach ($tasks as $task) {
	$tcost += (int)$task['cost'];
}
$template['tasks_stats']['totalcost'] = $tcost;


