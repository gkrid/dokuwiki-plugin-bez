<?php
include_once DOKU_PLUGIN."bez/models/report.php";
include_once DOKU_PLUGIN."bez/models/entities.php";

/*jeżeli nie mamy tokenu generujemy nowy i przekierowujemy*/
$toko = new Tokens();
if (!isset($_GET['t']) || ! $toko->check(trim($_GET['t']), $ID))
	header('Location: '.$uri.'?'.$_SERVER['QUERY_STRING'].'&t='.$toko->get($ID));

$repo = new Report();
$ento = new Entities();

$value = array('entity' => '-all', 'year' => '-all', 'month' => '-all');
if (count($_POST) > 0) {
	$filters = $repo->validate_filters($_POST);

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

$template['uri'] = $uri;
$template['hidden'] = array();
if (isset($value['year']))
	$template['hidden']['year'] = $value['year'];
if (isset($value['month']))
	$template['hidden']['month'] = $value['month'];

$template['report'] = $repo->report($value);

$template['title'] = $bezlang['report'].($value['year'] != '-all' ? ' '.$value['year'] : '').
					($value['month'] != '-all' ? '/'.($value['month'] > 10 ? $value['month'] : '0'.$value['month']) : '');

$template['uri'] = $uri.'?'.$_SERVER['QUERY_STRING'];
