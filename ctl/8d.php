<?php

//if we don't have a token, generate a new one and redirect
if (!isset($_GET['t'])) {
    $token = $this->model->authentication_tokenFactory->get_token($this->id());
    header('Location: ' . $this->url() . '&t=' . $token);
}

/** @var bez\mdl\Thread $thread */
$thread = $this->model->threadFactory->get_one($this->get_param('id'));
$this->tpl->set('thread', $thread);
$this->tpl->set('causes_real',
        $this->model->thread_commentFactory->get_from_thread($thread, array('type' => 'cause_real'))->fetchAll());
$this->tpl->set('causes_potential',
    $this->model->thread_commentFactory->get_from_thread($thread, array('type' => 'cause_potential'))->fetchAll());
$tasks = $this->model->taskFactory->get_by_type($thread);
$this->tpl->set('8d_tasks', $tasks);


/*jeżeli nie mamy tokenu generujemy nowy i przekierowujemy*/
//$toko = new Tokens();
//if ($this->model->acl->get_level() >= BEZ_AUTH_USER &&
//    (!isset($_GET['t']) || ! $toko->check(trim($_GET['t']), $this->page_id()))) {
//	header('Location: '.$uri.'?id='.$_GET['id'].'&t='.$toko->get($this->page_id()));
//}
//
//$issue_id = $nparams['id'];
//
////$isso = new Issues();
////$causo = new Causes();
////$tasko = new Tasks();
////
////$template['issue'] = $isso->get($issue_id);
////$template['team'] = $isso->get_team($issue_id);
////
////$template['real_causes'] = $causo->get_real($issue_id);
////$template['potential_causes'] = $causo->get_potential($issue_id);
////
////
////$template['tasks'] = $tasko->get_by_8d($issue_id);
////$template['cost_total'] = $tasko->get_total_cost($issue_id);
//
//$template['issue'] = $this->model->issues->get_one($issue_id);
//$template['total_cost'] = $template['issue']->total_cost();
//
//$template['real_causes'] = $this->model->commcauses->get_all(array(
//    'type'  => '1',
//    'issue' => $issue_id
//))->fetchAll(); //fetchAll becouse we need to count rows before displaying them
//
//$template['potential_causes'] = $this->model->commcauses->get_all(array(
//    'type'  => '2',
//    'issue' => $issue_id
//))->fetchAll(); //fetchAll becouse we need to count rows before displaying them
//
//$template['tasks'] = array();
//$template['tasks']['3d'] =  $this->model->tasks->get_all(array(
//    'action'  => '0',
//    'state' => array('!=', '2'),
//    'issue' => $issue_id
//))->fetchAll(); //fetchAll becouse we need to count rows before displaying them
//
//$template['tasks']['5d'] =  $this->model->tasks->get_all(array(
//    'action'  => '1',
//    'state' => array('!=', '2'),
//    'issue' => $issue_id
//))->fetchAll(); //fetchAll becouse we need to count rows before displaying them
//
//
//$template['tasks']['7d'] =  $this->model->tasks->get_all(array(
//    'action'  => '2',
//    'state' => array('!=', '2'),
//    'issue' => $issue_id
//))->fetchAll(); //fetchAll becouse we need to count rows before displaying them
//
//$template['uri'] = $uri.'?'.$_SERVER['QUERY_STRING'];
