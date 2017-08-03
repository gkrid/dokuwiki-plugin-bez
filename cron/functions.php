<?php

require_once DOKU_PLUGIN.'bez/helper.php';

require_once DOKU_PLUGIN.'bez/exceptions.php';
require_once DOKU_PLUGIN.'bez/interfaces.php';

require_once DOKU_PLUGIN.'bez/mdl/model.php';

class Cron_dummy_action extends DokuWiki_Action_Plugin {
    public function getPluginName() {
        return 'bez';
    }
    
    public function id() {
        $args = func_get_args();
        array_unshift($args, 'bez');
        
        return implode(':', $args);
    }
};

$dummy_action = new Cron_dummy_action();
$model = new BEZ_mdl_Model($auth, $dw_user, $dummy_action, $conf);

function send_inactive_issue() {
    global $model;
    
    $issues = $model->issues->get_all(array(
        'last_activity' => array('<=',
            date('Y-m-d', strtotime('-30 days')), 'date'
        ),
        'state' => '0'
    ));
    
    foreach ($issues as $issue) {
        //send reminder once a month
        $day_of_issue_last_activity = date('d', strtotime($issue->last_activity));
        if ($day_of_issue_last_activity === date('d')) {
            //send message to all
            $issue->mail_notify_issue_inactive($issue->get_subscribents());
        }
    }
}

function send_one_day_task_reminder() {
    global $model;
    
    $tasks = $model->tasks->get_all(array(
        'plan_date' => date('Y-m-d', strtotime('+1 day')),
        'state'     => '0' //only open tasks
    ));
       
    foreach ($tasks as $task) {
        $task->mail_notify_remind($task->get_subscribents());
    }
}

function send_weekly_message($simulate=true) {
    global $conf, $auth, $bezlang;
    
    $helper = new helper_plugin_bez();
    
   //email => array('user' => array('issues' => array(), 'tasks' => array()))
    $msg = array();
    $output = array();

    try {
        $isso = new Issues();
        $tasko = new Tasks();
    } catch (Exception $e) {
        echo $e->getMessage().': '.$e->getFile();
    }

    $issues = $isso->cron_get_unsolved();
    
    foreach ($issues as $issue) {
        $key = $issue['coordinator'];
        if (!isset($msg[$key]))
            $msg[$key] = array('issues' => array(), 'coming_tasks' => array(),
                                'outdated_tasks' => array());

        $msg[$key]['issues'][] = $issue;
    }

    $coming_tasks_all  = $tasko->cron_get_coming_tasks();

    foreach ($coming_tasks_all as $task) {
        $key = $task['executor'];
        if (!isset($msg[$key]))
            $msg[$key] = array('issues' => array(), 'coming_tasks' => array(),
                                'outdated_tasks' => array());

        $msg[$key]['coming_tasks'][] = $task;
    }

    $outdated_tasks_all  = $tasko->cron_get_outdated_tasks();

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

        ob_start();
        include 'tpl/weekly-message.php';
        $body = ob_get_clean();
        
        $mailer = new BEZ_Mailer();
        $rep = array();
        $mailer->setBody('', $rep, NULL, $body, false);

        $mailer->to($to);
        $subject = 'Nadchodzące zadania';
        $mailer->subject($subject);
        
        if ($simulate === false) {
            $send = $mailer->send();
        }
        $output[] = array($to, $subject, $body, array());
    } 
    
    return $output;
}
