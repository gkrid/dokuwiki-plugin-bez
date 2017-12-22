<?php

function usage() {
    echo "usage: cron-daily.php url user\n";
    exit(1);
}

if (count($argv) < 3) {
    usage();
}

$url = $argv[1];

$dw_user = $argv[2];

$url_p = parse_url($url);

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

$inc = realpath(__DIR__.'/../../..');
define('DOKU_INC', $inc.'/');

// load and initialize the core system
require_once(DOKU_INC.'inc/init.php');
require_once 'cron/functions.php';

if (date('l') === $conf['plugin']['bez']['weekly_cron_day_of_the_week']) {
    //send_weekly_message(false);
}

send_one_day_task_reminder();

send_inactive_issue();
