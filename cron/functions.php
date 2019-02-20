<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once DOKU_PLUGIN . 'bez/vendor/phpmailer/phpmailer/src/Exception.php';
require_once DOKU_PLUGIN . 'bez/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once DOKU_PLUGIN . 'bez/vendor/phpmailer/phpmailer/src/SMTP.php';

$action = new action_plugin_bez_base();
$action->createObjects(true);

function send_inactive_issue() {
    global $action;
    
    $threads = $action->get_model()->threadFactory->get_all(array(
        'last_activity_date' => array('<=', date('c', strtotime('-26 days'))),
        'state' => 'opened'
    ));
    
    foreach ($threads as $thread) {
        //send reminder once a month
        $day_of_issue_last_activity = date('d', strtotime($thread->last_activity_date));
        if ($day_of_issue_last_activity == date('d')) {
            //send message to all
            $thread->mail_notify_inactive($thread->get_participants('subscribent'));
        }
    }
}

function send_task_reminder() {
    global $action;

    $filters = array('state' => 'opened', 'plan_date' => array('OR', array()));

    $days_before = $action->getConf('task_remaind_days_before');
    $days_before = array_map('trim', explode(',', $days_before));

    if (count($days_before) == 0) {
        return;
    }

    foreach($days_before as $day) {
        $filters['plan_date'][1][] = date('Y-m-d', strtotime("+$day day"));
    }

    
    $tasks = $action->get_model()->taskFactory->get_all($filters);

    $now = new DateTime(date('Y-m-d'));
    foreach ($tasks as $task) {
        $plan_date = new DateTime($task->plan_date);
        $task->mail_notify_remind($task->get_participants('subscribent'), $plan_date->diff($now)->format('%a'));
    }
}

function send_weekly_message() {
    global $action;
    global $auth;
    global $conf;

    //email => array('user' => array('issues' => array(), 'tasks' => array()))
    $msg = array();
    $output = array();

    $threads = $action->get_model()->threadFactory->get_all(array(
          'type' => 'issue',
          'priority' => array('OR', array('2', '1'))
      ));

    foreach ($threads as $thread) {
        $key = $thread->coordinator;
        if (!isset($msg[$key])) {
            $msg[$key] = array(
                'issues'          => array(),
                'coming_tasks'    => array(),
                'outdated_tasks'  => array()
            );
        }
        $msg[$key]['issues'][] = $thread;
    }

    $tasks  = $action->get_model()->taskFactory->get_all(array(
        'priority' => array('OR', array('2', '1'))
    ));

    foreach ($tasks as $task) {
        $key = $task->assignee;
        if (!isset($msg[$key])) {
            $msg[$key] = array(
                'issues'          => array(),
                'coming_tasks'    => array(),
                'outdated_tasks'  => array()
            );
        }

        if ($task->priority == '1') {
            $msg[$key]['coming_tasks'][] = $task;
        } else {
            $msg[$key]['outdated_tasks'][] = $task;
        }
    }

    //outdated_tasks, coming_tasks, open_tasks


    foreach ($msg as $user => $data) {
        $udata = $auth->getUserData($user);
        if (!$udata) continue;

        $issues = $data['issues'];
        $outdated_tasks = $data['outdated_tasks'];
        $coming_tasks = $data['coming_tasks'];


        if (count($issues) + count($outdated_tasks) + count($coming_tasks) == 0)
            continue;

        $tpl = $action->get_tpl();
        $tpl->set('issues', $issues);
        $tpl->set('outdated_tasks', $outdated_tasks);
        $tpl->set('coming_tasks', $coming_tasks);
        $body = $action->bez_tpl_include('cron/weekly-message', true);

        $mailer = new PHPMailer;
        $mailer->CharSet = 'utf-8';
        $mailer->isHTML(true);

        $mailer->setFrom($conf['mailfrom']);
        $mailer->addReplyTo($conf['mailfrom']);

        $token = $action->get_model()->factory('subscription')->getUserToken($user);
        $resign_link = $action->url('unsubscribe', array( 't' => $token));
        $oneClickUnsubscribe = $action->url('unsubscribe', array( 't' => $token, 'oneclick' => '1'));
        $mailer->AddCustomHeader("List-Unsubscribe: <$oneClickUnsubscribe>");
        $mailer->Body = str_replace('%%resign_link%%', $resign_link, $body);

        $mailer->addAddress($udata['mail'], $udata['name']);
        $subject = $conf['title'] . ' Nadchodzące zadania';
        $mailer->Subject = $subject;

        $mailer->send();
        $mailer->clearAddresses();
        $mailer->clearCustomHeaders();
        $output[] = array($udata['name'].' <'.$udata['mail'].'>', $subject, $body, array());
    }

    return $output;
}
