<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/tokens.php";

/*jeżeli nie mamy tokenu generujemy nowy i przekierowujemy*/
$toko = new Tokens();
if ($this->model->acl->get_level() >= BEZ_AUTH_USER &&
    (!isset($_GET['t']) || ! $toko->check(trim($_GET['t']), $this->page_id()))) {
	header('Location: '.$uri.'?id='.$_GET['id'].'&t='.$toko->get($this->page_id()));
}

$issue_id = $nparams['id'];

//$isso = new Issues();
//$causo = new Causes();
//$tasko = new Tasks();
//
//$template['issue'] = $isso->get($issue_id);
//$template['team'] = $isso->get_team($issue_id);
//
//$template['real_causes'] = $causo->get_real($issue_id);
//$template['potential_causes'] = $causo->get_potential($issue_id);
//
//
//$template['tasks'] = $tasko->get_by_8d($issue_id);
//$template['cost_total'] = $tasko->get_total_cost($issue_id);
       
$template['issue'] = $this->model->issues->get_one($issue_id);
$template['total_cost'] = $template['issue']->total_cost();

$template['real_causes'] = $this->model->commcauses->get_all(array(
    'type'  => '1',
    'issue' => $issue_id
))->fetchAll(); //fetchAll becouse we need to count rows before displaying them

$template['potential_causes'] = $this->model->commcauses->get_all(array(
    'type'  => '2',
    'issue' => $issue_id
))->fetchAll(); //fetchAll becouse we need to count rows before displaying them

$template['tasks'] = array();
$template['tasks']['3d'] =  $this->model->tasks->get_all(array(
    'action'  => '0',
    'state' => array('!=', '2'),
    'issue' => $issue_id
))->fetchAll(); //fetchAll becouse we need to count rows before displaying them

$template['tasks']['5d'] =  $this->model->tasks->get_all(array(
    'action'  => '1',
    'state' => array('!=', '2'),
    'issue' => $issue_id
))->fetchAll(); //fetchAll becouse we need to count rows before displaying them


$template['tasks']['7d'] =  $this->model->tasks->get_all(array(
    'action'  => '2',
    'state' => array('!=', '2'),
    'issue' => $issue_id
))->fetchAll(); //fetchAll becouse we need to count rows before displaying them

$template['uri'] = $uri.'?'.$_SERVER['QUERY_STRING'];
