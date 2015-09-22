<a name="z<?php echo $task['id'] ?>"></a>
<div id="z<?php echo $task['id'] ?>" class="task
	<?php
		switch($task['state']) {
			case $bezlang['task_opened']:
				echo 'opened';
				break;
			case $bezlang['task_done']:
				echo 'closed';
				break;
			case $bezlang['task_rejected']:
				echo 'rejected';
				break;
		}
	?>">

<div class="bez_timebox">
	<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($task['date']) ?></span>
	<?php if ($task['state'] != $bezlang['task_opened']): ?>
		<span>
			<strong><?php echo $task['state']?>:</strong>
			<?php echo $helper->time2date($task['close_date']) ?>
		</span>
	<?php endif ?>
</div>

<h2>
	<a href="?id=<?php echo $this->id('issue_task', 'id', $template['issue']['id'], 'tid', $task['id']) ?>">
		#z<?php echo $task['id'] ?>
	</a>
	<?php echo lcfirst($task['action']) ?>
	(<?php echo lcfirst($task['state']) ?>)
</h2>

<table>	
<tr>
		<td>
			<strong><?php echo $bezlang['executor'] ?>:</strong>
			<?php echo $task['executor'] ?>
		</td>

		<?php if ($task['cost'] != 0): ?>
			<td>
				<strong><?php echo $bezlang['cost'] ?>:</strong>
				<?php echo $task['cost'] ?>
			</td>
		<?php endif ?>
</tr>
</table>	

<?php echo $task['task'] ?>

<?php if ($task['rejected']): ?>
	<h3><?php echo $bezlang['reason'] ?></h3>
	<?php echo $task['reason'] ?>
<?php endif ?>

<div class="bez_buttons">
	<a class="bds_inline_button" href="
		<?php echo $helper->mailto($task['executor_email'],
		$bezlang['task'].': #'.$task['issue'].' '.$template['issue']['title'].' | #z'.$task['id'].' '.$task['action'],
		$template['uri'].'#z'.$task['id']) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>
	<?php if ($template['issue'][raw_state] == 0 &&
	($task['executor_nick'] == $INFO['client'] || $helper->user_coordinator($template[issue][id]))): ?> 
		<a class="bds_inline_button"
			href="?id=<?php echo $this->id('issue_show', $template['issue']['id'], 'edit', 'task', $task['id']) ?>#z_">
		 	✎ <?php echo $bezlang['edit'] ?>
	</a>
	<?php endif ?>
</div>
</div>

