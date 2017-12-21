<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<a id="zk<?php echo $tpl->get('task_comment')->id ?>"></a>
<div class="bez_comment bez_type_0
	<?php
        if ($tpl->get('task_comment')->author == $tpl->current_user()) {
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
                <a href="#zk<?php echo $tpl->get('task_comment')->id ?>">#zk<?php echo $tpl->get('task_comment')->id ?></a>
                <strong><?php echo $tpl->user_name($tpl->get('task_comment')->author) ?></strong>
                <?php echo $tpl->getLang('comment_added') ?>
                <?php echo $tpl->datetime($tpl->get('task_comment')->create_date) ?>

                <?php if ($tpl->param('zkid') != $tpl->get('task_comment')->id): ?>
                    <div class="bez_comment_buttons">
                        <?php if (
                            $tpl->get('task')->state == 'opened' &&
                            $tpl->get('task_comment')->acl_of('id') >= BEZ_PERMISSION_CHANGE): ?>

                            <a class="bez_comment_button"
                               href="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id, 'action', 'comment_edit', 'zkid', $tpl->get('task_comment')->id) ?>#zk_">
                                <span class="bez_awesome">&#xf040;</span>
                            </a>
                            <?php if ($tpl->get('task_comment')->acl_of('id') >= BEZ_PERMISSION_DELETE): ?>
                                <a class="bez_comment_button bez_commcause_delete_prompt"
                                   data-kid="<?php echo $tpl->get('task_comment')->id ?>"
                                   href="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id, 'action', 'comment_delete', 'zkid', $tpl->get('task_comment')->id) ?>">
                                    <span class="bez_awesome">&#xf00d;</span>
                                </a>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>
            </h2>
            <div class="bez_content">
                <?php echo $tpl->get('task_comment')->content_html; ?>
            </div>
        </div>
    </div>
</div>
