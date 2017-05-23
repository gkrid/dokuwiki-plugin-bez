<a name="z<?php echo $template['task']->id ?>"></a>
<div id="z<?php echo $template['task']->id ?>"
	class="bds_block task <?php $template['task']->state_string	?>">

<div class="bez_timebox">
	<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($template['task']->date) ?></span>
	
	<?php if ($template['task']->state !== '0'): ?>
		<span>
			<strong><?php echo $template['task']->state_string ?>:</strong>
			<?php echo $helper->time2date($template['task']->close_date) ?>
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
    
<?php
    $top_row = array(
        '<strong>'.$bezlang['executor'].': </strong>' . 
        $this->model->users->get_user_full_name($template['task']->executor),
        
        '<strong>'.$bezlang['reporter'].': </strong>' . 
        $this->model->users->get_user_full_name($template['task']->reporter)
    );

    if ($template['task']->tasktype_string != '') {
        $top_row[] =
            '<strong>'.$bezlang['task_type'].': </strong>' . 
            $template['task']->tasktype_string;
    }
		
	if ($template['task']->cost != '') {
        $top_row[] =
            '<strong>'.$bezlang['cost'].': </strong>' . 
            $template['task']->cost;
    }
	
    //BOTTOM ROW
    $bottom_row = array(
        '<strong>'.$bezlang['plan_date'].': </strong>' . 
        $template['task']->plan_date
    );			

	if ($template['task']->all_day_event == '0') {
        $bottom_row[] =
            '<strong>'.$bezlang['start_time'].': </strong>' . 
            $template['task']->start_time;
        $bottom_row[] =
            '<strong>'.$bezlang['finish_time'].': </strong>' . 
            $template['task']->finish_time;
	}
    echo bez_html_irrtable(array(), $top_row, $bottom_row);
?>

<?php echo $template['task']->task_cache ?>

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
<?php else: ?>
	<?php if ($template['task']->state === '2'): ?>
		<h3><?php echo $bezlang['reason'] ?></h3>
		<?php echo $template['task']->reason_cache ?>
	<?php elseif ($template['task']->state === '1' && $template['task']->reason != ''): ?>
		<h3><?php echo $bezlang['evaluation'] ?></h3>
		<?php echo $template['task']->reason_cache ?>
	<?php endif ?>
	<div class="bez_buttons">
		<?php if (	$template['task']->state === '0' &&
					$template['task']->get_level() >= 10): ?>
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
		<?php elseif ((!isset($template['issue']) || $template['issue']->state === '0') &&
                      $template['task']->get_level() >= 10): ?>
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
        
		<?php if ( (!isset($template['issue']) || $template['issue']->state === '0') &&
                  ($template['task']->get_level() >= 15 ||
                   $template['task']->reporter === $template['task']->get_user())): ?>
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

		<?php if ($template['task']->tasktype != NULL && $template['task']->get_level() >= 5): ?>
			<a class="bds_inline_button"
					href="?id=<?php echo $this->id('task_form', 'duplicate', $template['task']->id, 'tasktype', $template['task']->tasktype) ?>">
					⇲ <?php echo $bezlang['duplicate'] ?>
			</a>
		<?php endif ?>
	</div>	
<?php endif ?>

</div>

