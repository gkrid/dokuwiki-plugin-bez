<a name="z<?php echo $template['task']->id ?>"></a>
<div id="z<?php echo $template['task']->id ?>"
	class="task <?php $template['task']->state_string($template['task']->state)	?>">

<div class="bez_timebox">
	<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($template['task']->date) ?></span>
	
	<?php if ($template['task']->state !== '0'): ?>
		<span>
			<strong><?php echo $bezlang[$template['task']->state_string($template['task']->state)] ?>:</strong>
			<?php echo $helper->time2date($template['task']->close_date) ?>
		</span>
		<span>
			<strong><?php echo $bezlang['report_priority'] ?>: </strong>
			<?php echo $helper->days((int)$template['task']->close_date - (int)$template['task']->date) ?>
		</span>
	<?php endif ?>
</div>

<h2>
	<a href="?id=<?php echo $this->id('show_task', 'tid', $template['task']->id) ?>">
		#z<?php echo $template['task']->id ?>
	</a>
	<?php echo lcfirst($bezlang[$template['task']->action_string($template['task']->action)]) ?>
	(<?php echo lcfirst($bezlang[$template['task']->state_string($template['task']->state)]) ?>)
</h2>

<?php
	$cost_colspan = 1;
	$task_type_colspan = 1;
	$plan_date_colspan = 1;
	$finish_time_colspan = 1;
	
	if ($template['task']->cost == '' && $template['task']->all_day_event == '1') {
		$plan_date_colspan = 3;
	} elseif ($template['task']->cost == '' && $template['task']->all_day_event == '0') {
		/*leave default*/
	} elseif ($template['task']->cost != '' && $template['task']->all_day_event == '1') {
		$plan_date_colspan = 4;
	} elseif ($template['task']->cost != '' && $template['task']->all_day_event == '0') {
		$finish_time_colspan = 2;
	}
	
	$td_with_colspan = function($colspan) {
		if ($colspan === 1) {
			echo '<td>';
		} else {
			echo '<td colspan="'.$colspan.'">';
		}
	}
?>

<table>	
<tr>
		<td>
			<strong><?php echo $bezlang['executor'] ?>:</strong>
			<?php echo $this->model->users->get_user_full_name($template['task']->executor) ?>
		</td>
		
		<td>
			<strong><?php echo $bezlang['reporter'] ?>:</strong>
			<?php echo $this->model->users->get_user_full_name($template['task']->reporter) ?>
		</td>

		<?php if ($template['task']->tasktype_string != ''): ?>
			<?php echo $td_with_colspan($task_type_colspan) ?>
				<strong><?php echo $bezlang['task_type'] ?>:</strong>
				<?php echo $template['task']->tasktype_string ?>
			</td>
		<?php endif ?>
		
		<?php if ($template['task']->cost != ''): ?>
			<?php echo $td_with_colspan($cost_colspan) ?>
				<strong><?php echo $bezlang['cost'] ?>:</strong>
				<?php echo $template['task']->cost ?>
			</td>
		<?php endif ?>
</tr>

<tr>
	<?php echo $td_with_colspan($plan_date_colspan) ?>
		<strong><?php echo $bezlang['plan_date'] ?>:</strong>
		<?php echo $template['task']->plan_date ?>
	</td>
	
	<?php if ($template['task']->all_day_event == '0'): ?>
		<td><strong><?php echo $bezlang['start_time'] ?>:</strong>
		<?php echo $template['task']->start_time ?></td>
		<?php echo $td_with_colspan($finish_time_colspan) ?>
		<strong><?php echo $bezlang['finish_time'] ?>:</strong>
		<?php echo $template['task']->finish_time ?></td>
	<?php endif ?>
	
</tr>

</table>

<?php echo $template['task']->task_cache ?>

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
	<?php if ($template['task']->state == '2'): ?>
		<h3><?php echo $bezlang['reason'] ?></h3>
		<?php echo $template['task']->reason_cache ?>
	<?php elseif ($template['task']->state == '1'): ?>
		<h3><?php echo $bezlang['evaluation'] ?></h3>
		<?php echo $template['task']->reason_cache ?>
	<?php endif ?>
<?php endif ?>



<?php if (!isset($nparams['state'])): ?>
<div class="bez_buttons">

	<a class="bds_inline_button"
		href="?id=<?php echo $helper->id('icalendar', 'tid', $template['task']->id) ?>">
		<span class="bez_awesome">&#xf073;</span>  <?php echo $bezlang['download_in_icalendar'] ?>
	</a>

	<?php if ($template['task']->state == '0' && $template['task']->get_level() >= 10): ?>
		<a class="bds_inline_button"
			href="?id=<?php
				if($template['task']->issue == '') {
					echo $helper->id('show_task', 'tid', $template['task']->id, 'state', '1');
				} elseif (isset($nparams['cid'])) {
					echo $helper->id('issue_cause_task', 'id', $template['task']->issue, 'cid', $template['task']->cause, 'tid', $template['task']->id, 'state', '1');
				} else {
					echo $helper->id('issue_task', 'id', $template['task']->issue, 'tid', $template['task']->id, 'state', '1');
				}
			?>#form">
			↬ <?php echo $bezlang['task_do'] ?>
		</a>
		<a class="bds_inline_button"
			href="?id=<?php
				if($template['task']->issue == '') {
					echo $helper->id('show_task', 'tid', $template['task']->id, 'state', '2');
				} elseif (isset($nparams['cid'])) {
					echo $helper->id('issue_cause_task', 'id', $template['task']->issue, 'cid', $template['task']->cause, 'tid', $template['task']->id, 'state', '2');
				} else {
					echo $helper->id('issue_task', 'id', $template['task']->issue, 'tid', $template['task']->id, 'state', '2');
				}
			?>#form">
			↛ <?php echo $bezlang['task_reject'] ?>
		</a>
	<?php elseif ($template['task']->get_level() >= 10): ?>
		<a class="bds_inline_button"
				href="?id=<?php
					if($template['task']->issue == '') {
						echo $helper->id('show_task', 'tid', $template['task']->id, 'state', '0');
					} elseif (isset($nparams['cid'])) {
						echo $helper->id('issue_cause_task', 'id', $template['task']->issue, 'cid', $template['task']->cause, 'tid', $template['task']->id, 'state', '0');
					} else {
						echo $helper->id('issue_task', 'id', $template['task']->issue, 'tid', $template['task']->id, 'state', '0');
					}
				?>#form">
			 	↻ <?php echo $bezlang['task_reopen'] ?>
			</a>
	<?php endif ?>

	<?php if($template['task']->get_level() >= 15 || $template['task']->reporter === $template['task']->get_user()): ?>
			<a class="bds_inline_button"
				href="?id=<?php
					if($template['task']->issue == '') {
						echo $helper->id('task_report', 'tasktype', $template['task']->tasktype, 'tid', $template['task']->id);
					} elseif ($template['task']->cause == '') {
						echo $helper->id('task_form', 'id', $template['task']->issue, 'tid', $template['task']->id);
					} else {
						echo $helper->id('task_form', 'id', $template['task']->issue, 'cid', $template['task']->cause, 'tid', $template['task']->id);
					}
				?>">
				✎ <?php echo $bezlang['edit'] ?>
			</a>
	<?php endif ?>

	<a class="bds_inline_button" href="
	<?php echo $helper->mailto($this->model->users->get_user_email($template['task']->executor),
	$bezlang['task'].': #z'.$template['task']->id.' '.lcfirst($bezlang[$template['task']->action_string($template['task']->action)]),
	$template['task']->issue != '' ? 
		DOKU_URL . 'doku.php?id='.$this->id('issue_task', 'id', $template['task']->issue, 'tid', $template['task']->id)
		: DOKU_URL . 'doku.php?id='.$this->id('show_task', 'tid', $template['task']->id)) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>

	<?php if ($template['task']->tasktype != NULL && $template['task']->get_level() >= 5): ?>
		<a class="bds_inline_button"
				href="?id=<?php echo $this->id('task_report', 'duplicate', $template['task']->id, 'tasktype', $template['task']->tasktype) ?>">
				⇲ <?php echo $bezlang['duplicate'] ?>
		</a>
	<?php endif ?>
</div>	
<?php endif ?>

</div>

