<h1 class="bez_report">
<?php echo $template['title'] ?>
<span><?php echo $bezlang['report_subhead'] ?></span>
</h1>

<h2><?php echo $bezlang['report_issues'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['type'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
		<th><?php echo $bezlang['totalcost'] ?></th>
	</tr>
	<?php foreach ($template['report']['issues'] as $issue): ?>
		<tr>
			<td><?php echo $issue['type'] ?></td>
			<td><?php echo $issue['number'] ?></td>
			<td>
				<?php if ($issue['totalcost'] == ''): ?>
					<em><?php echo $bezlang['ns'] ?></em>
				<?php else: ?>
					<?php echo $issue['totalcost'] ?>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['issues_total'] ?></td>
		<td><?php echo $template['report']['issues_totalcost'] ?></td>
	</tr>
</table>
<h2><?php echo $bezlang['report_tasks'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['class'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
		<th><?php echo $bezlang['totalcost'] ?></th>
	</tr>
	<?php foreach ($template['report']['tasks'] as $task): ?>
		<tr>
			<td><?php echo $task['action'] ?></td>
			<td><?php echo $task['number'] ?></td>
			<td>
				<?php if ($task['totalcost'] == ''): ?>
					<em><?php echo $bezlang['ns'] ?></em>
				<?php else: ?>
					<?php echo $task['totalcost'] ?>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['tasks_total'] ?></td>
		<td><?php echo $template['report']['tasks_totalcost'] ?></td>
	</tr>
</table>

<h2><?php echo $bezlang['report_causes'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['root_cause'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
	</tr>
	<?php foreach ($template['report']['causes'] as $cause): ?>
		<tr>
			<td><?php echo $cause['rootcause'] ?></td>
			<td><?php echo $cause['number'] ?></td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['causes_total'] ?></td>
	</tr>
</table>

<h2><?php echo $bezlang['report_priority'] ?></h2>
<table class="bez bez_sumarise">
	<tr>
		<th><?php echo $bezlang['priority'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
		<th><?php echo $bezlang['average'] ?></th>
	</tr>
	<?php foreach ($template['report']['priorities'] as $priority): ?>
		<tr class="pr<?php echo $priority['priority_nr'] ?>">
			<td><?php echo $priority['priority'] ?></td>
			<td><?php echo $priority['number'] ?></td>
			<td><?php echo $priority['average'] ?></td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['priorities_total'] ?></td>
		<td><?php echo $template['report']['priorities_average'] ?></td>
	</tr>
</table>
