<?php
include_once DOKU_PLUGIN."bez/models/issues.php";

include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/users.php";
include_once DOKU_PLUGIN."bez/models/rootcauses.php";

if	(!$helper->user_viewer()) {
	$errors[] = $bezlang['error_issues'];
	$controller->preventDefault();
} 

$isso = new Issues();
$stao = new States();
$issto = new Issuetypes();
$usro = new Users();
$rootco = new Rootcauses();

if (count($_POST) > 0)
	$raw_filters = $_POST;
elseif (count($params) == 0 && isset($_COOKIE[bez_issues_filters]))
	$raw_filters = $_COOKIE[bez_issues_filters];

if (isset($raw_filters)) {
	$filters = $isso->validate_filters($raw_filters);

	$query_uri = '';
	foreach ($filters as $k => $v)
		if ($v != '-all' && $v != '')
			$query_uri .= ':'.urlencode($k).':'.urlencode($v);
			
	if ($query_uri == '')
		$query_uri = ':year:-all';

	header('Location: ?id='.$this->id('issues').$query_uri);
}

/*rekordy parzyste to nagłówki, nieparzyste to ich wartości.*/
/*np. status:1:type:2:podmiot:PCA*/
$value = array('state' => '-all', 'type' => '-all', 'coordinator' => '-all', 'year' => '-all', 'sort_open' => '',
			'rootcause' => '-all');
for ($i = 0; $i < count($params); $i += 2)
	$value[urldecode($params[$i])] = urldecode($params[$i+1]);
	
//save filters
foreach ($value as $k => $v)
	setcookie("bez_issues_filters[$k]", $v);


$template['uri'] = $uri; 

$template['states'] = $stao->get_list();
$template['issue_types'] = $issto->get();
$template['coordinators'] = $usro->get();
$template['years'] = $isso->get_years();
$template['rootcauses'] = $rootco->get();

$issues = $isso->get_filtered($value);
$template['issues'] = $issues;


$tcost = 0;
foreach ($issues as $issue) {
	$tcost += (int)$issue['cost'];
}
$template['total_cost'] = $tcost;
