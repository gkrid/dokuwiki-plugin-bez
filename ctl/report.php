<?php
include_once DOKU_PLUGIN."bez/models/report.php";
include_once DOKU_PLUGIN."bez/models/entities.php";

if	(!$helper->user_viewer()) {
	$errors[] = $bezlang['error_issues'];
	$controller->preventDefault();
} 

$repo = new Report();
$ento = new Entities();

$value = array('entity' => '-all', 'year' => '-all', 'month' => '-all');
if (count($_POST) > 0) {
	$post = $_POST;
	for ($i = 0; $i < count($params); $i += 2) {
		$key = urldecode($params[$i]);
		if (array_key_exists($key, $value) && !array_key_exists($post))
			$post[$key] = urldecode($params[$i+1]);
	}

	$filters = $repo->validate_filters($post);

	$query_uri = '';
	foreach ($filters as $k => $v)
		if ($v != '-all')
			$query_uri .= ':'.urlencode($k).':'.urlencode($v);


	header('Location: ?id=bez:report'.$query_uri);
}

/*rekordy parzyste to nagłówki, nieparzyste to ich wartości.*/
/*np. status:1:type:2:podmiot:PCA*/
for ($i = 0; $i < count($params); $i += 2)
	$value[urldecode($params[$i])] = urldecode($params[$i+1]);

$template['entities'] = $ento->get_list();

$template['report'] = $repo->report($value);
