<?php

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}

$period = NULL;
if(count($_POST) > 0 && ($_POST['from'] != '' || $_POST['to'] != '')) {
    $from = new DateTime($_POST['from']);
    $to = new DateTime($_POST['to']);

    $this->tpl->set_values(array(
                               'from' => $from->format('Y-m-d'),
                               'to' => $to->format('Y-m-d')));

    $to->modify('+1 day');//add one day extra
    $period = new DatePeriod($from, new DateInterval('P1D'), $to);
}

$this->tpl->set('issues', $this->model->threadFactory->report_issue($period)->fetchAll(PDO::FETCH_ASSOC));
$this->tpl->set('projects', $this->model->threadFactory->report_project($period)->fetchAll(PDO::FETCH_ASSOC));
$this->tpl->set('tasks', $this->model->taskFactory->report($period)->fetchAll(PDO::FETCH_ASSOC));