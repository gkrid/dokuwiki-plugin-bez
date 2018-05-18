<?php

//include base config
$inc = realpath(__DIR__.'/../../..');
define('DOKU_INC', $inc.'/');

include DOKU_INC . 'conf/local.php';

if (!isset($conf['plugin']['bez']['url'])) {
    echo "set the plugin wiki URL in bez config\n";
    exit(1);
}

$url_p = parse_url($conf['plugin']['bez']['url']);
if (!isset($url_p['scheme'])) {
    $_SERVER['HTTPS'] = 'on';
    
    $ex = explode('/', $url_p['path'], 2);
    
    $_SERVER['SERVER_NAME'] = $ex[0];
    if (isset($ex[1])) {
        $_SERVER['DOCUMENT_ROOT'] = $ex[1] . '/';
    } else {
        $_SERVER['DOCUMENT_ROOT'] = '/';
    }
    
} else {
    if ($url_p['scheme'] === 'https') {
        $_SERVER['HTTPS'] = 'on';
    } else {
        $_SERVER['HTTPS'] = 'off';
    }
    $_SERVER['SERVER_NAME'] = $url_p['host'];
    $_SERVER['DOCUMENT_ROOT'] = $url_p['path'] . '/';
}

//in case of $conf['basedir'] is empty
$_SERVER['SCRIPT_NAME'] = $_SERVER['DOCUMENT_ROOT'].'doku.php';

// load and initialize the core system
require_once(DOKU_INC.'inc/init.php');

$INFO = array();
$INFO['client'] = false;
require_once 'cron/functions.php';

if (date('l') === $conf['plugin']['bez']['weekly_cron_day_of_the_week']) {
    send_weekly_message();
}

send_one_day_task_reminder();

send_inactive_issue();
