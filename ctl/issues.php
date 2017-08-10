<?php

define('BEZ_ISSUES_FILTERS_COOKIE_NAME', 'bez_issues_filters');

if (count($_POST) > 0) {
	$raw_filters = $_POST;
} elseif (empty($this->params) && isset($_COOKIE[BEZ_ISSUES_FILTERS_COOKIE_NAME])) {
	$raw_filters = $_COOKIE[BEZ_ISSUES_FILTERS_COOKIE_NAME];
}

if (isset($raw_filters)) {
    
    $filters = array_filter($raw_filters, function($v) {
       return $v !== '-all' && $v !== '';
    });
    
    if (empty($filters)) {
        $filters['year'] = '-all';
    }
    
	header('Location: '.$this->url('issues', $filters));
} else {
    $filters = $this->params;
}

$this->tpl->set_values($filters);

//save filters
foreach ($filters as $k => $v) {
	setcookie(BEZ_ISSUES_FILTERS_COOKIE_NAME."[$k]", $v);
}

$issuetypes = $this->model->issuetypes->get_all();
$years = $this->model->issues->get_years_scope();

//some filters are just copied
$db_filters = array_filter($filters, function ($k) {
    return in_array($k, array('full_state'));
}, ARRAY_FILTER_USE_KEY);

if (isset($filters['year']) && $filters['year'] !== '-all') {
    $year = $filters['year'];
    
    $start_day = "$year-01-01";
    $end_day = "$year-12-31";
    
    $db_filters['date'] = array('BETWEEN', array($start_day, $end_day), array('date', "'unixepoch'"));
}

$issues = $this->model->issues->get_all($db_filters);


$this->tpl->set('issuetypes', $issuetypes);
$this->tpl->set('issues', $issues);
$this->tpl->set('years', $years);
