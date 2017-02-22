<?php

function log_errors() {
//	global $errors;
//	foreach ($errors as $error) {
//		error_log($error);
//	}
}

function send_message($URI, $http, $conf, $helper, $auth, $bezlang) {
   //email => array('user' => array('issues' => array(), 'tasks' => array()))
    $msg = array();
    $output = array();

    try {
        $isso = new Issues();
        $tasko = new Tasks();
    } catch (Exception $e) {
        error_log($e->getMessage().': '.$e->getFile());
    }

    $issues = $isso->cron_get_unsolved();

    log_errors();
    foreach ($issues as $issue) {
        $key = $issue['coordinator'];
        if (!isset($msg[$key]))
            $msg[$key] = array('issues' => array(), 'coming_tasks' => array(),
                                'outdated_tasks' => array());

        $msg[$key]['issues'][] = $issue;
    }

    $coming_tasks_all  = $tasko->cron_get_coming_tasks();
    log_errors();
    foreach ($coming_tasks_all as $task) {
        $key = $task['executor'];
        if (!isset($msg[$key]))
            $msg[$key] = array('issues' => array(), 'coming_tasks' => array(),
                                'outdated_tasks' => array());

        $msg[$key]['coming_tasks'][] = $task;
    }

    $outdated_tasks_all  = $tasko->cron_get_outdated_tasks();
    log_errors();
    foreach ($outdated_tasks_all as $task) {
        $key = $task['executor'];
        if (!isset($msg[$key]))
            $msg[$key] = array('issues' => array(), 'coming_tasks' => array(),
                                'outdated_tasks' => array());

        $msg[$key]['outdated_tasks'][] = $task;
    }

    //outdated_tasks, coming_tasks, open_tasks

    
    foreach ($msg as $user => $data) {
        $udata = $auth->getUserData($user);

        $his_issues = $data['issues'];
        $outdated_tasks = $data['outdated_tasks'];
        $coming_tasks = $data['coming_tasks'];


        if (count($his_issues) + count($outdated_tasks) + count($coming_tasks) == 0)
            continue;

        $to = $udata['name'].' <'.$udata['mail'].'>';
        $title = trim($conf['title']);
        if ($title == '')
            $title = $URI;
        $subject = "[BEZ][$title] Termin rozwiązania problemu";
        ob_start();

        include "cron-message-tpl.php";
        $body = ob_get_clean();

        $helper->mail($to, $subject, $body, $URI, "text/html");
        $output[] = 'mail send to: '. $to;
    } 
    
    return $output;
}
