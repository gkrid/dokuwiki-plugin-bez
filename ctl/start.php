<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";

$isso = new Issues();
$tasko= new Tasks();

$issues = $isso->get_by_days();
$tasks = $tasko->get_by_days();

$timeline = $helper->days_array_merge($issues, $tasks);

$template['timeline'] = $timeline;

$isso = new Issues();
$no = count($isso->get_filtered(array('state' => '0', 'coordinator' => $INFO['client'])));
$template['my_issues'] = $no;

$tasko = new Tasks();
$no = count($tasko->get_filtered(array('state' => '0', 'coordinator' => $INFO['client'])));
$template['my_tasks'] = $no;

$no = count($isso->get_filtered( array('state' => '-proposal') ));
$template['proposals'] = $no;

$template['client'] = $INFO['client'];
