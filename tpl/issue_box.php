<div id="bds_issue_box" class="pr<?php echo $template['issue']->priority ?>">
<h1>

<a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id) ?>">
    #<?php echo $template['issue']->id ?>
</a>

<?php if ($template['issue']->type_string !== NULL): ?>
	<?php echo $template['issue']->type_string ?>
<?php else: ?>
	<i style="color: #777"><?php echo $bezlang['issue_type_no_specified'] ?></i>
<?php endif ?>

(<?php echo $template['issue']->state_string ?>)

<span style="color: #777; font-weight: normal; font-size: 90%;">
    <?php echo $bezlang['coordinator'] ?>:
    <span style="font-weight: bold;">
        <?php if ($template['issue']->coordinator === '-proposal'): ?>
            <i style="font-weight: normal;"><?php echo $bezlang['proposal'] ?></i>
        <?php else: ?>
            <?php echo $this->model->users->get_user_full_name($template['issue']->coordinator) ?>
        <?php endif?>
    </span>
</span>
</h1>

<h1 id="bez_issue_title"><?php echo $template['issue']->title ?></h1>

<div class="bez_timebox">
<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($template['issue']->date) ?></span>
<?php if ($template['issue']->state !== '0'): ?>
	<span>
		<strong><?php echo $bezlang['closed'] ?>: </strong>
		<?php echo $helper->time2date($template['issue']->last_mod) ?>
	</span>
	<span>
		<strong><?php echo $bezlang['report_priority'] ?>: </strong>
		<?php echo $helper->days((int)$template['issue']->last_mod - (int)$template['issue']->date) ?>
	</span>
<?php endif ?>
</div>

<?php echo $template['issue']->description_cache ?>

<?php if ($template['issue']->state !== '0'): ?>
<h2>
	<?php if ($template['issue']->state === '1'): ?>
		<?php echo $bezlang['opinion'] ?>
	<?php else: ?>
		<?php echo $bezlang['reason'] ?>
	<?php endif ?>
</h2>
	<?php echo $template['issue']->opinion_cache ?>
<?php endif ?>

<?php if (	$template['action'] === 'issue_close' ||
			$template['action'] === 'issue_close_confirm'): ?>
<h2>
	<?php if ($template['issue']->assigned_tasks_count > 0): ?>
		<?php echo $bezlang['opinion'] ?>
	<?php else: ?>
		<?php echo $bezlang['reason'] ?>
	<?php endif ?>
</h2>
<?php $id = $this->id('issue', 'id', $template['issue']->id, 'action', 'issue_close_confirm') ?>
<form action="?id=<?php echo $id ?>" method="POST" class="bez_form">
	<input type="hidden" name="id" value="<?php echo $id ?>">
	<div class="bez_opinion_toolbar"></div>
	<textarea name="opinion" id="opinion" class="edit"><?php echo $value['opinion'] ?></textarea>
	<?php if ($template['issue']->assigned_tasks_count > 0): ?>
		<input type="hidden" name="state" value="1" />
		<input type="submit" value="<?php echo $bezlang['close_issue'] ?>">
	<?php else: ?>
	<input type="hidden" name="state" value="2" />
		<input type="submit" value="<?php echo $bezlang['reject_issue'] ?>">
	<?php endif ?>
	 <a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id) ?>" class="bez_delete_button bez_link_button bez_cancel_button">
		<?php echo $bezlang['cancel'] ?>
	</a>
</form>
<?php else: ?>
	<?php if ($template['issue']->opened_tasks_count > 0): ?>
		<div class="info"><?php echo $bezlang['issue_unclosed_tasks'] ?></div>
	<?php endif ?>
	<?php if ($template['issue']->coordinator === '-proposal'): ?>
		<div class="info"><?php echo $bezlang['issue_is_proposal'] ?></div>
	<?php endif ?>
	<?php if ($template['issue']->causes_without_tasks_count() > 0): ?>
		<div class="info"><?php echo $bezlang['cause_without_task'] ?></div>
	<?php endif ?>
	<?php if (	$template['issue']->assigned_tasks_count === 0 &&
				$template['issue']->state === '0'): ?>
		<div class="info"><?php echo $bezlang['issue_no_tasks'] ?></div>
	<?php endif ?>
<div class="bez_buttons">
	<?php if ((!isset($template['no_edit']) || $template['no_edit'] === false) &&                  $template['issue']->get_level() >= 15): ?> 
		<?php if ($template['issue']->state !== '0'): ?>
			<a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'action', 'reopen') ?>" class="bds_inline_button">
			 	↺ <?php echo $bezlang['issue_reopen'] ?>
			</a>
		<?php elseif (	$template['issue']->assigned_tasks_count > 0 &&
						$template['issue']->opened_tasks_count === 0 &&
						$template['issue']->causes_without_tasks_count() === 0): ?>
			<a href="?id=<?php echo $this->id('issue', 'action', 'issue_close', 'id', $template['issue']->id) ?>" class="bds_inline_button">
			 	↬ <?php echo $bezlang['close_issue'] ?>
			</a>
		<?php elseif ($template['issue']->assigned_tasks_count === 0): ?>
			<a href="?id=<?php echo $this->id('issue', 'action', 'issue_close', 'id', $template['issue']->id) ?>" class="bds_inline_button">
			 	↛ <?php echo $bezlang['reject_issue'] ?>
			</a>
		<?php endif ?>
	<?php endif ?> 	
	
	<?php if ((!isset($template['no_edit']) || $template['no_edit'] === false) &&                  $template['issue']->get_level() >= 15): ?> 
		<a href="?id=<?php echo $this->id('issue_report', 'action', 'edit', 'id', $template['issue']->id) ?>" class="bds_inline_button">
		 	✎ <?php echo $bezlang['edit'] ?>
		</a>
	<?php endif ?>

	<a class="bds_inline_button" href="
		<?php echo $helper->mailto($template['issue']->coordinator_email,
		$bezlang['issue'].': #'.$template['issue']->id.' '.$template['issue']->title,
		DOKU_URL . 'doku.php?id='.$this->id('issue', 'id', $template['issue']->id)) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>

	<a href="<?php echo $helper->link_8d($template['issue']->id) ?>" class="bds_inline_button bds_report_button">
		⎙ <?php echo $bezlang['8d_report'] ?>
	</a>
</div>
<?php endif ?>
</div>

