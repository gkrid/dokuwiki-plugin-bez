<a id="k<?php echo $template['commcause']->id ?>"></a>
<div class="bez_comment
	<?php echo $template['commcause']->type === '0' ? 'bez_type_0' : 'bez_cause' ?>
	<?php
		if ($template['commcause']->reporter ==
                $this->model->users->get_user_full_name($this->model->user_nick)) {
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
            <?php if (  $template['action'] === 'commcause_edit_metadata' &&
                        $template['kid'] === $template['commcause']->id): ?>
                <?php $id = $this->id('issue', 'id', $template['issue']->id, 'action', 'commcause_edit_metadata', 'kid', $template['kid']) ?>
                <form   class="bez_metaform"
                        action="?id=<?php echo $id ?>" method="POST">
            <?php endif ?>
			<h2>
                <?php if (  $template['action'] === 'commcause_edit_metadata' &&
                            $template['kid'] === $template['commcause']->id &&
                            $template['commcause']->acl_of('reporter') >= BEZ_PERMISSION_CHANGE): ?>
                     <select name="reporter" id="reporter" data-validation="required">
                        <option value="">--- <?php echo $bezlang['select'] ?>---</option>
                        <?php foreach ($template['users'] as $nick => $name): ?>
                            <option <?php if ($value['reporter'] === $nick) echo 'selected' ?>
                            value="<?php echo $nick ?>"><?php echo $name ?></option>
                        <?php endforeach ?>
                    </select>       
                <?php else: ?>
                    <strong><?php echo $this->model->users->get_user_full_name($template['commcause']->reporter) ?></strong>
                <?php endif ?>
                
                
                <?php if ($template['commcause']->type > 0): ?>
				    <?php echo $bezlang['cause_added'] ?>
                <?php else: ?>
                    <?php echo $bezlang['comment_added'] ?>
                <?php endif ?>
                
                <?php if (  $template['action'] === 'commcause_edit_metadata' &&
                            $template['kid'] === $template['commcause']->id &&
                            $template['commcause']->acl_of('reporter') >= BEZ_PERMISSION_CHANGE): ?>
                      <input name="date" style="width:90px;" data-validation="required,date" value="<?php echo $value['date'] ?>" />
                      <?php echo $this->model->action->getLang('at_hour') ?>
                     <input name="time" style="width:60px;" data-validation="required,custom" data-validation-regexp="^(\d{1,2}):(\d{1,2})$" value="<?php echo $value['time'] ?>" />
                <?php else: ?>
                    <?php echo $template['commcause']->date_format($template['commcause']->datetime) ?>
                <?php endif ?>
                
                <?php if ($template['commcause']->type === '1'): ?>
                    <span style="color: #000;">
                        (<?php echo lcfirst($bezlang['cause_type_default']) ?>)
                    </span>
                <?php elseif ($template['commcause']->type === '2'): ?>
                    <span style="color: #000;">
                        (<?php echo lcfirst($bezlang['cause_type_potential']) ?>)
                    </span>
                <?php endif ?>
			
            <?php if (  $template['action'] !== 'commcause_edit_metadata' ||
                            $template['kid'] !== $template['commcause']->id): ?>
                <div class="bez_comment_buttons">
                    
                    <a class="bds_inline_button_noborder"
                    href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id, 'action', 'commcause_edit_metadata', 'kid', $template['commcause']->id) ?>#k<?php echo $template['commcause']->id; ?>">Edytuj metadane</a>
                <?php if (
                    (!isset($template['no_edit']) || $template['no_edit'] === false) &&
                    $template['issue']->state === '0' &&
                    (   ($template['commcause']->type === '0' &&
                         $template['commcause']->reporter == $this->model->user_nick) ||
                            $template['issue']->user_is_coordinator())
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
            <?php endif ?>

			</h2>
			<div class="bez_content 
            <?php if (  $template['action'] === 'commcause_edit_metadata' &&
                $template['kid'] === $template['commcause']->id) echo 'bez_metadata_edit_warn' ?>">
                <?php if (  $template['action'] === 'commcause_edit_metadata' &&
                            $template['kid'] === $template['commcause']->id): ?>
                    <h1 style="color: #f00; border-bottom: 1px solid #f00; font-size: 15px;"><?php echo $bezlang['metadata_edit_header'] ?></h1>
     
                <?php endif ?>
                
				<?php echo $template['commcause']->content_cache; ?>
                
                <?php if (  $template['action'] === 'commcause_edit_metadata' &&
                            $template['kid'] === $template['commcause']->id): ?>
                    <input type="submit" value="<?php echo $bezlang['save'] ?>">&nbsp;&nbsp;
                    <a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id).'#k'.$template['commcause']->id ?>"
                         class="bez_delete_button bez_link_button">
                            <?php echo $bezlang['cancel'] ?>
                    </a> 
                <?php endif ?>
			</div>
		</div>
		
		<?php if (isset($template['commcauses_tasks'][$template['commcause']->id])): ?>
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
                                $template['commcause']->type !== '0' &&
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
        
        <?php if (  $template['action'] === 'commcause_edit_metadata' &&
                        $template['kid'] === $template['commcause']->id): ?>
            </form>
        <?php endif ?>
	</div>
</div>			
