<?php $id = $this->id('issue', 'id', $template['issue']->id, 'action',
	$template['action'] === 'commcause_edit' ? 'commcause_edit' : 'commcause_add',
'kid', $template['kid']) ?>
<a id="k_"></a>
<form action="?id=<?php echo $id ?>" method="POST">
	<input type="hidden" name="id" value="<?php echo $id ?>">
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
			<div class="commcause_content">
			<h2>
				<?php if ($template['commcause']->acl_of('type') >= BEZ_PERMISSION_CHANGE): ?> 
				<ul class="bez_tabs">
					<li
                        <?php if (!isset($value['type']) ||
                              $value['type'] === '0') echo 'class="active"' ?>
                        <?php if (  $template['kid'] !== '-1' &&
                                    $template['commcause']->tasks_count > 0)
                                echo 'style="display:none;"';
                        ?>
                    >
                        <a href="#comment"><?php echo $bezlang['comment_noun'] ?></a>
                    </li>
					<li <?php if($value['type'] === '1' || $value['type'] === '2') echo 'class="active"' ?>>
						<a href="#cause"><?php echo $bezlang['cause_noun'] ?></a>
                    </li>
				</ul>
				<?php endif ?>
				<div class="bez_toolbar"></div>
			</h2>
			</div>
			<div class="bez_content">
				<textarea data-validation="required" name="content" class="bez_textarea_content" id="content1"><?php echo $value['content'] ?></textarea>
				
				<input class="bez_comment_type" type="hidden" name="type" value="0" />
				<?php if ($template['commcause']->acl_of('type') >= BEZ_PERMISSION_CHANGE): ?> 
					<div class="bez_cause_type">
						<div style="margin-bottom: 10px;">
						<label for="potential">
							<?php echo $bezlang['cause_type'] ?>:
						</label>
                        <label>
                            <input type="radio" name="type" value="1"
                                <?php if(!isset($value['type']) || $value['type'] === '0' || $value['type'] === '1') echo 'checked' ?>/>
                                <?php echo $bezlang['cause_type_default'] ?>
						</label>
						&nbsp;&nbsp;
                        <label>
                            <input type="radio" name="type" value="2"
                                <?php if($value['type'] === '2') echo 'checked' ?>/>
                                <?php echo $bezlang['cause_type_potential'] ?>
                        </label>
					   </div>
                    </div>
				<?php endif ?>
				<input type="submit" value="<?php echo $template['kid'] !== '-1' ? $bezlang['correct'] : $bezlang['add'] ?>">
				 <a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id) ?><?php if ($template['kid'] !== '-1') echo '#k'.$template['kid'] ?>" class="bez_delete_button bez_link_button bez_cancel_button">
					<?php echo $bezlang['cancel'] ?>
				</a>
		</div>
        <?php if (  $template['kid'] !== '-1' &&
                    $template['commcause']->tasks_count > 0): ?> 
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
            </div>
        <?php endif ?>
    </div>
    </div>
</form>
