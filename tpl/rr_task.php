<td>
	<h3>
		<strong>
			<a href="?id=<?php echo $this->id('issue_task', 'id', $task[issue], 'tid', $task[id]) ?>">
				#z<?php echo $task[id] ?>
			</a>
		</strong>
		<?php echo $task[executor] ?>
	</h3>
	<?php echo $task[task] ?>
</td>
<td>
	<?php if ($task[close_date] == NULL): ?>
	<strong><?php echo $bezlang[open] ?>:</strong> <?php echo $helper->time2date($task['date']) ?>
	<?php else: ?>
	<strong><?php echo $task[state] ?>:</strong> <?php echo $helper->time2date($task['close_date']) ?>
	<br><br><?php echo $task[reason] ?>
</td>
	<?php endif ?>
	
</td>
