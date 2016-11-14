<a name="z<?php echo $task->id ?>"></a>
<div id="z<?php echo $task->id ?>"
	class="task <?php $task->state_string($task->state)	?>">

<div class="bez_timebox">
	<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($task->date) ?></span>
	
	<?php if ($task->state !== '0'): ?>
		<span>
			<strong><?php echo $bezlang[$task->state_string($task->state)] ?>:</strong>
			<?php echo $helper->time2date($task->close_date) ?>
		</span>
		<span>
			<strong><?php echo $bezlang['report_priority'] ?>: </strong>
			<?php echo $helper->days((int)$task->close_date - (int)$task->date) ?>
		</span>
	<?php endif ?>
</div>

<h2>
	<a href="?id=<?php echo $this->id('show_task', 'tid', $task->id) ?>">
		#z<?php echo $task->id ?>
	</a>
	<?php echo lcfirst($bezlang[$task->action_string($task->action)]) ?>
	(<?php echo lcfirst($bezlang[$task->state_string($task->state)]) ?>)
</h2>

<?php
	$cost_colspan = 1;
	$tasktype_colspan = 1;
	$plan_date_colspan = 1;
	
	if ($task->cost == '' && $task->all_day_event == '1') {
		$plan_date_colspan = 2;
	} elseif ($task->cost == '' && $task->all_day_event == '0') {
		$tasktype_colspan = 2;
	} elseif ($task->cost != '' && $task->all_day_event == '1') {
		$plan_date_colspan = 3;
	}
?>

<table>	
<tr>
		<td>
			<strong><?php echo $bezlang['executor'] ?>:</strong>
			<?php echo $this->model->users->get_user_full_name($task->executor) ?>
		</td>

		<?php if ($task->tasktype_string != ''): ?>
			<td colspan="<?php echo $tasktype_colspan ?>">
				<strong><?php echo $bezlang['task_type'] ?>:</strong>
				<?php echo $task->tasktype_string ?>
			</td>
		<?php endif ?>
		
		<?php if ($task->cost != ''): ?>
			<td colspan="<?php echo $cost_colspan ?>">
				<strong><?php echo $bezlang['cost'] ?>:</strong>
				<?php echo $task->cost ?>
			</td>
		<?php endif ?>
</tr>

<tr>
	<td colspan="<?php echo $plan_date_colspan ?>"><strong><?php echo $bezlang['plan_date'] ?>:</strong>
	<?php echo $task->plan_date ?></td>
	
	<?php if ($task->all_day_event == '0'): ?>
		<td><strong><?php echo $bezlang['start_time'] ?>:</strong>
		<?php echo $task->start_time ?></td>
		<td><strong><?php echo $bezlang['finish_time'] ?>:</strong>
		<?php echo $task->finish_time ?></td>
	<?php endif ?>
	
</tr>

</table>

<?php echo $task->task_cache ?>

<?php if (isset($nparams['state'])): ?>
	<a name="form"></a>
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
	<?php if ($task->state == '2'): ?>
		<h3><?php echo $bezlang['reason'] ?></h3>
		<?php echo $task->reason_cache ?>
	<?php elseif ($task->state == '1'): ?>
		<h3><?php echo $bezlang['evaluation'] ?></h3>
		<?php echo $task->reason_cache ?>
	<?php endif ?>
<?php endif ?>



<?php if (!isset($nparams['state'])): ?>
<div class="bez_buttons">

	<a class="bds_inline_button"
		href="?id=<?php echo $helper->id('icalendar', 'tid', $task->id) ?>">
		<span class="bez_awesome">&#xf073;</span>  <?php echo $bezlang['download_in_icalendar'] ?>
	</a>

	<?php if ($task->state == '0' && $task->get_level() >= 10): ?>
		<a class="bds_inline_button"
			href="?id=<?php
				if($task->issue == '') {
					echo $helper->id('show_task', 'tid', $task->id, 'state', '1');
				} elseif (isset($nparams['cid'])) {
					echo $helper->id('issue_cause_task', 'id', $task->issue, 'cid', $task->cause, 'tid', $task->id, 'state', '1');
				} else {
					echo $helper->id('issue_task', 'id', $task->issue, 'tid', $task->id, 'state', '1');
				}
			?>#form">
			↬ <?php echo $bezlang['task_do'] ?>
		</a>
		<a class="bds_inline_button"
			href="?id=<?php
				if($task->issue == '') {
					echo $helper->id('show_task', 'tid', $task->id, 'state', '2');
				} elseif (isset($nparams['cid'])) {
					echo $helper->id('issue_cause_task', 'id', $task->issue, 'cid', $task->cause, 'tid', $task->id, 'state', '2');
				} else {
					echo $helper->id('issue_task', 'id', $task->issue, 'tid', $task->id, 'state', '2');
				}
			?>#form">
			↛ <?php echo $bezlang['task_reject'] ?>
		</a>
	<?php elseif ($task->get_level() >= 10): ?>
		<a class="bds_inline_button"
				href="?id=<?php
					if($task->issue == '') {
						echo $helper->id('show_task', 'tid', $task->id, 'state', '0');
					} elseif (isset($nparams['cid'])) {
						echo $helper->id('issue_cause_task', 'id', $task->issue, 'cid', $task->cause, 'tid', $task->id, 'state', '0');
					} else {
						echo $helper->id('issue_task', 'id', $task->issue, 'tid', $task->id, 'state', '0');
					}
				?>#form">
			 	↻ <?php echo $bezlang['task_reopen'] ?>
			</a>
	<?php endif ?>
	
	<?php if($task->get_level() >= 15): ?>
			<a class="bds_inline_button"
				href="?id=<?php
					if($task->issue == '') {
						echo $helper->id('task_report', 'tasktype', $task->tasktype, 'tid', $task->id);
					} elseif ($task->cause == '') {
						echo $helper->id('task_form', 'id', $task->issue, 'tid', $task->id);
					} else {
						echo $helper->id('task_form', 'id', $task->issue, 'cid', $task->cause, 'tid', $task->id);
					}
				?>">
				✎ <?php echo $bezlang['edit'] ?>
			</a>
	<?php endif ?>

	<a class="bds_inline_button" href="
	<?php echo $helper->mailto($this->model->users->get_user_email($task->executor),
	$bezlang['task'].': #z'.$task->id.' '.lcfirst($bezlang[$task->action_string($task->action)]),
	$task->issue != '' ? 
		DOKU_URL . 'doku.php?id='.$this->id('issue_task', 'id', $task->issue, 'tid', $task->id)
		: DOKU_URL . 'doku.php?id='.$this->id('show_task', 'tid', $task->id)) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>
	
	<?php if($task->get_level() >= 12): ?>
		<a class="bds_inline_button"
				href="?id=<?php echo $this->id('task_report', 'duplicate', $task->id, 'tasktype', $task->tasktype) ?>">
				⇲ <?php echo $bezlang['duplicate'] ?>
		</a>
	<?php endif ?>
</div>	
<?php endif ?>

</div>

