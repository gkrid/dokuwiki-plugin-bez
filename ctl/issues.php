<?php
include_once DOKU_PLUGIN."bez/models/issues.php";

include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/entities.php";
include_once DOKU_PLUGIN."bez/models/users.php";

if	(!$helper->user_viewer()) {
	$errors[] = $bezlang['error_issues'];
	$controller->preventDefault();
} 

$isso = new Issues();
$stao = new States();
$issto = new Issuetypes();
$ento = new Entities();
$usro = new Users();

if (count($_POST) > 0) {
	$filters = $isso->validate_filters($_POST);

	$query_uri = '';
	foreach ($filters as $k => $v)
		if ($v != '-all')
			$query_uri .= ':'.urlencode($k).':'.urlencode($v);

	header('Location: ?id='.$this->id('issues').$query_uri);
}

/*rekordy parzyste to nagłówki, nieparzyste to ich wartości.*/
/*np. status:1:type:2:podmiot:PCA*/
$value = array('state' => '-all', 'type' => '-all', 'entity' => '-all', 'coordinator' => '-all', 'year' => '-all');
for ($i = 0; $i < count($params); $i += 2)
	$value[urldecode($params[$i])] = urldecode($params[$i+1]);


$template['uri'] = $uri; 

$template['states'] = $stao->get_all();
$template['issue_types'] = $issto->get();
$template['entities'] = $ento->get_list();
$template['coordinators'] = $usro->get();
$template['years'] = $isso->get_years();

$template['issues'] = $isso->get_filtered($value);
