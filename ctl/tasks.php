<?php

/** @var action_plugin_bez $this */

define('BEZ_THREAD_FILTERS_COOKIE_NAME', 'bez_task_filters');

if (count($_POST) > 0) {
    $raw_filters = $_POST;
} elseif (empty($this->params) && isset($_COOKIE[BEZ_THREAD_FILTERS_COOKIE_NAME])) {
    $raw_filters = $_COOKIE[BEZ_THREAD_FILTERS_COOKIE_NAME];
}

if (isset($raw_filters)) {
    //save filters
    foreach ($raw_filters as $k => $v) {
        setcookie(BEZ_THREAD_FILTERS_COOKIE_NAME."[$k]", $v);
    }

    $filters = array_filter($raw_filters, function($v) {
        return $v !== '-all' && $v !== '';
    });

    if (empty($filters)) {
        $filters['year'] = '-all';
    }

    header('Location: '.$this->url('tasks', $filters));
} else {
    $filters = $this->params;
}

$this->tpl->set_values($filters);

$years = $this->model->threadFactory->get_years_scope();

//some filters are just copied
$db_filters = array_filter($filters, function ($k) {
    return in_array($k, array('thread_id', 'state', 'type', 'task_program_id'));
}, ARRAY_FILTER_USE_KEY);

//-none filters become empty filters
$db_filters = array_map(function($v) {
    if ($v === '-none') {
        return '';
    }
    return $v;
}, $db_filters);

if (isset($filters['year']) && $filters['year'] !== '-all') {
    $year = $filters['year'];

    $start_day = "$year-01-01";
    $end_day = "$year-12-31";

    $db_filters['create_date'] = array('BETWEEN', array($start_day, $end_day), array('date'));
}

if (isset($filters['assignee']) &&
    substr($filters['assignee'], 0, 1) === '@') {
    $group = substr($filters['assignee'], 1);
    $db_filters['assignee'] = array('OR', $this->model->userFactory->users_of_group($group));
}

if (isset($filters['content'])) {
    $content = preg_replace('/\s/', '%', $filters['content']);
    $db_filters['content'] = array('LIKE', "%$content%");
}

$orderby = 'last_activity_date';

$tasks = $this->model->taskFactory->get_all($db_filters, $orderby);

$this->tpl->set('task_programs', $this->model->task_programFactory->get_all());
$this->tpl->set('tasks', $tasks);
$this->tpl->set('years', $years);


//include_once DOKU_PLUGIN."bez/models/tasks.php";
//include_once DOKU_PLUGIN."bez/models/taskactions.php";
//include_once DOKU_PLUGIN."bez/models/taskstates.php";
//include_once DOKU_PLUGIN."bez/models/tasktypes.php";
//include_once DOKU_PLUGIN."bez/models/users.php";
//include_once DOKU_PLUGIN."bez/models/issues.php";
//
//if	(!$helper->user_viewer()) {
//	throw new PermissionDeniedException();
//}
//
//$tasko = new Tasks();
//$taskao = new Taskactions();
//$taskso = new Taskstates();
//$tasktypeso = new Tasktypes();
//$usro = new Users();
//$isso = new Issues();
//
//if (count($_POST) > 0)
//	$raw_filters = $_POST;
//elseif (count($nparams) === 1 && isset($_COOKIE['bez_tasks_filters']))
//	$raw_filters = $_COOKIE['bez_tasks_filters'];
//
//if (isset($raw_filters)) {
//	$filters = $tasko->validate_filters($raw_filters);
//	$query_uri = '';
//	foreach ($filters as $k => $v)
//		if ($v != '-all' && $v != '')
//			$query_uri .= ':'.urlencode($k).':'.urlencode($v);
//
//	if ($query_uri == "")
//		$query_uri = ":year:-all";
//
//	header('Location: ?id='.$this->id('tasks').$query_uri);
//}
//
///*rekordy parzyste to nagłówki, nieparzyste to ich wartości.*/
///*np. status:1:type:2:podmiot:PCA*/
//$value = array('issue' => '-all', 'action' => '-all', 'taskstate' => '-all',
//				'executor' => '-all', 'year' => '-all', 'tasktype' => '-all',
//				'month' => '-all', 'task' => '', 'reason' => '', 'date_type' => 'plan');
//for ($i = 0; $i < count($params); $i += 2)
//	$value[urldecode($params[$i])] = urldecode($params[$i+1]);
//
////save filters
//foreach ($value as $k => $v)
//	setcookie("bez_tasks_filters[$k]", $v);
//
//$ical_link = '?id=bez:tasks_ical';
//foreach ($value as $k => $v)
//	if ($v != '-all' && $v != '')
//		$ical_link .= ':'.urlencode($k).':'.urlencode($v);
//
//$template['ical_link'] = $ical_link;
//
//$template['uri'] = $uri;
//
//$template['issues'] = $isso->get_ids();
//
//$template['actions'] = $taskao->get();
//
//$template['states'] = $taskso->get();
//
//$template['executors'] = $usro->get();
//$template['groups'] = $usro->groups();
//
//
//$template['years'] = $tasko->get_years();
//
//$tasks = $tasko->get_filtered($value);
//
//
//$template['tasks_stats']['total'] = count($tasks);
//
//$tcost = 0;
//$thours = 0;
//foreach ($tasks as &$task) {
//	$tcost += (int)$task['cost'];
//	if ($task['start_time'] != '') {
//		$start_time = strtotime($task['start_time']);
//		$finish_time = strtotime($task['finish_time']);
//		$secs = $finish_time - $start_time;
//		$hours = $secs / 3600;
//		$hours_s = sprintf("%.1f", $hours);
//		$task['hours'] = $hours_s;
//		$thours += $hours;
//	} else
//		$task['hours'] = '';
//}
//$template['tasks'] = $tasks;
//
//$template['tasks_stats']['totalcost'] = $tcost;
//$template['tasks_stats']['totalhours'] = sprintf("%.1f", $thours);
//
//$tasktypes = $tasktypeso->get();
//$template['tasktypes'] = $tasktypes;
//
//
//if ($nparams['taskstate'] == '0')
//	$template['view'] = 'plan';
//else
//	$template['view'] = 'realization';
//
//$template['months'] = array(1 => 'jan',
//							2 => 'feb',
//							3 => 'mar',
//							4 => 'apr',
//							5 => 'may',
//							6 => 'june',
//							7 => 'july',
//							8 => 'aug',
//							9 => 'sept',
//							10 => 'oct',
//							11 => 'nov',
//							12 => 'dec');
