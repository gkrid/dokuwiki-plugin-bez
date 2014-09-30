<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";

if (count($_POST) > 0) {
	include_once DOKU_PLUGIN."bez/models/users.php";
	$usro = new Users();

	$como = new Comments();
	$data = array('reporter' => $usro->get_nick(), 'date' => time());
	$como->add($_POST, $data);

	if (count($errors) > 0)
		$value = $_POST;
	else
		$redirect = $_SERVER[REQUEST_URI].'#comment_'.$como->lastid();
}

$isso = new Issues();

$value = $isso->get($params[0]);

$value['description'] = $helper->wiki_parse($value['description']);

$como = new Comments();
$co = $como->get($value['id']);
$value['comments'] = array();
foreach ($co as $v)
	$value['comments'][] = $helper->wiki_parse($v);

$template['comment_button'] = $bezlang['change_comment_button'];

