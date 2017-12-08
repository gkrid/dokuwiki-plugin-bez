<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<?php include "thread_box.php" ?>

<!-- Comments -->
<div class="bez_comments">
	<div class="bez_left_col">
		<!-- Correction -->
		<div style="margin-top: 10px">
			<?php foreach ($tpl->get('corrections') as $task): ?>
				<?php $tpl->set('task', $task) ?>
				<?php if (	$tpl->action() === 'task_edit' &&
                            $tpl->param('tid') == $task->id): ?>
					<?php include 'task_form.php' ?>
				<?php else: ?>
					<?php include 'task_box.php' ?>
				<?php endif ?>
				
			<?php endforeach ?>
			<?php if ($tpl->action() == 'task_correction_add'): ?>
				<?php include 'task_form.php' ?>
			<?php endif ?>
		</div>

		<div class="bez_second_lv_buttons" style="margin-top: 10px">
			<?php if (	$tpl->get('thread')->user_is_coordinator() &&
                        $tpl->get('thread')->state == 'opened'): ?>
				<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'task_correction_add') ?>#z_" class="bez_subscribe_button">
					<span class="bez_awesome">&#xf0fe;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('correction_add') ?>
				</a>
			<?php endif ?>
			<a href="#" class="bez_subscribe_button bez_hide_comments">
				<span class="bez_awesome">&#xf070;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('hide_comments') ?>
			</a>
			<a href="#" class="bez_subscribe_button bez_show_comments">
				<span class="bez_awesome">&#xf06e;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('show_comments') ?>
			</a>
		</div>
		
		<?php foreach ($tpl->get('thread_comments') as $thread_comment): ?>
            <?php $tpl->set('thread_comment', $thread_comment) ?>
			<?php if (	$tpl->action() == 'commcause_edit' &&
						$tpl->param('kid') == $thread_comment->id): ?>
				<?php include 'commcause_form.php' ?>
			<?php else: ?>
				<?php include 'commcause_box.php' ?>
			<?php endif ?>
		<?php endforeach ?>

<?php if (	$tpl->get('thread')->state == 'opened' &&
			!(strpos($tpl->action(), 'task') === 0) &&
            $tpl->action() != 'commcause_edit'): ?>

<?php include 'commcause_form.php' ?>
	
<?php endif ?>

</div>
<div class="bez_right_col">
	
<div class="bez_box">
<h2><?php echo $tpl->getLang('comment_last_activity') ?></h2>

<?php echo dformat(strtotime($tpl->get('thread')->last_activity_date), '%Y-%m-%d') ?>


</div>

<div class="bez_box bez_subscribe_box">
<h2><?php echo $tpl->getLang('norifications') ?></h2>
<?php if ($tpl->get('thread')->is_subscribent()): ?>
	<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'unsubscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf1f6;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('unsubscribe') ?></a>
	<p><?php echo $tpl->getLang('subscribed_info') ?></p>
<?php else: ?>
	<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'subscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf0f3;</span>&nbsp;&nbsp;<?php echo $tpl->getLang('subscribe') ?></a>
	<p><?php echo $tpl->getLang('subscribed_info') ?></p>
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
            $tpl->get('thread')->state == 'opened'): ?>
    <h2><?php echo $tpl->getLang('issue_invite_header') ?></h2>
    <form action="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'invite') ?>" method="post" id="bez_invite_users_form">
    <div id="bez_invite_users" class="ui-widget">
        <select name="client">
            <option value="">--- <?php echo $tpl->getLang('select') ?> ---</option>
            <?php foreach (array_key_diff($tpl->get('users'), $tpl->get('thread')->get_participants('subscribent')) as $user_id => $ignore): ?>
                <option value="<?php echo $user_id ?>"><?php echo $tpl->user_name($user_id) ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <button class="bez_subscribe_button"><?php echo $tpl->getLang('issue_invite_button') ?></button>
    </form>
<?php endif ?>


</div>


</div>

