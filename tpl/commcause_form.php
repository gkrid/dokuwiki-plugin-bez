<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<?php $url = $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action',
	$tpl->param('action') == 'commcause_edit' ? 'commcause_edit' : 'commcause_add',
'kid', $tpl->param('kid')) ?>
<a id="k_"></a>
<form class="bez_form_blank" action="<?php echo $url ?>" method="POST">
	<input type="hidden" name="id" value="">
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
				<?php if ($tpl->static_acl('thread_comment', 'type') >= BEZ_PERMISSION_CHANGE): ?>
				<ul class="bez_tabs">
					<li
                        <?php if (  $tpl->value('type') == '' ||
                                    $tpl->value('type') == 'comment') echo 'class="active"' ?>
                        <?php if (  $tpl->param('kid') !== '' &&
                                    $tpl->get('thread_comment')->task_count > 0)
                                echo 'style="display:none;"';
                        ?>
                    >
                        <a href="#comment"><?php echo $tpl->getLang('comment_noun') ?></a>
                    </li>
					<li <?php if(strpos($tpl->value('type'), 'cause') === 0) echo 'class="active"' ?>>
						<a href="#cause"><?php echo $tpl->getLang('cause_noun') ?></a>
                    </li>
				</ul>
				<?php endif ?>
				<div class="bez_toolbar"></div>
			</h2>
			</div>
			<div class="bez_content">
				<textarea data-validation="required" name="content" class="bez_textarea_content" id="content1"><?php echo $tpl->value('content') ?></textarea>
				
				<input class="bez_comment_type" type="hidden" name="type" value="comment" />
				<?php if ($tpl->static_acl('thread_comment', 'type') >= BEZ_PERMISSION_CHANGE): ?>
					<div class="bez_cause_type">
						<div style="margin-bottom: 10px;">
						<label for="potential">
							<?php echo $tpl->getLang('cause_type') ?>:
						</label>
                        <label>
                            <input type="radio" name="type" value="cause_real"
                                <?php if($tpl->value('type') != 'cause_potential') echo 'checked' ?>/>
                                <?php echo $tpl->getLang('cause_type_default') ?>
						</label>
						&nbsp;&nbsp;
                        <label>
                            <input type="radio" name="type" value="cause_potential"
                                <?php if($tpl->value('type') == 'cause_potential') echo 'checked' ?>/>
                                <?php echo $tpl->getLang('cause_type_potential') ?>
                        </label>
					   </div>
                    </div>
				<?php endif ?>
				<input type="submit" value="<?php echo $tpl->param('kid') != '' ? $tpl->getLang('correct') : $tpl->getLang('add') ?>">
				 <a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id) ?><?php if ($tpl->param('kid') !== '-1') echo '#k'.$tpl->param('kid') ?>" class="bez_delete_button bez_link_button bez_cancel_button">
					<?php echo $tpl->getLang('cancel') ?>
				</a>
		</div>
        <?php if (  $tpl->param('kid') !== '-1' &&
                    $tpl->get('thread_comment')->task_count > 0): ?>
            <div style="margin-top: 10px; margin-left: 40px">
                <?php foreach ($tpl->get('thread_comment')->get_tasks() as $task): ?>
                    <?php $tpl->set('task', $task) ?>
                    <?php if (	$tpl->action() == 'task_edit' &&
                                $tpl->param('kid') == $task->id): ?>
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
