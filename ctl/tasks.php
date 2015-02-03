<?php
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";
include_once DOKU_PLUGIN."bez/models/taskstates.php";

if	(!$helper->user_viewer()) {
	$errors[] = $bezlang['error_issues'];
	$controller->preventDefault();
} 

$tasko = new Tasks();
$taskao = new Taskactions();
$taskso = new Taskstates();

if (count($_POST) > 0) {
	$filters = $tasko->validate_filters($_POST);

	$query_uri = '';
	foreach ($filters as $k => $v)
		if ($v != '-all')
			$query_uri .= ':'.urlencode($k).':'.urlencode($v);

	header('Location: ?id=bez:tasks'.$query_uri);
}

/*rekordy parzyste to nagłówki, nieparzyste to ich wartości.*/
/*np. status:1:type:2:podmiot:PCA*/
$value = array('action' => '-all', 'state' => '-all', 'executor' => '-all', 'year' => '-all');
for ($i = 0; $i < count($params); $i += 2)
	$value[urldecode($params[$i])] = urldecode($params[$i+1]);


$template['uri'] = $uri; 

$template['actions'] = $taskao->get();
$template['states'] = $taskso->get();
$template['executors'] = $tasko->get_executors();
$template['years'] = $tasko->get_years();

$template['tasks'] = $tasko->get_filtered($value);
