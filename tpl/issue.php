<?php if ($template['action'] === 'issue_edit_metadata'): ?>
    <?php $id = $this->id('issue', 'id', $template['issue']->id, 'action', 'issue_edit_metadata') ?>
    <form class="bez_metaform" action="?id=<?php echo $id ?>" method="POST">
<?php endif ?>

<?php include "issue_box.php" ?>

<!-- Comments -->
<div class="bez_comments">
	<div class="bez_left_col">
		<!-- Correction -->
		<div style="margin-top: 10px">
			<?php foreach ($template['corrections'] as $task): ?>
				<?php $template['task'] = $task ?>
				<?php if (	$template['action'] === 'task_edit' &&
							$template['tid'] === $template['task']->id): ?>
					<?php include 'task_form.php' ?>
				<?php else: ?>
					<?php include 'task_box.php' ?>
				<?php endif ?>
				
			<?php endforeach ?>
			<?php if ($template['action'] === 'task_correction_add'): ?>
				<?php include 'task_form.php' ?>
			<?php endif ?>
		</div>

		<div class="bez_second_lv_buttons" style="margin-top: 10px">
			<?php if (	$template['issue']->user_is_coordinator() &&
						$template['issue']->full_state() === '0'): ?>
				<a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'action', 'task_correction_add') ?>#z_" class="bez_subscribe_button">
					<span class="bez_awesome">&#xf0fe;</span>&nbsp;&nbsp;<?php echo $bezlang['correction_add'] ?>
				</a>
			<?php endif ?>
			<a href="#" class="bez_subscribe_button bez_hide_comments">
				<span class="bez_awesome">&#xf070;</span>&nbsp;&nbsp;<?php echo $bezlang['hide_comments'] ?>
			</a>
			<a href="#" class="bez_subscribe_button bez_show_comments">
				<span class="bez_awesome">&#xf06e;</span>&nbsp;&nbsp;<?php echo $bezlang['show_comments'] ?>
			</a>
		</div>
		
		<?php foreach ($template['commcauses'] as $commcause): ?>
			<?php $template['commcause'] = $commcause ?>
			<?php if (	$template['action'] === 'commcause_edit' &&
						$template['kid'] === $template['commcause']->id): ?>
				<?php include 'commcause_form.php' ?>
			<?php else: ?>
				<?php include 'commcause_box.php' ?>
			<?php endif ?>
		<?php endforeach ?>

<?php if (	$template['issue']->state === '0' &&
			!(strpos($template['action'], 'task') === 0) &&
			$template['action'] !== 'issue_close' &&
            $template['action'] !== 'issue_edit_metadata' &&
			$template['kid'] === '-1'): ?> 

<?php include 'commcause_form.php' ?>
	
<?php endif ?>

</div>
<div class="bez_right_col">
	
<div class="bez_box">
<h2><?php echo $bezlang['comment_last_activity'] ?></h2>

<?php if ($template['action'] === 'issue_edit_metadata'): ?>
<div style="white-space: nowrap;">
    <input name="last_activity_date" style="width:90px;" data-validation="required,date" value="<?php echo $value['last_activity_date'] ?>" />
                          <?php echo $this->model->action->getLang('at_hour') ?>
     <input name="last_activity_time" style="width:60px;" data-validation="required,custom" data-validation-regexp="^(\d{1,2}):(\d{1,2}):(\d{1,2})$" value="<?php echo $value['last_activity_time'] ?>" />
</div>
<?php else: ?>
    <?php echo $template['issue']->last_activity ?>
<?php endif ?>


</div>

<div class="bez_box bez_subscribe_box">
<h2><?php echo $bezlang['norifications'] ?></h2>
<?php if ($template['issue']->is_subscribent()): ?>
	<a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'action', 'unsubscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf1f6;</span>&nbsp;&nbsp;<?php echo $bezlang['unsubscribe'] ?></a>
	<p><?php echo $bezlang['subscribed_info'] ?></p>
<?php else: ?>
	<a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'action', 'subscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf0f3;</span>&nbsp;&nbsp;<?php echo $bezlang['subscribe'] ?></a>
	<p><?php echo $bezlang['not_subscribed_info'] ?></p>
<?php endif ?>

</div>

<div class="bez_box">
<h2><?php echo $bezlang['comment_participants'] ?></h2>
<ul id="issue_participants">
<?php foreach ($template['issue']->get_participants() as $nick => $participant): ?>
	<li><a href="<?php echo $helper->mailto($this->model->users->get_user_email($nick),
		$bezlang['issue'].': #'.$template['issue']->id.' '.$template['issue']->title,
		DOKU_URL . 'doku.php?id='.$this->id('issue', 'id', $template['issue']->id)) ?>"  title="<?php echo $nick ?>">
		<span class="bez_name"><?php echo $participant ?></span>
		<span class="bez_icons">
		<?php if($template['issue']->reporter === $nick): ?>
			<span class="bez_awesome"
				title="<?php echo $bezlang['reporter'] ?>">
				&#xf058;
			</span>
		<?php endif ?>
		<?php if($template['issue']->coordinator === $nick): ?>
			<span class="bez_awesome"
				title="<?php echo $bezlang['coordinator'] ?>">
				&#xf0e3;
			</span>
		<?php endif ?>
		<?php if($template['issue']->is_task_executor($nick)): ?>
			<span class="bez_awesome"
				title="<?php echo $bezlang['executor'] ?>">
				&#xf073;
			</span>
		<?php endif ?>
		<?php if($template['issue']->is_commentator($nick)): ?>
			<span class="bez_awesome"
				title="<?php echo $bezlang['commentator'] ?>">
				&#xf27a;
			</span>
		<?php endif ?>
		<?php if($template['issue']->is_subscribent($nick)): ?>
			<span class="bez_awesome"
				title="<?php echo $bezlang['subscribent'] ?>">
				&#xf0e0;
			</span>
		<?php endif ?>
		</span>
	</a></li>
<?php endforeach ?>
</ul>

<?php if (	$template['issue']->user_is_coordinator() &&
						$template['issue']->full_state() === '0' &&
                        $template['action'] !== 'issue_edit_metadata'): ?>
    <h2><?php echo $bezlang['issue_invite_header'] ?></h2>
    <form action="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'action', 'invite') ?>" method="post" id="bez_invite_users_form">
    <div id="bez_invite_users" class="ui-widget">
        <select name="client">
            <option value="">--- <?php echo $bezlang['select'] ?> ---</option>
            <?php foreach ($template['users_to_invite'] as $nick => $name): ?>
                <option value="<?php echo $nick ?>"><?php echo $name ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <button class="bez_subscribe_button"><?php echo $bezlang['issue_invite_button'] ?></button>
    </form>
<?php endif ?>


</div>


</div>

</div>	

<?php if ($template['action'] === 'issue_edit_metadata'): ?>
    </form>
<?php endif?>
