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

//setup hostbased
$reflectionClass = new ReflectionClass('DokuWikiFarmCore');
$reflectionProperty = $reflectionClass->getProperty('hostbased');
$reflectionProperty->setAccessible(true);
$reflectionProperty->setValue($FARMCORE, true);

function help() {
    echo "cli.php purge\n";
    exit(0);
}

function purge() {
    $action = new action_plugin_bez_base();
    $action->createObjects(true);

    $threads = $action->get_model()->threadFactory->get_all();
    foreach ($threads as $thread) {
        $thread->purge();
        $action->get_model()->threadFactory->save($thread);
        echo "Thread #" . $thread->id . " purged\n";

    }
    $thread_comments = $action->get_model()->thread_commentFactory->get_all();
    foreach ($thread_comments as $thread_comment) {
        $thread_comment->purge();
        $action->get_model()->thread_commentFactory->save($thread_comment);
        echo "Thread comment #k" . $thread_comment->id . " purged\n";

    }

    $tasks = $action->get_model()->taskFactory->get_all();
    foreach ($tasks as $task) {
        $task->purge();
        $action->get_model()->taskFactory->save($task);
        echo "Task #z" . $task->id . " purged\n";

    }

    $task_comments = $action->get_model()->task_commentFactory->get_all();
    foreach ($task_comments as $task_comment) {
        $task_comment->purge();
        $action->get_model()->task_commentFactory->save($task_comment);
        echo "Task comment #zk" . $task_comment->id . " purged\n";

    }

}


switch ($argv[1]) {
    case 'purge':
        purge();
        exit(0);
    default:
        help();
}