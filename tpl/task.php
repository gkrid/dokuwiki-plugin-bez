<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<?php if ($tpl->get('task')->thread_id != '' && $tpl->get('task')->thread->acl_of('id') >= BEZ_PERMISSION_VIEW): ?>
    <div class="bez_thread
        <?php
    if ($tpl->get('task')->thread->state == 'opened') {
        echo 'priority_' . $tpl->get('task')->thread->priority;
    }
    ?>">
        <div>
            <strong><?php echo $tpl->getLang('issue') ?>:</strong>
            <a href="<?php echo $tpl->url('thread', 'id', $tpl->get('task')->thread->id) ?>">
                #<?php echo $tpl->get('task')->thread->id ?>
            </a>
            <strong>
            <?php if ($tpl->get('task')->thread->type == 'project'): ?>
                <?php echo $tpl->getLang('project') ?>
            <?php elseif (!empty($tpl->get('task')->thread->label_name)): ?>
                <?php echo $tpl->get('task')->thread->label_name ?>
            <?php else: ?>
                <i style="color: #777"><?php echo $tpl->getLang('issue_type_no_specified') ?></i>
            <?php endif ?>

            (<?php echo $tpl->getLang('state_' . $tpl->get('task')->thread->state) ?>):
            </strong>
            <?php echo $tpl->get('task')->thread->title ?>
        </div>

        <?php if ($tpl->get('task')->thread_comment_id != ''): ?>
            <div style="margin-top: 12px;">
                <strong>
                    <a href="<?php echo $tpl->url('thread', 'id', $tpl->get('task')->thread->id) ?>#k<?php echo $tpl->get('task')->thread_comment->id ?>">
                        #k<?php echo $tpl->get('task')->thread_comment->id ?>
                    </a>
                    <?php echo $tpl->getLang($tpl->get('task')->thread_comment->type) ?>
                </strong>
                <?php echo $tpl->get('task')->thread_comment->content_html ?>
            </div>
        <?php endif ?>
    </div>


    <br>
<?php endif ?>

<?php if (	$tpl->param('action') == 'task_edit' &&
    $tpl->param('tid') == $tpl->get('task')->id): ?>
    <?php include 'task_form.php' ?>
<?php else: ?>
    <?php include 'task_box.php' ?>
<?php endif ?>

<br>
<div class="bez_comments">
    <div class="bez_left_col">
    <?php foreach ($tpl->get('task_comments') as $task_comment): ?>
        <?php $tpl->set('task_comment', $task_comment) ?>
        <?php if (	$tpl->param('action') == 'comment_edit' &&
            $tpl->param('zkid') == $task_comment->id): ?>
            <?php include 'task_comment_form.php' ?>
        <?php else: ?>
            <?php include 'task_comment_box.php' ?>
        <?php endif ?>
    <?php endforeach ?>

    <?php if ($tpl->get('task')->state == 'done'): ?>
        <div class="plugin__bez_status_label">
            <span class="icon icon_green">
                <?php echo inlineSVG(DOKU_PLUGIN . 'bez/images/tick.svg') ?>
            </span>
            <?php printf($tpl->getLang('user_did_task'),
                           '<strong>' . $tpl->user_name($tpl->get('task')->closed_by) . '</strong>',
                           $tpl->date_fuzzy_age($tpl->get('task')->close_date)) ?>
        </div>
    <?php endif ?>

    <?php if($tpl->param('action') != 'task_edit' && $tpl->param('action') != 'comment_edit' && $tpl->get('task')->can_add_comments()): ?>
        <?php include 'task_comment_form.php' ?>
    <?php endif ?>

    </div>

<div class="bez_right_col" style="position:relative; top: -15px;">

    <div class="bez_box bez_subscribe_box">
        <h2><?php echo $tpl->getLang('norifications') ?></h2>
        <?php if ($tpl->get('task')->is_subscribent()): ?>
            <a href="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id, 'action', 'unsubscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf1f6;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('unsubscribe') ?></a>
            <p><?php echo $tpl->getLang('task_subscribed_info') ?></p>
        <?php else: ?>
            <a href="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id, 'action', 'subscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf0f3;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('subscribe') ?></a>
            <p><?php echo $tpl->getLang('task_not_subscribed_info') ?></p>
        <?php endif ?>

    </div>

    <div class="bez_box">
        <h2><?php echo $tpl->getLang('comment_participants') ?></h2>
        <ul id="issue_participants">
            <?php foreach ($tpl->get('task')->get_participants() as $participant): ?>
                <li>
                    <?php if ($tpl->get('task')->acl_of('participants') >= BEZ_PERMISSION_CHANGE &&
                        $participant['assignee'] == '0'): ?>
                        <a href="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id, 'action', 'participant_remove', 'user_id', $participant['user_id']) ?>"
                           class="participant_remove">
                            <span class="bez_awesome">
                                &#xf00d;
                            </span>
                        </a>
                    <?php endif ?>
                    <a href="<?php echo $tpl->mailto($tpl->user_email($participant['user_id']),
                                                     '#z'.$tpl->get('task')->id,
                                                     $tpl->url('task', 'tid', $tpl->get('task')->id)) ?>"
                       title="<?php echo $participant['user_id'] ?>">
                        <span class="bez_name"><?php echo $tpl->user_name($participant['user_id']) ?></span>
                        <span class="bez_icons">
		<?php if($participant['original_poster']): ?>
            <span class="bez_awesome"
                  title="<?php echo $tpl->getLang('reporter') ?>">
				&#xf058;
			</span>
        <?php endif ?>
            <?php if($participant['assignee']): ?>
                <span class="bez_awesome"
                      title="<?php echo $tpl->getLang('executor') ?>">
				&#xf073;
			</span>
                <?php endif ?>
                <?php if($participant['commentator']): ?>
                    <span class="bez_awesome"
                          title="<?php echo $tpl->getLang('commentator') ?>">
				&#xf27a;
			</span>
                <?php endif ?>
                <?php if($participant['subscribent']): ?>
                    <span class="bez_awesome"
                          title="<?php echo $tpl->getLang('subscribent') ?>">
				&#xf0e0;
			</span>
                            <?php endif ?>
		</span>
                    </a></li>
            <?php endforeach ?>
        </ul>

        <?php if ($tpl->get('task')->acl_of('participants') >= BEZ_PERMISSION_CHANGE): ?>
            <h2><?php echo $tpl->getLang('issue_invite_header') ?></h2>
            <form action="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id, 'action', 'invite') ?>" method="post" id="bez_invite_users_form">
                <div id="bez_invite_users" class="ui-widget">
                    <select name="client">
                        <option value="">--- <?php echo $tpl->getLang('select') ?> ---</option>
                        <?php foreach (array_diff_key($tpl->get('users'), $tpl->get('task')->get_participants('subscribent')) as $user_id => $ignore): ?>
                            <option value="<?php echo $user_id ?>"><?php echo $tpl->user_name($user_id) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <button class="bez_subscribe_button"><?php echo $tpl->getLang('issue_invite_button') ?></button>
            </form>
        <?php endif ?>


    </div>


</div>

</div>

