<td>
	<h3><strong>#z<?php echo $task[id] ?></strong> <?php echo $task[executor] ?> (<?php echo lcfirst($task[state]) ?>)</h3>
	<?php echo $task[task] ?>
</td>
<td>
	<?php if ($task[close_date] != NULL): ?>
		<h3><strong><?php echo $task[state] ?>:</strong> <?php echo $helper->time2date($task['close_date']) ?></h3>
		<?php echo $task[reason] ?>
</td>
	<?php endif ?>
</td>
