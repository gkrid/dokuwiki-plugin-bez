<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/tokens.php";

/*jeżeli nie mamy tokenu generujemy nowy i przekierowujemy*/
$toko = new Tokens();
if (!isset($_GET['token']) || ! $toko->check(trim($_GET['token']), $ID))
	header('Location: '.$uri.'?'.$_SERVER['QUERY_STRING'].'&token='.$toko->get($ID));

$issue_id = $params[0];

$isso = new Issues();
$causo = new Causes();
$tasko = new Tasks();

$template['issue'] = $isso->get($issue_id);

$template['causes'] = $causo->get_by_rootcause($issue_id);

$template['tasks'] = $tasko->get_by_8d($issue_id);
$template['cost_total'] = $tasko->get_total_cost($issue_id);

$template['uri'] = $uri.'?'.$_SERVER['QUERY_STRING'];
