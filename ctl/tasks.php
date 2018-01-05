<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}

define('BEZ_THREAD_FILTERS_COOKIE_NAME', 'bez_task_filters');

if (count($_POST) > 0) {
    $raw_filters = $_POST;
} elseif (empty($this->params) && isset($_COOKIE[BEZ_THREAD_FILTERS_COOKIE_NAME])) {
    $raw_filters = $_COOKIE[BEZ_THREAD_FILTERS_COOKIE_NAME];
}

if (isset($raw_filters)) {
    //save filters
    foreach ($raw_filters as $k => $v) {
        setcookie(BEZ_THREAD_FILTERS_COOKIE_NAME."[$k]", $v);
    }

    $filters = array_filter($raw_filters, function($v) {
        return $v !== '-all' && $v !== '';
    });

    if (empty($filters)) {
        $filters['year'] = '-all';
    }

    header('Location: '.$this->url('tasks', $filters));
} else {
    $filters = $this->params;
}

$this->tpl->set_values($filters);

$years = $this->model->taskFactory->get_years_scope();

//some filters are just copied
$db_filters = array_filter($filters, function ($k) {
    return in_array($k, array('thread_id', 'state', 'type', 'assignee', 'original_poster', 'task_program_id'));
}, ARRAY_FILTER_USE_KEY);

//-none filters become empty filters
$db_filters = array_map(function($v) {
    if ($v === '-none') {
        return '';
    }
    return $v;
}, $db_filters);

if (isset($filters['year']) && $filters['year'] !== '-all') {
    $year = $filters['year'];

    $start_month = '01';
    $end_month = '12';
    if (isset($filters['month']) && $filters['month'] !== '-all') {
        $start_month = $end_month = sprintf("%02d", (int)$filters['month']);
    }

    $start_day = "$year-$start_month-01";
    $end_day = "$year-$end_month-31";

    $db_filters[$filters['date_type']] = array('BETWEEN', array($start_day, $end_day), array('date'));
}

if (isset($filters['original_poster']) &&
    substr($filters['original_poster'], 0, 1) === '@') {
    $group = substr($filters['original_poster'], 1);
    $db_filters['original_poster'] = array('OR', $this->model->userFactory->users_of_group($group));
}

if (isset($filters['assignee']) &&
    substr($filters['assignee'], 0, 1) === '@') {
    $group = substr($filters['assignee'], 1);
    $db_filters['assignee'] = array('OR', $this->model->userFactory->users_of_group($group));
}

if (isset($filters['content'])) {
    $content = preg_replace('/\s/', '%', $filters['content']);
    $db_filters['content'] = array('LIKE', "%$content%");
}

$orderby = 'id';

$tasks = $this->model->taskFactory->get_all($db_filters, $orderby);

$this->tpl->set('task_programs', $this->model->task_programFactory->get_all());
$this->tpl->set('tasks', $tasks);
$this->tpl->set('months', array(1 => 'jan',
                                2 => 'feb',
                                3 => 'mar',
                                4 => 'apr',
                                5 => 'may',
                                6 => 'june',
                                7 => 'july',
                                8 => 'aug',
                                9 => 'sept',
                                10 => 'oct',
                                11 => 'nov',
                                12 => 'dec'));
$this->tpl->set('years', $years);
