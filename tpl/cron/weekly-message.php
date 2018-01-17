<!DOCTYPE html>
<html>
<head>
<style type="text/css">
	body {
		font-family: Arial, sans-serif;
	}
	table {
		border-collapse:collapse;
	}
	td, th {
		border: 1px solid #000;
		padding: 2px;
	}
	th {
		text-align: left;
		background-color: #EEE;
	}
	a {
		text-decoration: none;
		color: #2B73B7;
	}
	a:hover {
		text-decoration: underline;
	}
	h1 {
		font-size: 105%;
	}
</style>
</head>
<body>
<?php if (count($tpl->get('issues')) > 0): ?>
<h1>Twoje niezgodności:</h1>
<table>
<tr>
	<th>Nr</th>
	<th>Typ problemu</th>
	<th>Tytuł</th>
	<th>Zgłoszone</th>
	<th>Zadania zamknięte</th>
</tr>
<?php foreach ($tpl->get('issues') as $issue): ?>
<?php
switch($issue->priority) {
	case '2':
		$color = "#F8E8E8";
		break;
	case '1':
		$color = "#ffd";
		break;
	case '0':
		$color = "#EEF6F0";
		break;
}
?>
<tr style="background-color: <?php echo $color ?>">
	<td><a href="<?php echo $tpl->url('thread', 'id', $issue->id) ?>">
		#<?php echo $issue->id ?>
	</a></td>
	<td><?php echo $issue->label_name ?></td>
	<td><?php echo $issue->title ?></td>
	<td><?php echo $issue->create_date ?> (<?php echo $tpl->date_fuzzy_age($issue->create_date) ?>)</td>
	<td><?php echo $issue->task_count_closed ?>/<?php echo $issue->task_count ?></td>
</tr>
<?php endforeach ?>
</table>
<?php endif ?>

<?php if (count($tpl->get('outdated_tasks')) > 0): ?>
    <h1>Zadania przeterminowane</h1>
    <?php $tpl->set('tasks', $tpl->get('outdated_tasks')) ?>
    <?php include 'weekly-message-tasks.php' ?>
<?php endif ?>

<?php if (count($tpl->get('coming_tasks')) > 0): ?>
    <h1>Zadania nadchodzące</h1>
    <?php $tpl->set('tasks', $tpl->get('coming_tasks')) ?>
    <?php include 'weekly-message-tasks.php' ?>
<?php endif ?>

</body>
</html>
