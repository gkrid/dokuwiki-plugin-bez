<?php if ($tpl->get('task')->thread_id == ''): ?>
    <div class="bez_comments">
    <div class="bez_left_col">
<?php endif ?>

<a name="z<?php echo $tpl->get('task')->id ?>"></a>
<div id="z<?php echo $tpl->get('task')->id ?>"
	class="bds_block task <?php echo 'state_' . $tpl->get('task')->state ?>">

<div class="bez_timebox">
    <span>
        <strong><?php echo $tpl->getLang('open') ?>:</strong>
        <?php echo dformat(strtotime($tpl->get('task')->create_date), '%Y-%m-%d') ?>
    </span>
	
	<?php if ($tpl->get('task')->state != 'opened'): ?>
    
        <span>
            <strong><?php echo $tpl->getLang('task_' . $tpl->get('task')->state) ?>:</strong>
            <?php echo dformat(strtotime($tpl->get('task')->close_date), '%Y-%m-%d') ?>
        </span>
        
		<span>
			<strong><?php echo $tpl->getLang('report_priority') ?>: </strong>
            <?php $dStart = new DateTime($tpl->get('task')->create_date) ?>
            <?php $dEnd = new DateTime($tpl->get('task')->close_date) ?>
            <?php echo $dStart->diff($dEnd)->days ?> <?php echo $tpl->getLang('days') ?>
		</span>
	<?php endif ?>
</div>

<h2>
	<a href="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id) ?>">
		#z<?php echo $tpl->get('task')->id ?>
	</a>
	<?php echo lcfirst($tpl->getLang('task_type_' . $tpl->get('task')->type)) ?>
	(<?php echo lcfirst($tpl->getLang('task_' . $tpl->get('task')->state)) ?>)
</h2>

<table class="bez_box_data_table">
<tr>
    <th><?php echo $tpl->getLang('reporter') ?>:</th>
    <td>
        <?php echo $tpl->user_name($tpl->get('task')->original_poster) ?>
    </td>
    
    <th><?php echo $tpl->getLang('executor') ?>:</th>
    <td><?php echo $tpl->user_name($tpl->get('task')->assignee) ?></td>
</tr>

<tr>
    <th style="white-space: nowrap;"><?php echo $tpl->getLang('plan_date') ?>:</th>
    <td>
        <?php echo $tpl->get('task')->plan_date ?><?php if ($tpl->get('task')->all_day_event == '0'): ?>,
            <?php echo $tpl->get('task')->start_time ?> - <?php echo $tpl->get('task')->finish_time ?>
        <?php endif ?>
    </td> 
    
    <th><?php echo $tpl->getLang('task_type') ?>:</th>
    <td>
    <?php if ($tpl->get('task')->task_program_id == ''): ?>
        ---
    <?php else: ?>
        <?php echo $tpl->get('task')->task_program_name ?>
    <?php endif ?>
    </td>
</tr>  

<tr>
    <th><?php echo $tpl->getLang('cost') ?>:</th>
    <td colspan="3">
    <?php if ($tpl->get('task')->cost == ''): ?>
        ---
    <?php else: ?>
        <?php echo $tpl->get('task')->cost ?>
    <?php endif ?>
    </td>
</tr>  

</table>

<?php echo $tpl->get('task')->content_html ?>

    <div class="bez_buttons">

        <?php if (count($tpl->get('task')->changable_fields()) > 0): ?>
                <a class="bds_inline_button"
                    href="<?php
                        if ($tpl->action() == 'thread') {
                            echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'tid', $tpl->get('task')->id, 'action', 'task_edit');
                        } else {
                            echo $tpl->url('task', 'tid', $tpl->get('task')->id, 'action', 'task_edit');
                        }
                    ?>#z_">
                    ✎ <?php echo $tpl->getLang('edit') ?>
                </a>
        <?php endif ?>

        <a class="bds_inline_button" href="
        <?php echo $tpl->mailto($tpl->user_email($tpl->get('task')->assignee),
        '#z'.$tpl->get('task')->id,
        $tpl->url('task', 'tid', $tpl->get('task')->id)) ?>">
            ✉ <?php echo $tpl->getLang('send_mail') ?>
        </a>

        <?php if ($tpl->get('task')->task_program_id != '' &&
                  $tpl->user_acl_level() >= BEZ_AUTH_USER): ?>
            <a class="bds_inline_button"
                    href="?id=<?php echo $this->id('task_form', 'duplicate', $tpl->get('task')->id, 'task_program_id', $tpl->get('task')->task_program_id) ?>">
                    ⇲ <?php echo $tpl->getLang('duplicate') ?>
            </a>
        <?php endif ?>
	</div>
</div>

<?php if ($tpl->get('task')->thread_id == ''): ?>
</div>

<div class="bez_right_col" style="position:relative; top: -15px;">
	
<div class="bez_box bez_subscribe_box">
<h2><?php echo $bezlang['norifications'] ?></h2>
<?php if ($template['task']->is_subscribent()): ?>
	<a href="?id=<?php echo $this->id('task', 'tid', $template['task']->id, 'action', 'unsubscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf1f6;</span>&nbsp;&nbsp;<?php echo $bezlang['unsubscribe'] ?></a>
	<p><?php echo $bezlang['subscribed_info'] ?></p>
<?php else: ?>
	<a href="?id=<?php echo $this->id('task', 'tid', $template['task']->id, 'action', 'subscribe') ?>" class="bez_subscribe_button"><span class="bez_awesome">&#xf0f3;</span>&nbsp;&nbsp;<?php echo $bezlang['subscribe'] ?></a>
	<p><?php echo $bezlang['not_subscribed_info'] ?></p>
<?php endif ?>

</div>

<div class="bez_box">
<h2><?php echo $bezlang['comment_participants'] ?></h2>
<ul id="issue_participants">
<?php foreach ($template['task']->get_participants() as $nick => $participant): ?>
	<li><a href="<?php echo $helper->mailto($this->model->users->get_user_email($nick),
		$bezlang['task'].': #z'.$template['task']->id,
		DOKU_URL . 'doku.php?id='.$this->id('task', 'tid', $template['task']->id)) ?>"  title="<?php echo $nick ?>">
		<span class="bez_name"><?php echo $participant ?></span>
		<span class="bez_icons">
		<?php if($template['task']->reporter === $nick): ?>
			<span class="bez_awesome"
				title="<?php echo $bezlang['reporter'] ?>">
				&#xf058;
			</span>
		<?php endif ?>
		<?php if($template['task']->executor === $nick): ?>
			<span class="bez_awesome"
				title="<?php echo $bezlang['executor'] ?>">
				&#xf073;
			</span>
		<?php endif ?>
        <?php if($template['task']->is_subscribent($nick)): ?>
            <span class="bez_awesome"
                title="<?php echo $bezlang['subscribent'] ?>">
                &#xf0e0;
            </span>
        <?php endif ?>
		</span>
	</a></li>
<?php endforeach ?>
</ul>

<?php if ($template['task']->acl_of('subscribents') >= BEZ_PERMISSION_CHANGE): ?>
    <h2><?php echo $bezlang['issue_invite_header'] ?></h2>
    <form action="?id=<?php echo $this->id('task', 'tid', $template['task']->id, 'action', 'invite') ?>" method="post" id="bez_invite_users_form">
    <div id="bez_invite_users" class="ui-widget">
        <select name="client">
            <option value="">--- <?php echo $bezlang['select'] ?> ---</option>
            <?php foreach ($template['users_to_invite'] as $nick => $name): ?>
                <option value="<?php echo $nick ?>"><?php echo $name ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <button class="bez_subscribe_button"><?php echo $bezlang['issue_invite_button'] ?></button>
    </form>
<?php endif ?>


</div>


</div>

</div>

<?php endif ?>