<?php
//include_once DOKU_PLUGIN."bez/models/issues.php";
//include_once DOKU_PLUGIN."bez/models/comments.php";
//include_once DOKU_PLUGIN."bez/models/causes.php";
//include_once DOKU_PLUGIN."bez/models/tasks.php";
//
///*w celach importu*/
//include_once DOKU_PLUGIN."bez/models/issuetypes.php";
//new Issuetypes();
//
//$isso = new Issues();
//$tasko= new Tasks();
//$causo = new Causes();
//$commo = new Comments();
//
////pobierz rekordy z ostatniego miesiąca
//$issues = $isso->get_by_days(31);
//$tasks = $tasko->get_by_days(31);
//$causes = $causo->get_by_days(31);
//$comments = $commo->get_by_days(31);
//
//$timeline = $helper->days_array_merge($issues, $tasks, $comments, $causes);
//
//$template['timeline'] = $timeline;
//
//$isso = new Issues();
//$no = count($isso->get_filtered(array('state' => '0', 'coordinator' => $INFO['client'])));
//$template['my_issues'] = $no;
//
//$tasko = new Tasks();
//$no = count($tasko->get_filtered(array('taskstate' => '0', 'executor' => $INFO['client'])));
//$template['my_tasks'] = $no;
//
//$no = count($isso->get_filtered( array('state' => '-proposal') ));
//$template['proposals'] = $no;
//
//$template['client'] = $INFO['client'];

$my_tasks_conut = $this->model->tasks->count(array(
                                'state' => '0',
                                'executor' => $this->model->user_nick
                            ));
                            
$my_issues_count = $this->model->issues->count(array(
                                'state' => '0',
                                'coordinator' => $this->model->user_nick
                            )); 
                            
$proposals_count = $this->model->issues->count(array(
                                'state' => '0',
                                'coordinator' => '-proposal'
                            ));                                           

$this->tpl->set('timeline', $this->model->timeline->get_all(30));
$this->tpl->set('my_tasks_conut', $my_tasks_conut);
$this->tpl->set('my_issues_conut', $my_issues_count);
$this->tpl->set('proposals_count', $proposals_count);


//create database
//~ $this->model;

//~ include_once DOKU_PLUGIN."bez/models/issues.php";
//~ $isso = new Issues();
//~ $no = count($isso->get_filtered(array('state' => '0', 'coordinator' => $INFO['client'])));
//~ $template['my_issues'] = $no;

//~ $no = count($isso->get_filtered( array('state' => '-proposal') ));
//~ $template['proposals'] = $no;


