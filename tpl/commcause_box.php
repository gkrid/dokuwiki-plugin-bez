<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<a id="k<?php echo $tpl->get('thread_comment')->id ?>"></a>
<div class="bez_comment
	<?php echo $tpl->get('thread_comment')->type == 'comment' ? 'bez_type_0' : 'bez_cause' ?>
	<?php
		if ($tpl->get('thread_comment')->author == $tpl->current_user()) {
			echo 'bez_my_comment';
		}
	?>">
	<div class="bez_avatar">
		<img src="<?php echo DOKU_URL ?>lib/plugins/bez/images/avatar_default.png" />
	</div>
	<div class="bez_text_comment">
		<span class="bez_arrow-tip-container ">
			<span class="bez_arrow-tip ">
				<span class="bez_arrow-tip-grad"></span>
			</span>
		</span>
		<div class="commcause_content">
			<h2>
                <a href="#k<?php echo $tpl->get('thread_comment')->id ?>">#k<?php echo $tpl->get('thread_comment')->id ?></a>
                <strong><?php echo $tpl->user_name($tpl->get('thread_comment')->author) ?></strong>

                <?php if ($tpl->get('thread_comment')->type == 'comment'): ?>
                    <?php echo $tpl->getLang('comment_added') ?>
                <?php else: ?>
                    <?php echo $tpl->getLang('cause_added') ?>
                <?php endif ?>
                <?php echo $tpl->datetime($tpl->get('thread_comment')->create_date) ?>
                
                <?php if (strpos($tpl->get('thread_comment')->type, 'cause') === 0): ?>
                    <span style="color: #000;">
                        (<?php echo $tpl->getLang($tpl->get('thread_comment')->type) ?>)
                    </span>
                <?php endif ?>

                <?php if ($tpl->param('kid') != $tpl->get('thread_comment')->id): ?>
                    <div class="bez_comment_buttons">
                        <?php if (  $tpl->get('thread')->can_add_comments() &&
                                    count($tpl->get('thread_comment')->changable_fields()) > 0): ?>
                            <a class="bez_comment_button"
                               href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'commcause_edit', 'kid', $tpl->get('thread_comment')->id) ?>#k_">
                                <span class="bez_awesome">&#xf040;</span>
                            </a>
                            <?php if (  $tpl->get('thread_comment')->acl_of('id') >= BEZ_PERMISSION_DELETE &&
                                        $tpl->get('thread_comment')->task_count == 0): ?>
                                <a class="bez_comment_button bez_commcause_delete_prompt"
                                   data-kid="<?php echo $tpl->get('thread_comment')->id ?>"
                                   href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'commcause_delete', 'kid', $tpl->get('thread_comment')->id) ?>">
                                    <span class="bez_awesome">&#xf00d;</span>
                                </a>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>
			</h2>
			<div class="bez_content">
				<?php echo $tpl->get('thread_comment')->content_html; ?>
            </div>
		</div>
		
		<?php if (strpos($tpl->get('thread_comment')->type, 'cause') === 0): ?>
        <?php if ($tpl->get('tasks ' . $tpl->get('thread_comment')->id) == '')
            $tpl->set('causes_without_tasks', true) ?>
		<div style="margin-top: 10px; margin-left: 40px">
			<?php foreach ($tpl->get('tasks ' . $tpl->get('thread_comment')->id, array()) as $task): ?>
				<?php $tpl->set('task', $task) ?>
				<?php if (	$tpl->param('action') == 'task_edit' &&
                            $tpl->param('tid') == $task->id): ?>
					<?php include 'task_form.php' ?>
				<?php else: ?>
					<?php include 'task_box.php' ?>
				<?php endif ?>
			<?php endforeach ?>
			<?php if ($tpl->get('thread')->user_is_coordinator()): ?>
				<?php if (	$tpl->param('action') == 'task_add' &&
                            $tpl->param('kid') == $tpl->get('thread_comment')->id): ?>
					<?php include 'task_form.php' ?>
				<?php elseif (	$tpl->get('thread_comment')->type != 'comment' &&
                                $tpl->get('thread')->state == 'opened' &&
                                $tpl->param('action') != 'task_edit'): ?>
						<div class="bez_second_lv_buttons" style="margin-top:10px">
							<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'kid', $tpl->get('thread_comment')->id, 'action', 'task_add') ?>#z_" class="bez_subscribe_button">
								<span class="bez_awesome">&#xf0fe;</span>&nbsp;&nbsp;
								<?php if ($tpl->get('thread_comment')->type == 'cause_real'): ?>
									<?php echo $tpl->getLang('corrective_action_add') ?>
								<?php else: ?>
									<?php echo $tpl->getLang('preventive_action_add') ?>
								<?php endif ?>
							</a>
						</div>
					<?php endif ?>
			<?php endif ?>
		</div>
		<?php endif ?>
        
	</div>
</div>			
