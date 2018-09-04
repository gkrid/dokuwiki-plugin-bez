<?php

//include base config
$inc = realpath(__DIR__.'/../../..');
define('DOKU_INC', $inc.'/');

// load and initialize the core system
require_once(DOKU_INC.'inc/init.php');

if (empty($conf['baseurl']) || empty($conf['basedir'])) {
    echo "set baseurl and basedir in dokuwiki config\n";
    exit(-1);
}

$dryrun = false;
if ($argv[1] == 'dryrun') {
    $dryrun = true;
}

require_once 'cron/functions.php';

if ($dryrun || date('l') === $conf['plugin']['bez']['weekly_cron_day_of_the_week']) {
    send_weekly_message();
}

send_task_reminder();

send_inactive_issue();
