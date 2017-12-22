<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<?php $url = $tpl->url('task', 'tid', $tpl->get('task')->id, 'action',
                       $tpl->param('action') == 'comment_edit' ? 'comment_edit' : 'comment_add',
                       'zkid', $tpl->param('zkid')) ?>
<a id="zk_"></a>
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
                    <div class="bez_toolbar"></div>
                </h2>
            </div>
            <div class="bez_content">
                <textarea name="content" class="bez_textarea_content" id="content"><?php echo $tpl->value('content') ?></textarea>
                 <div class="plugin__bez_form_buttons">
                <div class="plugin__bez_form_buttons_container">

                <?php if ($tpl->param('zkid') != ''): ?>
                    <a href="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id) ?><?php if ($tpl->param('zkid') != '') echo '#zk'.$tpl->param('zkid') ?>"
                       class="plugin__bez_button plugin__bez_button_red">
                        <?php echo $tpl->getLang('cancel') ?>
                    </a>
                <?php endif ?>

                <?php if ($tpl->get('task')->state == 'opened'): ?>
                    <button class="plugin__bez_button plugin__bez_button_green" name="fn" value="comment_add">
                        <?php echo $tpl->param('zkid') != '' ? $tpl->getLang('correct') : $tpl->getLang('comment') ?>
                    </button>
                <?php endif ?>
                <?php if ($tpl->param('zkid') == '' && $tpl->get('task')->acl_of('state') >= BEZ_PERMISSION_CHANGE): ?>
                    <?php if ($tpl->get('task')->state == 'opened'): ?>
                        <button class="plugin__bez_button plugin__bez_button_gray" name="fn" value="task_do">
                            <?php echo $tpl->getLang('js')['do_task'] ?>
                        </button>
                    <?php else: ?>
                        <button class="plugin__bez_button plugin__bez_button_gray" name="fn" value="task_reopen">
                            <?php echo $tpl->getLang('js')['reopen_task'] ?>
                        </button>
                    <?php endif ?>
                <?php endif ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</form>
