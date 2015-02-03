<?php
include_once DOKU_PLUGIN."bez/models/report.php";
include_once DOKU_PLUGIN."bez/models/entities.php";

$repo = new Report();
$ento = new Entities();

$template['entities'] = $ento->get_list();

$template['report'] = $repo->report();
