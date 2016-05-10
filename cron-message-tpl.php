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
<?php if (count($his_issues) > 0): ?>
<h1>Twoje niezgodności:</h1>
<table>
<tr>
	<th>Nr</th>
	<th>Typ problemu</th>
	<th>Tytuł</th>
	<th>Zgłoszone</th>
	<th>Ostatnia zmiana</th>
	<th>Zadania zamknięte</th>
</tr>
<?php foreach ($his_issues as $issue): ?>
<?php
switch($issue['priority']) {
	case 0:
		$color = "#EEF6F0";
		break;
	case 1:
		$color = "#ffd";
		break;
	case 2:
		$color = "#F8E8E8";
		break;
}
?>
<tr style="background-color: <?php echo $color ?>">
	<td><a href="<?php echo $http ?>://<?php echo $URI ?>/doku.php?id=bez:issue:id:<?php echo $issue['id'] ?>">
		#<?php echo $issue['id'] ?>
	</a></td>
	<td><?php echo $issue['type'] ?></td>
	<td><?php echo $issue['title'] ?></td>
	<td><?php echo $helper->string_time_to_now($issue['date']) ?></td>
	<td><?php echo $helper->string_time_to_now($issue['last_mod']) ?></td>
	<td>
		<a href="<?php echo $http ?>://<?php echo $URI ?>/doku.php?id=bez:tasks:issue:<?php echo $issue['id'] ?>:state:0">
			<?php echo $issue['tasks_closed'] ?>
		</a> /
		<a href="<?php echo $http ?>://<?php echo $URI ?>/doku.php?id=bez:tasks:issue:<?php echo $issue['id'] ?>">
			<?php echo $issue['tasks_all'] ?>
		</a> 
	</td>
</tr>
<?php endforeach ?>
</table>
<?php endif ?>

<?php if (count($outdated_tasks) > 0): ?>
<h1>Zadania przeterminowane</h1>
<table>
<tr>
	<th>Nr</th>
	<th>Typ działania</th>
	<th>Typ zadania</th>
	<th>Zgłoszone</th>
	<th>Plan</th>
</tr>
<?php foreach ($outdated_tasks as $task): ?>
<?php
switch($task['priority']) {
	case 0:
		$color = "#EEF6F0";
		break;
	case 1:
		$color = "#ffd";
		break;
	case 2:
		$color = "#F8E8E8";
		break;
}
?>
<tr style="background-color: <?php echo $color ?>">
<?php
	$url = "$http://$URI/doku.php?id=";
	if (isset($task['issue']))
		$url .= "bez:issue_task:id:$task[issue]:tid:$task[id]";
	else
		$url .= "bez:show_task:tid:$task[id]";
?>
	<td><a href="<?php echo $url ?>">
		#<?php echo $task['id'] ?>
	</a></td>
	<td><?php echo $task['action'] ?></td>
	<td><?php echo $task['tasktype'] ?></td>
	<td><?php echo $helper->string_time_to_now($task['date']) ?></td>
	<td>
		<?php if ($task['plan_date'] != ''): ?>
			<?php echo $task['plan_date'] ?>
			<?php if ($task['all_day_event'] == '0'): ?>
				<?php echo $task['start_time'] ?>&nbsp;-&nbsp;<?php echo $task['finish_time'] ?>
			<?php endif ?>
		<?php else: ?>
			<em>---</em>
		<?php endif ?>
	</td>
</tr>
<?php endforeach ?>
</table>
<?php endif ?>

<?php if (count($coming_tasks) > 0): ?>
<h1>Zadania nadchodzące</h1>

<table>
<tr>
	<th>Nr</th>
	<th>Typ działania</th>
	<th>Typ zadania</th>
	<th>Zgłoszone</th>
	<th>Plan</th>
</tr>
<?php foreach ($coming_tasks as $task): ?>
<?php
switch($task['priority']) {
	case 0:
		$color = "#EEF6F0";
		break;
	case 1:
		$color = "#ffd";
		break;
	case 2:
		$color = "#F8E8E8";
		break;
}
?>
<tr style="background-color: <?php echo $color ?>">
<?php
	$url = "$http://$URI/doku.php?id=";
	if (isset($task['issue']))
		$url .= "bez:issue_task:id:$task[issue]:tid:$task[id]";
	else
		$url .= "bez:show_task:tid:$task[id]";
?>
	<td><a href="<?php echo $url ?>">
		#<?php echo $task['id'] ?>
	</a></td>
	<td><?php echo $task['action'] ?></td>
	<td><?php echo $task['tasktype'] ?></td>
	<td><?php echo $helper->string_time_to_now($task['date']) ?></td>
	<td>
		<?php if ($task['plan_date'] != ''): ?>
			<?php echo $task['plan_date'] ?>
			<?php if ($task['all_day_event'] == '0'): ?>
				<?php echo $task['start_time'] ?>&nbsp;-&nbsp;<?php echo $task['finish_time'] ?>
			<?php endif ?>
		<?php else: ?>
			<em>---</em>
		<?php endif ?>
	</td>
</tr>
<?php endforeach ?>
</table>

<?php endif ?>

<?php if (count($open_tasks) > 0): ?>
<h1>Zadania otwarte</h1>

<table>
<tr>
	<th>Nr</th>
	<th>Typ działania</th>
	<th>Typ zadania</th>
	<th>Zgłoszone</th>
</tr>
<?php foreach ($open_tasks as $task): ?>
<?php
switch($task['priority']) {
	case 0:
		$color = "#EEF6F0";
		break;
	case 1:
		$color = "#ffd";
		break;
	case 2:
		$color = "#F8E8E8";
		break;
}
?>
<tr style="background-color: <?php echo $color ?>">
<?php
	$url = "$http://$URI/doku.php?id=";
	if (isset($task['issue']))
		$url .= "bez:issue_task:id:$task[issue]:tid:$task[id]";
	else
		$url .= "bez:show_task:tid:$task[id]";
?>
	<td><a href="<?php echo $url ?>">
		#<?php echo $task['id'] ?>
	</a></td>
	<td><?php echo $task['action'] ?></td>
	<td><?php echo $task['tasktype'] ?></td>
	<td><?php echo $helper->string_time_to_now($task['date']) ?></td>
</tr>
<?php endforeach ?>
</table>
<?php endif ?>


</body>
</html>
