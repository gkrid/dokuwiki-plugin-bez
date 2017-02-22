<a id="k<?php echo $template['commcause']->id ?>"></a>
<div class="bez_comment
	<?php
		if ($template['commcause']->type > 0) {
			echo "bez_cause";
		} else {
			echo 'bez_type_0';
		}
	?>
	<?php
		if ($template['commcause']->reporter == $this->model->users->get_user_full_name($INFO['client'])) {
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
		<div class="commcause_content">
			<h2>
				<strong><?php echo $this->model->users->get_user_full_name($template['commcause']->reporter) ?></strong>
				<?php echo $bezlang['comment_added'] ?>
				<?php echo $template['commcause']->datetime ?>
			
			<div class="bez_comment_buttons">
			<?php if (
                (!isset($template['no_edit']) || $template['no_edit'] === false) &&
                $template['issue']->state === '0' &&
                (   ($template['commcause']->type === '0' &&
                     $template['commcause']->reporter == $INFO['client']) ||
						$template['issue']->get_level() >= 15)
				): ?> 
				
				<a class="bez_comment_button"
				href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'action', 'commcause_edit', 'kid', $template['commcause']->id) ?>#k_">
					<span class="bez_awesome">&#xf040;</span>
				</a>
                <?php if ($template['commcause']->tasks_count === 0): ?>
				<a class="bez_comment_button bez_commcause_delete_prompt"
					data-kid="<?php echo $template['commcause']->id ?>"
					href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'action', 'commcause_delete', 'kid', $template['commcause']->id) ?>">
					<span class="bez_awesome">&#xf00d;</span>
				</a>
                <?php endif ?>
			<?php endif ?>
			</div>

			</h2>
			<div class="bez_content">
				<?php echo $template['commcause']->content_cache; ?>
			</div>
		</div>
		
		<?php if (is_array($template['commcauses_tasks'][$template['commcause']->id])): ?>
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
			<?php if ($template['issue']->get_level() >= 15): ?>
				<?php if (	$template['action'] === 'task_commcause_add' &&
							$template['kid'] === $template['commcause']->id): ?>
					<?php include 'task_form.php' ?>
				<?php elseif (	(!isset($template['no_edit']) || $template['no_edit'] === false) &&
                                $template['commcause']->type !== '0' &&
                              	$template['issue']->state === '0' &&
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
