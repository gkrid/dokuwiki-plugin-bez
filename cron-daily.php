<?php

//include base config
$inc = realpath(__DIR__.'/../../..');
define('DOKU_INC', $inc.'/');

// load and initialize the core system
require_once(DOKU_INC.'inc/init.php');

if (empty($conf['baseurl'])) {
    echo "set baseurl in dokuwiki config: ${conf['basedir']}\n";
    exit(-1);
}

require_once 'cron/functions.php';

$errors = [];
try {
    if (date('l') === $conf['plugin']['bez']['weekly_cron_day_of_the_week']) {
        send_weekly_message();
    }

    send_task_reminder();
    send_inactive_issue();
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

if ($errors) {
    echo $conf['baseurl'] . ":\n";
    echo implode("\n", $errors);
    echo "\n";
    exit(-1);
}

