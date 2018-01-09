<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<?php include "thread_box.php" ?>

<div class="bez_comments">
	<div class="bez_left_col">
        <?php if ($tpl->param('action') == '' && $tpl->get('thread')->user_is_coordinator() && $tpl->get('thread')->can_add_tasks()): ?>
        <div class="bez_second_lv_buttons" style="margin-top: 10px">
            <a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'task_add') ?>#z_" class="bez_subscribe_button">
                <span class="bez_awesome">&#xf0fe;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('correction_add' . $tpl->get('lang_suffix')) ?>
            </a>
        </div>
        <?php endif ?>

		<?php foreach ($tpl->get('timeline') as $entity): ?>
            <?php if ($entity->get_table_name() == 'thread_comment'): ?>
                <?php $tpl->set('thread_comment', $entity) ?>
                <?php if (	$tpl->param('action') == 'commcause_edit' &&
                            $tpl->param('kid') == $entity->id): ?>
                    <?php include 'commcause_form.php' ?>
                <?php else: ?>
                    <?php include 'commcause_box.php' ?>
                <?php endif ?>
            <?php elseif($entity->get_table_name() == 'task'): ?>
                <br>
                <?php $tpl->set('task', $entity) ?>
                <?php if (	$tpl->param('action') == 'task_edit' &&
                    $tpl->param('tid') == $entity->id): ?>
                    <?php include 'task_form.php' ?>
                <?php else: ?>
                    <?php include 'task_box.php' ?>
                <?php endif ?>
            <?php endif ?>
		<?php endforeach ?>

        <?php if ($tpl->param('action') == 'task_add' && $tpl->param('kid') == ''): ?>
            <br>
            <?php include 'task_form.php' ?>
        <?php elseif ($tpl->get('thread')->state == 'closed'): ?>
            <div class="plugin__bez_status_label">
            <span class="icon icon_green">
                <?php echo inlineSVG(DOKU_PLUGIN . 'bez/images/tick.svg') ?>
            </span>
                <?php printf($tpl->getLang('user_closed_issue'),
                             '<strong>' . $tpl->user_name($tpl->get('thread')->closed_by) . '</strong>',
                             $tpl->date_fuzzy_age($tpl->get('thread')->close_date)) ?>
            </div>
        <?php elseif ($tpl->get('thread')->state == 'rejected'): ?>
                <div class="plugin__bez_status_label">
            <span class="icon icon_red">
                <?php echo inlineSVG(DOKU_PLUGIN . 'bez/images/close.svg') ?>
            </span>
                    <?php printf($tpl->getLang('user_rejected_issue'),
                                 '<strong>' . $tpl->user_name($tpl->get('thread')->closed_by) . '</strong>',
                                 $tpl->date_fuzzy_age($tpl->get('thread')->close_date)) ?>
                </div>
        <?php endif ?>


<?php if (	!(strpos($tpl->param('action'), 'task') === 0) &&
            $tpl->param('action') != 'commcause_edit' &&
            !(in_array($tpl->get('thread')->state, array('closed', 'rejected')) &&
                $tpl->get('thread')->acl_of('state') < BEZ_PERMISSION_CHANGE)): ?>

    <?php include 'commcause_form.php' ?>

    <br>
    <?php if ($tpl->get('thread')->task_count - $tpl->get('thread')->task_count_closed > 0): ?>
        <div class="info"><?php echo $tpl->getLang('issue_unclosed_tasks' . $tpl->get('lang_suffix')) ?></div>
    <?php endif ?>
    <?php if ($tpl->get('thread')->state == 'proposal'): ?>
        <div class="info"><?php echo $tpl->getLang('issue_is_proposal' . $tpl->get('lang_suffix')) ?></div>
    <?php endif ?>
    <?php if ($tpl->get('causes_without_tasks')): ?>
        <div class="info"><?php echo $tpl->getLang('cause_without_task') ?></div>
    <?php endif ?>
    <?php if ($tpl->get('thread')->state == 'opened' && $tpl->get('thread')->task_count == 0): ?>
        <div class="info"><?php echo $tpl->getLang('issue_no_tasks' . $tpl->get('lang_suffix')) ?></div>
    <?php endif ?>
<?php endif ?>


</div>
<div class="bez_right_col">
	
<div class="bez_box">
<h2><?php echo $tpl->getLang('comment_last_activity') ?></h2>

<?php echo $tpl->datetime($tpl->get('thread')->last_activity_date) ?>


</div>

<div class="bez_box bez_subscribe_box">
<h2><?php echo $tpl->getLang('norifications') ?></h2>
<?php if ($tpl->get('thread')->is_subscribent()): ?>
	<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'unsubscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf1f6;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('unsubscribe') ?></a>
	<p><?php echo $tpl->getLang('subscribed_info' . $tpl->get('lang_suffix')) ?></p>
<?php else: ?>
	<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'subscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf0f3;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('subscribe') ?></a>
	<p><?php echo $tpl->getLang('not_subscribed_info' . $tpl->get('lang_suffix')) ?></p>
<?php endif ?>

</div>

<div class="bez_box">
<h2><?php echo $tpl->getLang('comment_participants') ?></h2>
<ul id="issue_participants">
<?php foreach ($tpl->get('thread')->get_participants() as $participant): ?>
	<li><a href="<?php echo $tpl->mailto($tpl->user_email($participant['user_id']),
		'#'.$tpl->get('thread')->id.' '.$tpl->get('thread')->title,
		$tpl->url('thread', 'id', $tpl->get('thread')->id)) ?>"  title="<?php echo $participant['user_id'] ?>">
		<span class="bez_name"><?php echo $tpl->user_name($participant['user_id']) ?></span>
		<span class="bez_icons">
		<?php if($participant['original_poster']): ?>
			<span class="bez_awesome"
				title="<?php echo $tpl->getLang('reporter') ?>">
				&#xf058;
			</span>
		<?php endif ?>
		<?php if($participant['coordinator']): ?>
			<span class="bez_awesome"
				title="<?php echo $tpl->getLang('coordinator') ?>">
				&#xf0e3;
			</span>
		<?php endif ?>
		<?php if($participant['task_assignee']): ?>
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

<?php if (	$tpl->get('thread')->user_is_coordinator() &&
            $tpl->get('thread')->can_add_participants()): ?>
    <h2><?php echo $tpl->getLang('issue_invite_header') ?></h2>
    <form action="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'invite') ?>" method="post" id="bez_invite_users_form">
    <div id="bez_invite_users" class="ui-widget">
        <select name="client">
            <option value="">--- <?php echo $tpl->getLang('select') ?> ---</option>
            <?php foreach (array_diff_key($tpl->get('users'), $tpl->get('thread')->get_participants('subscribent')) as $user_id => $ignore): ?>
                <option value="<?php echo $user_id ?>"><?php echo $tpl->user_name($user_id) ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <button class="bez_subscribe_button"><?php echo $tpl->getLang('issue_invite_button') ?></button>
    </form>
<?php endif ?>


</div>


</div>

