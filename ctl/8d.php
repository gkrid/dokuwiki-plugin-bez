<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/tokens.php";

/*jeżeli nie mamy tokenu generujemy nowy i przekierowujemy*/
$toko = new Tokens();
if (!isset($_GET['t']) || ! $toko->check(trim($_GET['t']), $ID))
	header('Location: '.$uri.'?id='.$_GET['id'].'&t='.$toko->get($ID));

$issue_id = $nparams[id];

$isso = new Issues();
$causo = new Causes();
$tasko = new Tasks();

$template['issue'] = $isso->get($issue_id);
$template['team'] = $isso->get_team($issue_id);

$template['causes'] = $causo->get_by_rootcause($issue_id);


$template['tasks'] = $tasko->get_by_8d($issue_id);
$template['cost_total'] = $tasko->get_total_cost($issue_id);

$template['uri'] = $uri.'?'.$_SERVER['QUERY_STRING'];
