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
                    <strong><?php echo $tpl->user_name($tpl->get('thread_comment')->author) ?></strong>
                
                
                <?php if ($tpl->get('thread_comment')->type == 'comment'): ?>
                    <?php echo $tpl->getLang('comment_added') ?>
                <?php else: ?>
                    <?php echo $tpl->getLang('cause_added') ?>
                <?php endif ?>

                <?php echo dformat(strtotime($tpl->get('thread_comment')->create_date), '%Y-%m-%d %H:%M') ?>
                
                <?php if ($tpl->get('thread_comment')->type == 'cause_real'): ?>
                    <span style="color: #000;">
                        (<?php echo lcfirst($tpl->getLang('cause_type_default')) ?>)
                    </span>
                <?php elseif ($tpl->get('thread_comment')->type == 'cause_potential'): ?>
                    <span style="color: #000;">
                        (<?php echo lcfirst($tpl->getLang('cause_type_potential')) ?>)
                    </span>
                <?php endif ?>

                <?php if ($tpl->param('kid') != $tpl->get('thread_comment')->id): ?>
                    <div class="bez_comment_buttons">
                        <?php if (
                            ($tpl->get('no_edit') == '') &&
                             $tpl->get('thread')->state == 'opened' &&
                             (($tpl->get('thread_comment')->type == 'comment' &&
                                     $tpl->get('thread_comment')->author == $tpl->current_user()) ||
                                 $tpl->get('thread')->user_is_coordinator())
                        ): ?>

                            <a class="bez_comment_button"
                               href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'commcause_edit', 'kid', $tpl->get('thread_comment')->id) ?>#k_">
                                <span class="bez_awesome">&#xf040;</span>
                            </a>
                            <?php if ($tpl->get('thread_comment')->task_count == '0'): ?>
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
		
		<?php if (false): ?>
		<div style="margin-top: 10px; margin-left: 40px">
			<?php foreach ($template['commcauses_tasks'][$template['commcause']->id] as $task): ?>
				<?php $template['task'] = $task ?>
				<?php if (	$template['action'] === 'task_edit' &&
							$template['tid'] === $template['task']->id): ?>
					<?php include 'task_form.php' ?>
				<?php else: ?>
					<?php include 'task_box.php' ?>
				<?php endif ?>
			<?php endforeach ?>
			<?php if ($template['issue']->user_is_coordinator()): ?>
				<?php if (	$template['action'] === 'task_commcause_add' &&
							$template['kid'] === $template['commcause']->id): ?>
					<?php include 'task_form.php' ?>
				<?php elseif (	(!isset($template['no_edit']) ||
                                    $template['no_edit'] === false) &&
                                $template['commcause']->type !== 'comment' &&
                              	$template['issue']->full_state() === '0' &&
								$template['action'] !== 'task_edit'): ?>
						<div class="bez_second_lv_buttons">
							<a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'kid', $template['commcause']->id, 'action', 'task_commcause_add') ?>#z_" class="bez_subscribe_button">
								<span class="bez_awesome">&#xf0fe;</span>&nbsp;&nbsp;
								<?php if ($template['commcause']->type === '1'): ?>
									<?php echo $bezlang['corrective_action_add'] ?>
								<?php else: ?>
									<?php echo $bezlang['preventive_action_add'] ?>
								<?php endif ?>
							</a>
						</div>
					<?php endif ?>
			<?php endif ?>
		</div>
		<?php endif ?>
        
	</div>
</div>			
