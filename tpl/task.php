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
			<span>
		<strong><?php echo $bezlang['report_priority'] ?>: </strong>
		<?php echo $helper->days((int)$task['close_date'] - (int)$task['date']) ?>
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


<?php
//count colspan
$top_colspan = 0;
$bottom_colspan = 0;

$top_columns = 1;
if ($task['tasktype'] != '')
	$top_columns++;

if ($task['cost'] != 0)
	$top_columns++;

	

if (isset($nparams['plan']))
	$bottom_columns = 4;
else if ($task['plan_date'] != '') {
	$bottom_columns = 1;
	if ($task['all_day_event'] == '0')
		$bottom_columns = 3;
} else
	//w celu wyzerowania górnego colspana
	$top_columns = 3;

if ($top_columns > $bottom_columns)
	$bottom_colspan = $top_columns - $bottom_columns + 1;
elseif ($top_columns < $bottom_columns)
	$top_colspan = $bottom_columns - $top_columns + 1;
	
$colspan1 = 0;
$colspan2 = 0;
$colspan3 = 0;

if ($task['cost'] != 0)
	$colspan3 = $top_colspan;
else if ($task['tasktype'] != '')
	$colspan2 = $top_colspan;
else
	$colspan1 = $top_colspan;
	
$colspan4 = 0;
$colspan5 = 0;
if ($task['all_day_event'] == '0')
	$colspan5 = $bottom_colspan;
else
	$colspan4 = $bottom_colspan;

?>
<table>	
<tr>
		<td colspan="<?php echo $colspan1 ?>">
			<strong><?php echo $bezlang['executor'] ?>:</strong>
			<?php echo $task['executor'] ?>
		</td>
		
		<?php if ($task['tasktype'] != ''): ?>
			<td colspan="<?php echo $colspan2 ?>">
				<strong><?php echo $bezlang['task_type'] ?>:</strong>
				<?php echo $task['tasktype'] ?>
			</td>
		<?php endif ?>
		
		<?php if ($task['cost'] != 0): ?>
			<td colspan="<?php echo $colspan3 ?>">
				<strong><?php echo $bezlang['cost'] ?>:</strong>
				<?php echo $task['cost'] ?>
			</td>
		<?php endif ?>
</tr>

<?php if ($task['plan_date'] != '' && !isset($nparams['plan'])): ?>
<tr>
	<td colspan="<?php echo $colspan4 ?>"><strong><?php echo $bezlang['plan_date'] ?>:</strong>
	<?php echo $task['plan_date'] ?></td>
	<?php if ($task['all_day_event'] == '0'): ?>
		<td><strong><?php echo $bezlang['start_time'] ?>:</strong>
		<?php echo $task['start_time'] ?></td>
		<td colspan="<?php echo $colspan5 ?>"><strong><?php echo $bezlang['finish_time'] ?>:</strong>
		<?php echo $task['finish_time'] ?></td>
	<?php endif ?>
</tr>
<?php endif ?>

<?php if (isset($nparams['plan'])): ?>
<tr>
<form action="?id=<?php echo $helper->nparams_to_id($this->action, $nparams) ?>:action:save_plan" method="post">
	<td><strong><?php echo $bezlang['plan_date'] ?>:</strong>
	<input name="plan_date" style="width:70px;" value="<?php echo $value['plan_date'] ?>"/><label><input type="checkbox" name="all_day_event" value="1" 
	<?php if (isset($value['all_day_event']) && $value['all_day_event'] != 0): ?>
		checked
	<?php endif ?> /> <?php echo $bezlang['all_day_event'] ?></label></td>
	<td><strong><?php echo $bezlang['start_time'] ?>:</strong>
	<input name="start_time" style="width:50px;" class="bez_timepicker" value="<?php echo $value['start_time'] ?>"/>
	</td>
	<td><strong><?php echo $bezlang['finish_time'] ?>:</strong>
	<input name="finish_time" style="width:50px;" class="bez_timepicker" value="<?php echo $value['finish_time'] ?>" />
	<td><input type="submit" value="<?php echo $bezlang['save'] ?>" />
	<a href="?id=<?php echo $helper->nparams_to_id($this->action, array_diff_key($nparams, array('plan'=>''))); ?>"
			 class="bez_delete_button bez_link_button">
				<?php echo $bezlang['cancel'] ?>
		</a></td>
</form>
</tr>
	
<?php endif ?>
</table>

<?php echo $task['task'] ?>

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
	<?php if ($task['raw_state'] == 2): ?>
		<h3><?php echo $bezlang['reason'] ?></h3>
	<?php elseif ($task['raw_state'] == 1): ?>
		<h3><?php echo $bezlang['evaluation'] ?></h3>
	<?php endif ?>
	<?php echo $task['reason'] ?>
<?php endif ?>



<?php if (!isset($nparams['state']) && !isset($nparams['plan'])): ?>
<div class="bez_buttons">
<?php if ($task['plan_date'] != ''): ?>	
		<a class="bds_inline_button"
		href="?id=<?php echo $helper->id('icalendar', 'tid', $task['id']) ?>">
		📅  <?php echo $bezlang['download_in_icalendar'] ?>
	</a>
<?php endif ?>
	<?php if ($task['raw_state'] == 0): ?>
		<?php if($task['executor_nick'] == $INFO['client'] || $helper->user_coordinator($template['issue']['id'])): ?> 
		
			<a class="bds_inline_button"
				href="?id=<?php
				 	if(!isset($template['issue']))
						echo $helper->id('show_task', 'tid', $task['id'], 'plan', 'plan');
					elseif (isset($nparams['cid']))
						echo $helper->id('issue_cause_task', 'id', $template['issue']['id'], 'cid', $cause['id'], 'tid', $task['id'], 'plan', 'plan');
					else
						echo $helper->id('issue_task', 'id', $template['issue']['id'], 'tid', $task['id'], 'plan', 'plan');
				?>#form">
			 	☑ <?php echo $bezlang['task_plan'] ?>
			</a>
			
			<a class="bds_inline_button"
				href="?id=<?php
					if(!isset($template['issue']))
						echo $helper->id('show_task', 'tid', $task['id'], 'state', '1');
					elseif (isset($nparams['cid']))
						echo $helper->id('issue_cause_task', 'id', $template['issue']['id'], 'cid', $cause['id'], 'tid', $task['id'], 'state', '1');
					else
						echo $helper->id('issue_task', 'id', $template['issue']['id'], 'tid', $task['id'], 'state', '1');
				?>#form">
			 	↬ <?php echo $bezlang['task_do'] ?>
			</a>
			<a class="bds_inline_button"
				href="?id=<?php
					if(!isset($template['issue']))
						echo $helper->id('show_task', 'tid', $task['id'], 'state', '2');
					elseif (isset($nparams['cid']))
						echo $helper->id('issue_cause_task', 'id', $template['issue']['id'], 'cid', $cause['id'], 'tid', $task['id'], 'state', '2');
					else
						echo $helper->id('issue_task', 'id', $template['issue']['id'], 'tid', $task['id'], 'state', '2');
				?>#form">
			 	↛ <?php echo $bezlang['task_reject'] ?>
			</a>
		<?php endif ?>
	<?php endif ?>
	<?php if($helper->user_coordinator($template['issue']['id'])): ?>
		<?php if(isset($template['issue'])): ?>
			<a class="bds_inline_button"
				href="?id=<?php echo $this->id('task_form', 'id', $template['issue']['id'], 'cid', $cause[id], 'tid', $task['id']) ?>">
				✎ <?php echo $bezlang['edit'] ?>
			</a>
		<?php else: ?>
			<a class="bds_inline_button"
				href="?id=<?php echo $this->id('task_form_plan', 'tid', $task['id']) ?>">
				✎ <?php echo $bezlang['edit'] ?>
			</a>
		<?php endif ?>
	<?php endif ?>

	<a class="bds_inline_button" href="
	<?php echo $helper->mailto($task['executor_email'],
	$bezlang['task'].': #'.$task['issue'].' '.$template['issue']['title'].' | #z'.$task['id'].' '.$task['action'],
	isset($template['issue']) ? 
		DOKU_URL . 'doku.php?id='.$this->id('issue_task', 'id', $template['issue']['id'], 'tid', $task['id'])
		: DOKU_URL . 'doku.php?id='.$this->id('show_task', 'tid', $task['id'])) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>
	
		<a class="bds_inline_button"
				href="?id=<?php echo $this->id('task_report', 'duplicate', $task['id']) ?>">
				⇲ <?php echo $bezlang['duplicate'] ?>
			</a>
	
</div>
<?php endif ?>

</div>

