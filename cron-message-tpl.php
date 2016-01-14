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

<?php if (count($his_tasks) > 0): ?>
<h1>Twoje zadania:</h1>
<table>
<tr>
	<th>Nr</th>
	<th>Działanie</th>
	<th>Zgłoszone</th>
</tr>
<?php foreach ($his_tasks as $task): ?>
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
	<td><a href="<?php echo $http ?>://<?php echo $URI ?>/doku.php?id=bez:issue_task:id:<?php echo $task['issue'] ?>:tid:<?php echo $task['id'] ?>">
		#<?php echo $task['id'] ?>
	</a></td>
	<td><?php echo $task['action'] ?></td>
	<td><?php echo $helper->string_time_to_now($task['date']) ?></td>
</tr>
<?php endforeach ?>
</table>
<?php endif ?>
</body>
</html>
