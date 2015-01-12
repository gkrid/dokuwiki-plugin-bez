<?php
include_once DOKU_PLUGIN."bez/models/issues.php";

include_once DOKU_PLUGIN."bez/models/states.php";
include_once DOKU_PLUGIN."bez/models/issuetypes.php";
include_once DOKU_PLUGIN."bez/models/entities.php";

if	(!$helper->user_viewer()) {
	$errors[] = $bezlang['error_issues'];
	$controller->preventDefault();
} 

$isso = new Issues();
$stao = new States();
$issto = new Issuetypes();
$ento = new Entities();

if (count($_POST) > 0) {
}

/*rekordy parzyste to nagłówki, nieparzyste to ich wartości.*/
/*np. status:1:type:2:podmiot:PCA*/
$value = array('state' => '-all', 'type' => '-all', 'entity' => '-all', 'year' => date('Y'));
for ($i = 0; $i < count($params); $i += 2)
	$value[$params[$i]] = $params[$i+1];



$template['states'] = $stao->get_all();
$template['issue_types'] = $issto->get();
$template['entities'] = $ento->get_list();
$template['years'] = $isso->get_years();
