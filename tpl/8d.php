<h1><?php echo $bezlang['8d_report'] ?></h1>

<table>
<tr>
	<td>
		<?php echo  ucfirst($template['issue']['type']) ?>
		 <strong>#<?php echo  $template['issue']['id'] ?></strong>
	</td>
	<td>
		<strong><?php echo $bezlang['entity'] ?>:</strong>
		<?php echo  $template['issue']['entity'] ?>
	</td>
	</tr>

	<tr>
	<td>
		<strong><?php echo $bezlang['open_date'] ?>:</strong>
		<?php echo  $helper->time2date($template['issue']['date']) ?>
	</td>
	<td>
		<strong><?php echo $bezlang['reporter'] ?>:</strong>
		<?php echo  $template['issue']['reporter'] ?>
	</td>
	</tr>

	<tr>
	<td colspan="2">
		<strong><?php echo $bezlang['title'] ?>:</strong>
		<?php echo  $template['issue']['title'] ?>
	</td>
</tr>
</table>

<h2><?php echo $bezlang['2d'] ?></h2>
<?php echo  $helper->wiki_parse($template['issue']['description']) ?>

<?php if (count($template['causes']) > 0): ?>
	<h2><?php echo $bezlang['3d'] ?></h2>

	<?php foreach ($template['causes'] as $rootcause => $cause): ?>
		<h3><?php echo $rootcause ?></h3>
		<ul>
			<?php foreach($cause as $value): ?>
				<li><?php echo  $helper->wiki_parse($value['cause']) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endforeach ?>
<?php endif ?>

<?php if (count($template['tasks']) > 0): ?>
	<?php foreach($template['tasks'] as $nd => $tasks): ?>
		<h2><?php echo $bezlang[$nd] ?></h2>
		<table>
		<tr>
			<th><?php echo $bezlang['id'] ?></th>
			<th><?php echo $bezlang['class'] ?></th>
			<th><?php echo $bezlang['date'] ?></th>
			<th><?php echo $bezlang['cost'] ?></th>
		</tr>
		<?php foreach($tasks as $task): ?>
			<tr>
				<td><?php echo $helper->html_task_link($task['issue'], $task['id']) ?></td>
				<td><?php echo $task['action'] ?></td>
				<td><?php echo $helper->string_time_to_now($task['date']) ?></td>
				<td>
					<?php if ($task['cost'] == ''): ?>
						<em><?php echo $bezlang['ns'] ?></em>
					<?php else: ?>
						<?php echo $task['cost'] ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
		</table>
	<?php endforeach ?>
<?php endif ?>



<?php if (strlen(trim($template['issue']['opinion'])) > 0): ?>
	<h2><?php echo $bezlang['7d'] ?></h2>
	<?php echo  $helper->wiki_parse($template['issue']['opinion']) ?>

<?php endif ?>

<h2><?php echo $bezlang['8d'] ?></h2>
<table>
<tr>
	<td>
		<strong><?php echo $bezlang['true_date'] ?>:</strong>
		<?php echo  $helper->time2date($template['issue']['last_mod']) ?>
	</td>
	<td>
		<strong><?php echo $bezlang['state'] ?>:</strong>
		<?php echo $template['issue']['state'] ?>
	</td>
</tr>

<tr>
	<td>
		<strong><?php echo $bezlang['cost_total'] ?>:</strong>
		<?php echo $template['cost_total'] ?>
	</td>
	<td>
		<strong><?php echo $bezlang['coordinator'] ?>:</strong>
		<?php echo $template['issue']['coordinator'] ?>
	</td>
</tr>
</table>
