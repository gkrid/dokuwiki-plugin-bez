<h1>
	<?php echo $bezlang['8d_report'] ?>
	<span id="bez_8d_send_button">[<a href="
		<?php echo $helper->mailto($template['issue']['coordinator_email'],
		$bezlang['8d_report'].': #'.$template['issue']['id'].' '.$template['issue']['title'],
		$template['uri']) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>]</span>
</h1>

<table>
<tr>
	<td>
		 <strong>
		 	<a href="<?php echo $this->issue_uri($template[issue][id]) ?>">
				#<?php echo  $template['issue']['id'] ?>
			</a>
		</strong>
		<?php echo  ucfirst($template['issue']['type']) ?>
	</td>

	<td>
		<strong><?php echo $bezlang['open_date'] ?>:</strong>
		<?php echo  $helper->time2date($template['issue']['date']) ?>
	</td>
</tr>

	<tr>
	<td colspan="2">
		<strong><?php echo $bezlang['title'] ?>:</strong>
		<?php echo  $template['issue']['title'] ?>
	</td>
</tr>
</table>
<h2><?php echo $bezlang['1d'] ?></h2>
<ul>
	<?php foreach($template['team'] as $user): ?>
		<li><?php echo  $user ?></li>
	<?php endforeach ?>
</ul>

<h2><?php echo $bezlang['2d'] ?></h2>
<?php echo  $template['issue']['description'] ?>

<?php if (count($template['causes']) > 0): ?>
	<h2><?php echo $bezlang['3d'] ?></h2>
	<table>
	<?php foreach ($template['causes'] as $rootcause => $cause): ?>

		<tr><th colspan=2><?php echo $rootcause ?></th></tr>
		<?php foreach($cause as $value): ?>
			<tr>
			<td>
				<a href="?id=<?php echo $this->id('issue_cause', 'id', $template[issue][id], 'cid', $value[id] ) ?>">
						#p<?php echo $value[id] ?>
				</a>
			</td>
			<td>
			<?php echo  $helper->wiki_parse($value['cause']) ?>
			</td>
			</tr>
		<?php endforeach ?>
	<?php endforeach ?>
	</table>
<?php endif ?>

<?php if (count($template['tasks']) > 0): ?>
	<?php foreach($template['tasks'] as $nd => $tasks): ?>
		<h2><?php echo $bezlang[$nd] ?></h2>
		<table>
		<tr>
			<th><?php echo $bezlang['id'] ?></th>
			<th><?php echo $bezlang['task'] ?></th>
			<th><?php echo $bezlang['state'] ?></th>
			<th><?php echo $bezlang['cost'] ?></th>
			<th><?php echo $bezlang['date'] ?></th>
			<th><?php echo $bezlang['closed'] ?></th>
		</tr>
		<?php foreach($tasks as $task): ?>
			<tr>
				<td>	
					<a href="?id=<?php echo $this->id('issue_task', 'id', $task[issue], 'tid', $task[id]) ?>">
						#z<?php echo $task[id] ?>
					</a>
				</td>
				<td><?php echo  $task['task'] ?></td>
				<td><?php echo $task['state'] ?></td>
				<td>
					<?php if ($task['cost'] == ''): ?>
						<em><?php echo $bezlang['ns'] ?></em>
					<?php else: ?>
						<?php echo $task['cost'] ?>
					<?php endif ?>
				</td>
				<td><?php echo $helper->time2date($task['date']) ?></td>
				<td>
					<?php if ($task['state'] == $bezlang['task_opened']): ?>
						<em><?php echo $bezlang['ns'] ?></em>
					<?php else: ?>
						<?php echo $helper->time2date($task['close_date']) ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
		</table>
	<?php endforeach ?>
<?php endif ?>



<?php if (strlen(trim($template['issue']['opinion'])) > 0): ?>
	<h2><?php echo $bezlang['7d'] ?></h2>
	<?php echo  $template['issue']['opinion'] ?>

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
		<strong><?php echo $bezlang['totalcost'] ?>:</strong>
		<?php echo $template['cost_total'] ?>
	</td>
	<td>
		<strong><?php echo $bezlang['coordinator'] ?>:</strong>
		<?php echo $template['issue']['coordinator'] ?>
	</td>
</tr>
</table>
