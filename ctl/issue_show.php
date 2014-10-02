<?php
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/comments.php";

$como = new Comments();

if ($params[1] == 'edit_comment')
	$value['content'] = $como->getcontent($params[2]);


if (isset($_POST['event'])) 
switch ($_POST['event']) {
	case 'comment':
		include_once DOKU_PLUGIN."bez/models/users.php";
		$usro = new Users();

		$como = new Comments();
		$data = array('reporter' => $usro->get_nick(), 'date' => time(), 'issue' => $params[0]);

		if ($params[1] == 'edit_comment')
			$como->update($_POST, $data, $params[2]);
		else 
			$como->add($_POST, $data);

		if (count($errors) > 0)
			$value = $_POST;
		else
			$redirect = '?id=bez:issue_show:'.$params[0].'#bez_comment_'.$como->lastid();

	break;
}

$isso = new Issues();

$template['issue'] = $isso->get($params[0]);

$template['issue']['description'] = $helper->wiki_parse($template['issue']['description']);

$co = $como->get($params[0]);

foreach ($co as &$v)
	$v['content'] = $helper->wiki_parse($v['content']);

$template['comments'] = $co;

if ($params[1] == 'edit_comment')
	$template['comment_button'] = $bezlang['change_comment_button'];
else
	$template['comment_button'] = $bezlang['add'];

$template['closed'] = false;


