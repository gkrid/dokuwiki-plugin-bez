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

<?php if (isset($nparams['state'])): ?>
	<?php if ($nparams['state'] == 2): ?>
		<h3><?php echo $bezlang['reason'] ?></h3>
	<?php else: ?>
		<h3><?php echo $bezlang['evaluation'] ?></h3>
	<?php endif ?>
	<form class="bez_form bez_task_form" action="?id=<?php echo $helper->nparams_to_id($this->action, $nparams) ?>:action:update" method="POST">
		<textarea name="reason" id="reason"><?php echo $value['reason'] ?></textarea>
		<br>
		<?php if ($nparams['state'] == 2): ?>
			<input type="submit" value="<?php echo $bezlang['task_reject'] ?>">
		<?php else: ?>
			<input type="submit" value="<?php echo $bezlang['task_do'] ?>">
		<?php endif ?>	
		<a href="?id=<?php echo $helper->nparams_to_id($this->action, array_diff_key($nparams, array('state'=>''))); ?>"
			 class="bez_delete_button bez_link_button">
				<?php echo $bezlang['cancel'] ?>
		</a>
	</form>
<?php else: ?>
	<?php if ($task['raw_state'] == 2): ?>
		<h3><?php echo $bezlang['reason'] ?></h3>
	<?php elseif ($task['raw_state'] == 1): ?>
		<h3><?php echo $bezlang['evaluation'] ?></h3>
	<?php endif ?>
	<?php echo $task['reason'] ?>
<?php endif ?>

<?php if (!isset($nparams['state'])): ?>
<div class="bez_buttons">
	<?php if ($task['raw_state'] == 0): ?>
		<?php if($task['executor_nick'] == $INFO['client'] || $helper->user_coordinator($template['issue']['id'])): ?> 
			<a class="bds_inline_button"
				href="?id=<?php
					if (isset($nparams['cid']))
						echo $helper->id('issue_cause_task', 'id', $template['issue']['id'], 'cid', $cause['id'], 'tid', $task['id'], 'state', '1');
					else
						echo $helper->id('issue_task', 'id', $template['issue']['id'], 'tid', $task['id'], 'state', '2');
				?>">
			 	↬ <?php echo $bezlang['task_do'] ?>
			</a>
			<a class="bds_inline_button"
				href="?id=<?php echo $_GET['id'] ?>:state:2">
			 	↛ <?php echo $bezlang['task_reject'] ?>
			</a>
		<?php endif ?>
	<?php endif ?>
	<?php if($helper->user_coordinator($template['issue']['id'])): ?> 
		<a class="bds_inline_button"
			href="?id=<?php echo $this->id('task_form', 'id', $template['issue']['id'], 'cid', $cause[id], 'tid', $task['id']) ?>">
		 	✎ <?php echo $bezlang['edit'] ?>
		</a>
	<?php endif ?>

	<a class="bds_inline_button" href="
	<?php echo $helper->mailto($task['executor_email'],
	$bezlang['task'].': #'.$task['issue'].' '.$template['issue']['title'].' | #z'.$task['id'].' '.$task['action'],
	DOKU_URL . 'doku.php?id='.$this->id('issue_task', 'id', $template['issue']['id'], 'tid', $task['id'])) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>
</div>
<?php endif ?>

</div>

