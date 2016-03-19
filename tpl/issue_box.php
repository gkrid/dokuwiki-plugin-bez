<?php if ($template['opentasks']): ?>
	<div class="info"><?php echo $bezlang['issue_unclosed_tasks'] ?></div>
<?php endif ?>
<?php if ($template['issue']['raw_coordinator'] == '-proposal'): ?>
	<div class="info"><?php echo $bezlang['issue_is_proposal'] ?></div>
<?php endif ?>
<?php if ($template['cause_without_task']): ?>
	<div class="info"><?php echo $bezlang['cause_without_task'] ?></div>
<?php endif ?>

<?php if (!$template['anytasks'] && $template['issue']['raw_state'] == 0): ?>
	<div class="info"><?php echo $bezlang['issue_no_tasks'] ?></div>
<?php endif ?>
<div id="bds_issue_box" class="pr<?php echo $template['issue']['priority'] ?>">
<h1>
<?php echo $this->html_issue_link($template['issue']['id']) ?>
<?php echo $template['issue']['type'] ?> (<?php echo $template['issue']['state'] ?>)
</h1>

<h1><?php echo $template['issue']['title'] ?></h1>

<div class="bez_timebox">
<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($template['issue']['date']) ?></span>
<?php if ($template['issue']['raw_state'] == 1): ?>
	<span>
		<strong><?php echo $bezlang['closed'] ?>: </strong>
		<?php echo $helper->time2date($template['issue']['last_mod']) ?>
	</span>
<?php endif ?>
</div>

<table>
<tr>
<td>
	<strong><?php echo $bezlang['coordinator'] ?>:</strong>
	<?php echo $template['issue']['coordinator'] ?>
</td>
</tr>
</table>

<?php echo $template['issue']['description'] ?>

<?php if ($template['issue']['raw_state'] == 1 || $template['issue']['raw_state'] == 2): ?>
<h2>
	<?php if ($template['anytasks']): ?>
		<?php echo $bezlang['opinion'] ?>
	<?php else: ?>
		<?php echo $bezlang['reason'] ?>
	<?php endif ?>
</h2>
	<?php echo $template['issue']['opinion'] ?>
<?php endif ?>

<?php if (!$template['close']): ?>
<div class="bez_buttons">
	<?php if ($helper->user_coordinator($template['issue']['id'])): ?>
		<?php if ($template['issue']['raw_state'] == 1): ?>
			<a href="?id=<?php echo $this->id('issue', 'id', $template['issue']['id'], 'action', 'reopen') ?>" class="bds_inline_button">
			 	↺ <?php echo $bezlang['issue_reopen'] ?>
			</a>
		<?php elseif ($template['anytasks'] && !$template['opentasks'] && !$template['cause_without_task']): ?>
			<a href="?id=<?php echo $this->id('issue_close', 'id', $template['issue']['id']) ?>" class="bds_inline_button">
			 	↬ <?php echo $bezlang['close_issue'] ?>
			</a>
		<?php elseif (!$template['anytasks']): ?>
			<a href="?id=<?php echo $this->id('issue_close', 'id', $template['issue']['id']) ?>" class="bds_inline_button">
			 	↛ <?php echo $bezlang['reject_issue'] ?>
			</a>
		<?php endif ?>
	<?php endif ?> 	
	
	<?php if ($helper->user_coordinator($template['issue']['id'])): ?> 
		<a href="?id=<?php echo $this->id('issue_report', 'id', $template['issue']['id']) ?>" class="bds_inline_button">
		 	✎ <?php echo $bezlang['edit'] ?>
		</a>
	<?php endif ?>

	<a class="bds_inline_button" href="
		<?php echo $helper->mailto($template['issue']['coordinator_email'],
		$bezlang['issue'].': #'.$template['issue']['id'].' '.$template['issue']['title'],
		DOKU_URL . 'doku.php?id='.$this->id('issue', 'id', $template['issue']['id'])) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>

	<a href="<?php echo $helper->link_8d($template[issue][id]) ?>" class="bds_inline_button bds_report_button">
		⎙ <?php echo $bezlang['8d_report'] ?>
	</a>

	<a href="<?php echo $helper->link_rr($template[issue][id]) ?>" class="bds_inline_button bds_report_button">
		⎚ <?php echo $bezlang['rr_report'] ?>
	</a>
</div>
<?php else: ?>
<h2>
	<?php if ($template['anytasks']): ?>
		<?php echo $bezlang['opinion'] ?>
	<?php else: ?>
		<?php echo $bezlang['reason'] ?>
	<?php endif ?>
</h2>
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('issue_close', 'id', $template['issue_id'], 'action', 'close') ?>" method="POST" class="bez_form">
	<textarea name="opinion" id="opinion" class="edit"><?php echo $value['opinion'] ?></textarea>
	<?php if ($template['anytasks']): ?>
		<input type="submit" value="<?php echo $bezlang['close_issue'] ?>">
	<?php else: ?>
		<input type="submit" value="<?php echo $bezlang['reject_issue'] ?>">
	<?php endif ?>
	 <a href="#" onclick="window.history.back()" class="bez_delete_button bez_link_button bez_cancel_button">
		<?php echo $bezlang['cancel'] ?>
	</a>
</form>
<?php endif ?>
</div>
