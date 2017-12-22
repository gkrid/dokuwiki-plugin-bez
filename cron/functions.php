<?php

class Cron_dummy_action extends DokuWiki_Action_Plugin {
    public function getPluginName() {
        return 'bez';
    }

    public static function id() {
        $args = func_get_args();
        array_unshift($args, 'bez');

        return implode(':', $args);
    }

    public static function url() {
        $args = func_get_args();

        $id = call_user_func_array('Cron_dummy_action::id', $args);
        return DOKU_URL . 'doku.php?id=' . $id;


    }
};

$dummy_action = new Cron_dummy_action();
$model = new \dokuwiki\plugin\bez\mdl\Model($auth, $dw_user, $dummy_action, $conf);

function send_inactive_issue() {
    global $model;
    
    $threads = $model->threadFactory->get_all(array(
        'last_activity_date' => array('<=', date('c', strtotime('-26 days'))),
        'state' => 'opened'
    ));
    
    foreach ($threads as $thread) {
        //send reminder once a month
        $day_of_issue_last_activity = date('d', strtotime($thread->last_activity_date));
        if ($day_of_issue_last_activity == date('d')) {
            //send message to all
            $thread->mail_notify_issue_inactive($thread->get_participants('subscribent'));
        }
    }
}

function send_one_day_task_reminder() {
    global $model;
    
    $tasks = $model->taskFactory->get_all(array(
        'plan_date' => date('Y-m-d', strtotime('+1 day')),
        'state'     => 'opened'
    ));
       
    foreach ($tasks as $task) {
        $task->mail_notify_remind($task->get_participants('subscribent'));
    }
}

//function send_weekly_message($simulate=true) {
//    global $conf, $auth;
//
//    //$helper = new helper_plugin_bez();
//
//   //email => array('user' => array('issues' => array(), 'tasks' => array()))
//    $msg = array();
//    $output = array();
//
//    try {
//        $isso = new Issues();
//        $tasko = new Tasks();
//    } catch (Exception $e) {
//        echo $e->getMessage().': '.$e->getFile();
//    }
//
//    $issues = $isso->cron_get_unsolved();
//
//    foreach ($issues as $issue) {
//        $key = $issue['coordinator'];
//        if (!isset($msg[$key]))
//            $msg[$key] = array('issues' => array(), 'coming_tasks' => array(),
//                                'outdated_tasks' => array());
//
//        $msg[$key]['issues'][] = $issue;
//    }
//
//    $coming_tasks_all  = $tasko->cron_get_coming_tasks();
//
//    foreach ($coming_tasks_all as $task) {
//        $key = $task['executor'];
//        if (!isset($msg[$key]))
//            $msg[$key] = array('issues' => array(), 'coming_tasks' => array(),
//                                'outdated_tasks' => array());
//
//        $msg[$key]['coming_tasks'][] = $task;
//    }
//
//    $outdated_tasks_all  = $tasko->cron_get_outdated_tasks();
//
//    foreach ($outdated_tasks_all as $task) {
//        $key = $task['executor'];
//        if (!isset($msg[$key]))
//            $msg[$key] = array('issues' => array(), 'coming_tasks' => array(),
//                                'outdated_tasks' => array());
//
//        $msg[$key]['outdated_tasks'][] = $task;
//    }
//
//    //outdated_tasks, coming_tasks, open_tasks
//
//
//    foreach ($msg as $user => $data) {
//        $udata = $auth->getUserData($user);
//
//        $his_issues = $data['issues'];
//        $outdated_tasks = $data['outdated_tasks'];
//        $coming_tasks = $data['coming_tasks'];
//
//
//        if (count($his_issues) + count($outdated_tasks) + count($coming_tasks) == 0)
//            continue;
//
//        $to = $udata['name'].' <'.$udata['mail'].'>';
//
//        ob_start();
//        include 'tpl/weekly-message.php';
//        $body = ob_get_clean();
//
//        $mailer = new \dokuwiki\plugin\bez\meta\Mailer();
//        $rep = array();
//        $mailer->setBody('', $rep, NULL, $body, false);
//
//        $mailer->to($to);
//        $subject = 'Nadchodzące zadania';
//        $mailer->subject($subject);
//
//        if ($simulate === false) {
//            $send = $mailer->send();
//        }
//        $output[] = array($to, $subject, $body, array());
//    }
//
//    return $output;
//}
