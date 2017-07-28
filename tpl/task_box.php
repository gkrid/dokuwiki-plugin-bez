<?php if ($template['task']->issue == ''): ?>
    <div class="bez_comments">
    <div class="bez_left_col">
<?php endif ?>

<a name="z<?php echo $template['task']->id ?>"></a>
<div id="z<?php echo $template['task']->id ?>"
	class="bds_block task <?php $template['task']->state_string	?>
    <?php if (  $template['action'] === 'task_edit_metadata' &&
            $template['tid'] === $template['task']->id) echo 'bez_metadata_edit_warn' ?>">
    
<?php if (  $template['action'] === 'task_edit_metadata' &&
            $template['tid'] === $template['task']->id): ?> 
    <?php
        if ($nparams['bez'] === 'issue') {
            $id = $this->id('issue', 'id', $template['issue']->id, 'action', $template['action'], 'tid', $template['tid']);
        } else {
             $id = $this->id('task', 'tid', $template['tid'], 'action', $template['action']);
        }
    ?>
    <h1 style="color: #f00; border-color: #f00;"><?php echo $bezlang['metadata_edit_header'] ?></h1>
	<form class="bez_metaform" action="?id=<?php echo $id ?>" method="POST">
<?php endif ?>

<div class="bez_timebox">
    <span>
    <?php if (  $template['action'] === 'task_edit_metadata' &&
            $template['tid'] === $template['task']->id &&
            $template['task']->acl_of('date') >= BEZ_PERMISSION_CHANGE): ?>
            <label><strong><?php echo $bezlang['open'] ?>:</strong> <input name="date" style="width:90px;" data-validation="required,date" value="<?php echo $value['date'] ?>" class="date start" /></label>
    <?php else: ?>
        <strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($template['task']->date) ?>
    <?php endif ?>
    </span>
	
	<?php if ($template['task']->state !== '0'): ?>
    
        <span>
        <?php if (  $template['action'] === 'task_edit_metadata' &&
                $template['tid'] === $template['task']->id &&
                $template['task']->acl_of('close_date') >= BEZ_PERMISSION_CHANGE): ?>
                <label><strong><?php echo $template['task']->state_string ?>:</strong> <input name="close_date" style="width:90px;" data-validation="required,date" value="<?php echo $value['close_date'] ?>" class="date end" /></label>
        <?php else: ?>
            
                <strong><?php echo $template['task']->state_string ?>:</strong>
                <?php echo $helper->time2date($template['task']->close_date) ?>
            
        <?php endif ?>
        </span>
        
		<span>
			<strong><?php echo $bezlang['report_priority'] ?>: </strong>
			<?php echo $helper->days((int)$template['task']->close_date - (int)$template['task']->date) ?>
		</span>
	<?php endif ?>
</div>

<h2>
	<a href="?id=<?php echo $this->id('task', 'tid', $template['task']->id) ?>">
		#z<?php echo $template['task']->id ?>
	</a>
	<?php echo lcfirst($template['task']->action_string) ?>
	(<?php echo lcfirst($template['task']->state_string) ?>)
</h2>

<table class="bez_box_data_table">
<tr>
    <th><?php echo $bezlang['reporter'] ?>:</th>
    <td>
        <?php if (  $template['action'] === 'task_edit_metadata' &&
            $template['tid'] === $template['task']->id &&
            $template['task']->acl_of('reporter') >= BEZ_PERMISSION_CHANGE): ?>
            
            <select name="reporter" id="reporter" data-validation="required">
                <option value="">--- <?php echo $bezlang['select'] ?>---</option>
                <?php foreach ($template['users'] as $nick => $name): ?>
                    <option <?php if ($value['reporter'] === $nick) echo 'selected' ?>
                     value="<?php echo $nick ?>"><?php echo $name ?></option>
                <?php endforeach ?>
            </select>
        <?php else: ?>
            <?php echo $this->model->users->get_user_full_name($template['task']->reporter) ?>
        <?php endif ?>
    </td>
    
    <th><?php echo $bezlang['executor'] ?>:</th>
    <td><?php echo $this->model->users->get_user_full_name($template['task']->executor) ?></td>
</tr>

<tr>
    <th style="white-space: nowrap;"><?php echo $bezlang['plan_date'] ?>:</th>
    <td>
        <?php echo $template['task']->plan_date ?><?php if ($template['task']->all_day_event === '0'): ?>,
            <?php echo $template['task']->start_time ?> - <?php echo $template['task']->finish_time ?>
        <?php endif ?>
    </td> 
    
    <th><?php echo $bezlang['task_type'] ?>:</th>
    <td>
    <?php if ($template['task']->tasktype_string == ''): ?>
        ---
    <?php else: ?>
        <?php echo $template['task']->tasktype_string ?>
    <?php endif ?>
    </td>
</tr>  

<tr>
    <th><?php echo $bezlang['cost'] ?>:</th>
    <td colspan="3">
    <?php if ($template['task']->cost === ''): ?>
        ---
    <?php else: ?>
        <?php echo $template['task']->cost_localized() ?>
    <?php endif ?>
    </td>
</tr>  

</table>

<?php echo $template['task']->task_cache ?>

<?php if (	$template['action'] !== 'task_change_state' ||
			$template['tid'] !== $template['task']->id): ?>
	<?php if ($template['task']->state === '2'): ?>
		<h3><?php echo $bezlang['reason'] ?></h3>
		<?php echo $template['task']->reason_cache ?>
	<?php elseif ($template['task']->state === '1' && $template['task']->reason != ''): ?>
		<h3><?php echo $bezlang['evaluation'] ?></h3>
		<?php echo $template['task']->reason_cache ?>
	<?php endif ?>
<?php endif ?>

<?php if (	$template['action'] === 'task_change_state' &&
			$template['tid'] === $template['task']->id): ?>
	<a name="form"></a>
	<?php if ($template['state'] === '2'): ?>
		<h3><?php echo $bezlang['reason'] ?></h3>
	<?php else: ?>
		<h3><?php echo $bezlang['evaluation'] ?></h3>
	<?php endif ?>
    <?php
        if ($nparams['bez'] === 'issue') {
            $id = $this->id('issue', 'id', $template['issue']->id, 'action', $template['action'], 'tid', $template['tid'], 'state', $template['state']);
        } else {
             $id = $this->id('task', 'tid', $template['tid'], 'action', $template['action'], 'state', $template['state']);
        }
    ?>
	<form class="bez_form" action="?id=<?php echo $id ?>" method="POST">
		<input type="hidden" name="id" value="<?php echo $id ?>">
        
        <?php if ($template['state'] === '1'): ?>
            <label style="display:block;margin-bottom:5px;"><input type="checkbox" name="no_evaluation" id="no_evaluation" /> <?php echo $bezlang['no_evaluation'] ?></label>
        <?php endif ?>
        
		<div class="bez_reason_toolbar"></div>
		<textarea name="reason" id="reason" data-validation="required"><?php echo $value['reason'] ?></textarea>
		<br>
		<?php if ($template['state'] === '2'): ?>
			<input type="submit" value="<?php echo $bezlang['task_reject'] ?>">
		<?php else: ?>
			<input type="submit" value="<?php echo $bezlang['task_do'] ?>">
		<?php endif ?>	
		<a href="?id=<?php
            if ($nparams['bez'] === 'issue') {
                echo $this->id('issue', 'id', $template['issue']->id).'#z'.$template['task']->id;
            } else {
                echo $this->id('task', 'tid', $template['task']->id);
            }
        ?>"
			 class="bez_delete_button bez_link_button">
				<?php echo $bezlang['cancel'] ?>
		</a>
	</form>
<?php elseif (  $template['action'] === 'task_edit_metadata' &&
                $template['tid'] === $template['task']->id): ?> 
        <input type="submit" value="<?php echo $bezlang['save'] ?>">&nbsp;&nbsp;
		<a href="?id=<?php
            if ($nparams['bez'] === 'issue') {
                echo $this->id('issue', 'id', $template['issue']->id).'#z'.$template['task']->id;
            } else {
                echo $this->id('task', 'tid', $template['task']->id);
            }
        ?>"
			 class="bez_delete_button bez_link_button">
				<?php echo $bezlang['cancel'] ?>
		</a>
    </form>
<?php else: ?>
	<div class="bez_buttons">
        <?php if (count($template['task']->changable_fields(
                    $template['task']->get_meta_fields()
                )) > 0): ?>
            <a class="bds_inline_button_noborder" style="float:left;"
				href="?id=<?php
					if ($nparams['bez'] === 'issue') {
						echo $helper->id('issue', 'id', $template['issue']->id, 'tid', $template['task']->id, 'action', 'task_edit_metadata');
					} else {
						echo $helper->id('task', 'tid', $template['task']->id, 'action', 'task_edit_metadata');
					}
				?>#z<?php echo $template['task']->id ?>">
				<?php echo $bezlang['edit_metadata'] ?>
			</a>
        <?php endif ?>
        
		<?php if (	$template['task']->state === '0' &&
					$template['task']->acl_of('state') >= BEZ_PERMISSION_CHANGE): ?>
			<a class="bds_inline_button"
				href="?id=<?php
					if ($nparams['bez'] === 'issue') {
						echo $helper->id('issue', 'id', $template['issue']->id, 'tid', $template['task']->id, 'action', 'task_change_state', 'state', '1');
					} else {
						echo $helper->id('task', 'tid', $template['task']->id, 'action', 'task_change_state', 'state', '1');
					}
				?>#z<?php echo $template['task']->id ?>">
				↬ <?php echo $bezlang['task_do'] ?>
			</a>
			<a class="bds_inline_button"
				href="?id=<?php
					if ($nparams['bez'] === 'issue') {
						echo $helper->id('issue', 'id', $template['issue']->id, 'tid', $template['task']->id, 'action', 'task_change_state', 'state', '2');
					} else {
						echo $helper->id('task', 'tid', $template['task']->id, 'action', 'task_change_state', 'state', '2');
					}
				?>#z<?php echo $template['task']->id ?>">
				↛ <?php echo $bezlang['task_reject'] ?>
			</a>
		<?php elseif (  $template['task']->state !== '0' &&
                        $template['task']->acl_of('state') >= BEZ_PERMISSION_CHANGE): ?>
			<a class="bds_inline_button"
					href="?id=<?php
						if ($nparams['bez'] === 'issue') {
							echo $helper->id('issue', 'id', $template['issue']->id, 'tid', $template['task']->id, 'action', 'task_reopen');
						} else {
							echo $helper->id('task', 'tid', $template['task']->id, 'action', 'task_reopen');
						}
					?>">
					↻ <?php echo $bezlang['task_reopen'] ?>
				</a>
		<?php endif ?>
        
		<?php if (count($template['task']->changable_fields()) > 0): ?>
				<a class="bds_inline_button"
					href="?id=<?php
						if ($nparams['bez'] === 'issue') {
							echo $helper->id('issue', 'id', $template['issue']->id, 'tid', $template['task']->id, 'action', 'task_edit');
						} else {
							echo $helper->id('task', 'tid', $template['task']->id, 'action', 'task_edit');
						}
					?>#z_">
					✎ <?php echo $bezlang['edit'] ?>
				</a>
		<?php endif ?>

		<a class="bds_inline_button" href="
		<?php echo $helper->mailto($this->model->users->get_user_email($template['task']->executor),
		$bezlang['task'].': #z'.$template['task']->id.' '.lcfirst($template['task']->action_string),
        DOKU_URL . 'doku.php?id='.$this->id('task', 'tid', $template['task']->id)) ?>">
			✉ <?php echo $bezlang['send_mail'] ?>
		</a>

		<?php if ($template['task']->tasktype !== '' &&
                  $this->model->acl->get_level() >= BEZ_AUTH_USER): ?>
			<a class="bds_inline_button"
					href="?id=<?php echo $this->id('task_form', 'duplicate', $template['task']->id, 'tasktype', $template['task']->tasktype) ?>">
					⇲ <?php echo $bezlang['duplicate'] ?>
			</a>
		<?php endif ?>
	</div>	
<?php endif ?>

</div>

<?php if ($template['task']->issue == ''): ?>
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
