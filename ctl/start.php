<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}
$orderby = array('sort', 'priority DESC', 'create_date DESC');
$filter = array('state' => 'proposal');
$proposals = $this->model->threadFactory->get_all($filter, $orderby);
$this->tpl->set('proposals', $proposals);
$this->tpl->set('proposals_count', $this->model->threadFactory->count($filter));

$orderby = array('sort', 'priority DESC', 'create_date DESC');
$filter = array('state' => 'opened', 'coordinator' => $this->model->user_nick);
$my_threads = $this->model->threadFactory->get_all($filter, $orderby);
$this->tpl->set('my_threads', $my_threads);
$this->tpl->set('my_threads_count', $this->model->threadFactory->count($filter));

$orderby = array('priority DESC', 'plan_date');
$filter = array('state' => 'opened', 'assignee' => $this->model->user_nick);
$my_tasks = $this->model->taskFactory->get_all($filter, $orderby);
$this->tpl->set('my_tasks', $my_tasks);
$this->tpl->set('my_tasks_count', $this->model->taskFactory->count($filter));

$orderby = array('sort', 'priority DESC', 'create_date DESC');
$filter = array('state' => 'opened', 'original_poster' => $this->model->user_nick);
$reported_threads = $this->model->threadFactory->get_all($filter, $orderby);
$this->tpl->set('reported_threads', $reported_threads);
$this->tpl->set('reported_threads_count', $this->model->threadFactory->count($filter));

$orderby = array('priority DESC', 'plan_date');
$filter = array('state' => 'opened', 'original_poster' => $this->model->user_nick);
$reported_tasks = $this->model->taskFactory->get_all($filter, $orderby);
$this->tpl->set('reported_tasks', $reported_tasks);
$this->tpl->set('reported_tasks_count', $this->model->taskFactory->count($filter));

