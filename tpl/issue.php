
<?php include "issue_box.php" ?>


<!-- Comments -->
<div class="bez_comments">
	<div class="bez_left_col">
		<!-- Correction -->
		<div class="bds_block" id="bez_tasks" style="margin-top: 10px">
		<?php foreach ($template['corrections'] as $task): ?>
			<?php include "task.php" ?>
		<?php endforeach ?>
		</div>
		
		<div class="bez_second_lv_buttons" style="margin-top: 10px">
			<?php if ($helper->user_editor()): ?>
				<a href="?id=<?php echo $this->id('issue_report') ?>" class="bez_subscribe_button">
					<span class="bez_awesome">&#xf0fe;</span>&nbsp;&nbsp;<?php echo $bezlang['correction_add'] ?>
				</a>
			<?php endif ?>
			<a href="?id=<?php echo $this->id('issues:state:0:coordinator:'.$template['client']) ?>" class="bez_subscribe_button">
				<span class="bez_awesome">&#xf070;</span>&nbsp;&nbsp;<?php echo $bezlang['hide_comments'] ?>
			</a>
		</div>
		
		<?php foreach ($template['commcauses'] as $commcause): ?>
			<a name="k<?php echo $commcause->id ?>"></a>
			<div id="k<?php echo $commcause->id ?>" class="bez_comment">
			<div class="bez_comment
				<?php
					if ($commcause->type > 0) {
						echo "bez_cause";
					}
				?>
				<?php
					if ($commcause->reporter == $this->model->users->get_user_full_name($INFO['client'])) {
						echo "bez_my_comment";
					}
				?>">
				<div class="bez_avatar">
					<img src="<?php echo DOKU_URL ?>lib/plugins/bez/images/avatar_default.png" />
				</div>
				<div class="bez_text_comment">
					<span class="bez_arrow-tip-container">
						<span class="bez_arrow-tip">
							<span class="bez_arrow-tip-grad"></span>
						</span>
					</span>
					<h2>
						<strong><?php echo $this->model->users->get_user_full_name($commcause->reporter) ?></strong>
						<?php echo $bezlang['comment_added'] ?>
						<?php echo $commcause->datetime ?>
					
					<div class="bez_comment_buttons">
					<?php if ($template['issue']['raw_state'] == 0 &&
						($commcause->reporter == $INFO['client'] ||
						$helper->user_coordinator($template['issue']['id']))): ?> 
						
						<a class="bez_comment_button"
						href="?id=<?php echo $this->id('issue', 'id', $template['issue']['id'], 'action', 'commcause_edit', 'kid', $commcause->id) ?>#k_">
							<span class="bez_awesome">&#xf040;</span>
						</a>
						<a class="bez_comment_button bez_delete_prompt"
						href="?id=<?php echo $this->id('issue', 'id', $template['issue']['id'], 'action', 'commcause_delete', 'kid', $commcause->id) ?>">
							<span class="bez_awesome">&#xf00d;</span>
						</a>
					<?php endif ?>
					</div>
			
					</h2>
					<div class="bez_content">
						<?php echo $commcause->content_cache; ?>
					</div>
					<?php if (true && $commcause->type > 0): ?>
						<div class="bez_second_lv_buttons" style="margin-top: 5px; margin-left: 14px">
						<?php if ($commcause->type == 1): ?>
								<a href="?id=<?php echo $this->id('issue_report') ?>" class="bez_subscribe_button">
									<span class="bez_awesome">&#xf0fe;</span>&nbsp;&nbsp;<?php echo $bezlang['corrective_action_add'] ?>
								</a>
						<?php elseif ($commcause->type == 2): ?>
	
								<a href="?id=<?php echo $this->id('issue_report') ?>" class="bez_subscribe_button">
									<span class="bez_awesome">&#xf0fe;</span>&nbsp;&nbsp;<?php echo $bezlang['preventive_action_add'] ?>
								</a>
						<?php endif ?>
						</div>
					<?php endif ?>
				</div>
			</div>			
			</div>
		<?php endforeach ?>

<?php if ($template['issue_object']->state === '0' && $helper->user_editor()): ?> 

<div id="bez_tabs_issue_forms">

<div id="bez_comment_form">
	<?php $id = $this->id('issue', 'id', $template['issue_object']->id, 'action', 'commcause_add') ?>
	<form action="?id=<?php echo $id ?>#bez_comment_form" method="POST">
		<input type="hidden" name="id" value="<?php echo $id ?>">
		<input type="hidden" name="type" value="0" />
		<div class="bez_comment bez_comment_form">
			<div class="bez_avatar">
				<img src="<?php echo DOKU_URL ?>lib/plugins/bez/images/avatar_default.png" />
			</div>
			<div class="bez_text_comment">
				<span class="bez_arrow-tip-container">
					<span class="bez_arrow-tip">
						<span class="bez_arrow-tip-grad"></span>
					</span>
				</span>
				<h2>
					<ul class="bez_tabs">
						<li class="active"><a href="#bez_comment_form"><?php echo $bezlang['comment_noun'] ?></a></li>
						<li><a href="#bez_cause_form"><?php echo $bezlang['cause_noun'] ?></a></li>
					</ul>
					<div class="bez_toolbar"></div>
				</h2>
				<div class="bez_content">
					<textarea name="content" class="bez_textarea_content" id="content1"><?php echo $value['content'] ?></textarea>
					<input type="submit" value="<?php echo $template['comment_button'] ?>">
				</div>
			</div>
		</div>
	</form>
</div>

<div id="bez_cause_form">
	<?php $id = $this->id('issue', 'id', $template['issue_object']->id, 'action', 'commcause_add') ?>
	<form action="?id=<?php echo $this->id('issue', 'id', $template['issue_object']->id, 'action', 'commcause_add') ?>#bez_cause_form" method="POST">
		<input type="hidden" name="id" value="<?php echo $id ?>">
		<div class="bez_comment bez_comment_form bez_cause">
			<div class="bez_avatar">
				<img src="<?php echo DOKU_URL ?>lib/plugins/bez/images/avatar_default.png" />
			</div>
			<div class="bez_text_comment">
				<span class="bez_arrow-tip-container">
					<span class="bez_arrow-tip">
						<span class="bez_arrow-tip-grad"></span>
					</span>
				</span>
				<h2>
					<ul class="bez_tabs">
						<li><a href="#bez_comment_form"><?php echo $bezlang['comment_noun'] ?></a></li>
						<li class="active"><a href="#bez_cause_form"><?php echo $bezlang['cause_noun'] ?></a></li>
					</ul>
					<div class="bez_toolbar"></div>
				</h2>
				<div class="bez_content">
					<textarea name="content" class="bez_textarea_content" id="content2"><?php echo $value['content'] ?></textarea>
					<div style="margin-bottom: 10px;">
					<label for="potential">
						<?php echo $bezlang['cause_type'] ?>:
					</label>
					<input type="radio" name="type" value="1"
						<?php if($value['potential'] == 0) echo 'checked' ?>/>
						<?php echo $bezlang['cause_type_default'] ?>
					</label>
					&nbsp;&nbsp;
					<input type="radio" name="type" value="2"
						<?php if($value['potential'] == 1) echo 'checked' ?>/>
						<?php echo $bezlang['cause_type_potential'] ?>
					</div>
					<input type="submit" value="<?php echo $template['comment_button'] ?>">
				</div>
			</div>
		</div>
	</form>
</div>

</div>
	
<?php endif ?>

</div>
<div class="bez_right_col">
	
<div class="bez_box">
<h2><?php echo $bezlang['comment_last_activity'] ?></h2>
<?php echo $template['issue_object']->last_activity ?>
</div>

<div class="bez_box bez_subscribe_box">
<h2><?php echo $bezlang['norifications'] ?></h2>
<?php if ($template['issue_object']->is_subscribent()): ?>
	<a href="?id=<?php echo $this->id('issue', 'id', $template['issue_object']->id, 'action', 'unsubscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf1f6;</span>&nbsp;&nbsp;<?php echo $bezlang['unsubscribe'] ?></a>
	<p><?php echo $bezlang['subscribed_info'] ?></p>
<?php else: ?>
	<a href="?id=<?php echo $this->id('issue', 'id', $template['issue_object']->id, 'action', 'subscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf0f3;</span>&nbsp;&nbsp;<?php echo $bezlang['subscribe'] ?></a>
	<p><?php echo $bezlang['not_subscribed_info'] ?></p>
<?php endif ?>

</div>

<div class="bez_box">
<h2><?php echo $bezlang['comment_participants'] ?></h2>
<?php foreach ($template['issue_object']->get_participants_names() as $participant): ?>
	<div>
		<?php echo $participant ?>
	</div>
<?php endforeach ?>
</div>


</div>

</div>	
