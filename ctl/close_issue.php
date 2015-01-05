<?php
include_once DOKU_PLUGIN."bez/models/issues.php";

$isso = new Issues();
$template['issues'] = $isso->get_close_issue();
