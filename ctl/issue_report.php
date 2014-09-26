<?php
include DOKU_PLUGIN."bez/models/entities.php";

$ento = new Entities();
$template['entities'] = $ento->get();
$template['issue_types'] = $helper->issue_types();
