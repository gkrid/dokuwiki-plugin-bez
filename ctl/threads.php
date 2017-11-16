<?php
/** @var action_plugin_bez $this */

define('BEZ_THREAD_FILTERS_COOKIE_NAME', 'bez_thread_filters');

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

	header('Location: '.$this->url('threads', $filters));
} else {
    $filters = $this->params;
}

$this->tpl->set_values($filters);

//$issuetypes = $this->model->issuetypes->get_all();
$years = $this->model->threadFactory->get_years_scope();

//some filters are just copied
$db_filters = array_filter($filters, function ($k) {
    return in_array($k, array('state', 'label', 'coordinator'));
}, ARRAY_FILTER_USE_KEY);

//-none filters become empty filters
$db_filters = array_map(function($v) {
    if ($v === '-none') {
        return NULL;
    }
    return $v;
}, $db_filters);

if (isset($filters['year']) && $filters['year'] !== '-all') {
    $year = $filters['year'];
    
    $start_day = "$year-01-01";
    $end_day = "$year-12-31";
    
    $db_filters['create_date'] = array('BETWEEN', array($start_day, $end_day), array('date'));
}

if (isset($filters['coordinator']) &&
        substr($filters['coordinator'], 0, 1) === '@') {
    $group = substr($filters['coordinator'], 1);
    $db_filters['coordinator'] = array('OR', $this->model->userFactory->users_of_group($group));
}

if (isset($filters['title'])) {
    $title = preg_replace('/\s/', '%', $filters['title']);
    $db_filters['title'] = array('LIKE', "%$title%");
}

$threads = $this->model->threadFactory->get_all($db_filters);

//$this->tpl->set('issuetypes', $issuetypes);
$this->tpl->set('threads', $threads);
$this->tpl->set('years', $years);
