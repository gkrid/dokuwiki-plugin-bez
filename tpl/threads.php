<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>

<?php if ($tpl->static_acl('thread', 'id') >= BEZ_PERMISSION_CHANGE): ?>
    <a href="<?php echo $tpl->url('thread_report') ?>" class="bez_start_button" id="bez_report_issue_button">
        <?php echo $tpl->getLang('report_issue') ?>
    </a>
<?php endif ?>

<br /><br />

<div class="bez_filter_form">
<form action="<?php echo $tpl->url('threads') ?>" method="post">
	<label><?php echo $tpl->getLang('state') ?>:
		<select name="state">
			<option <?php if ($tpl->value('state') === '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
		<?php foreach (\dokuwiki\plugin\bez\mdl\Thread::get_states() as $state): ?>
			<option <?php if ($tpl->value('state') === $state) echo 'selected' ?>
				value="<?php echo $state ?>"><?php echo $tpl->getLang('state_' . $state) ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $tpl->getLang('just_type') ?>:
		<select name="label_id">
			<option <?php if ($tpl->value('label_id') === '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
			<option <?php if ($tpl->value('label_id') === '-none') echo 'selected' ?>
			value="-none">--- <?php echo $tpl->getLang('issue_type_no_specified') ?> ---</option>
		<?php foreach ($tpl->get('labels') as $label): ?>
			<option <?php if ($tpl->value('label_id') === $label->id) echo 'selected' ?>
				value="<?php echo $label->id ?>"><?php echo $label->name ?></option>
		<?php endforeach ?>
		</select>
	</label>
    
	<label><?php echo $tpl->getLang('coordinator') ?>:
		<select name="coordinator">
			<option <?php if ($tpl->value('coordinator') === '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
			<option <?php if ($tpl->value('coordinator') === '-none') echo 'selected' ?>
				value="-none">--- <?php echo $tpl->getLang('none') ?> ---</option>
		<optgroup label="<?php echo $tpl->getLang('users') ?>">
			<?php foreach ($tpl->get('users') as $nick => $name): ?>
				<option <?php if ($tpl->value('coordinator') === $nick) echo 'selected' ?>
					value="<?php echo $nick ?>"><?php echo $name ?></option>
			<?php endforeach ?>
	</optgroup>	
	<optgroup label="<?php echo $tpl->getLang('groups') ?>">
		<?php foreach ($tpl->get('groups') as $name): ?>
			<?php $group = "@$name" ?>
			<option <?php if ($tpl->value('coordinator') === $group) echo 'selected' ?>
				value="<?php echo $group ?>"><?php echo $group ?></option>
		<?php endforeach ?>
	</optgroup>
	</select>
	</label>
    
	<label><?php echo $tpl->getLang('title') ?>:
		<input name="title" value="<?php echo $tpl->value('title') ?>" />
	</label>


	<label><?php echo $tpl->getLang('year') ?>:
		<select name="year">
			<option <?php if ($tpl->value('year') === '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
		<?php foreach ($tpl->get('years') as $year): ?>
			<option <?php if ($tpl->value('year') === $year) echo 'selected' ?>
				value="<?php echo $year ?>"><?php echo $year ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<label><?php echo $tpl->getLang('sort_by_open_date') ?>:
			<input type="checkbox" name="sort_open"
			<?php if ($tpl->value('sort_open') === 'on') echo 'checked="checked"' ?>>
	</label>
	<label><input type="submit" value="<?php echo $tpl->getLang('filter') ?>" /></label>
</form>
</div>

<table class="bez bez_sumarise">
	<tr>
		<th><?php echo $tpl->getLang('id') ?></th>
		<th><?php echo $tpl->getLang('state') ?></th>
		<th><?php echo $tpl->getLang('type') ?></th>
		<th><?php echo $tpl->getLang('title')?></th>
		<th><?php echo $tpl->getLang('coordinator') ?></th>
		<th><?php echo $tpl->getLang('date') ?></th>
		<th><?php echo $tpl->getLang('last_mod_date') ?></th>
		<th><?php echo $tpl->getLang('closed') ?></th>
		<th><?php echo $tpl->getLang('cost') ?></th>
		<th><?php echo $tpl->getLang('closed_tasks') ?></th>
	</tr>
    <?php $count = 0 ?>
    <?php $total_cost = 0.0 ?>
	<?php foreach ($tpl->get('threads') as $thread): ?>
        <?php $count += 1 ?>
        <?php $total_cost += (float) $thread->task_sum_cost ?>
        
		<tr class="pr<?php echo $thread->state == 'opened' ? 'None' : '-1' ?>">
			<td>
				<a href="<?php echo $tpl->url('thread', 'id', $thread->id) ?>">
                    #<?php echo $thread->id ?>
                </a>
			</td>
			<td>
			<?php echo $tpl->getLang('state_'.$thread->state) ?>
			</td>
			<td>
				<?php if ($thread->label_name === NULL): ?>
					<i style="color: #777"><?php echo $tpl->getLang('issue_type_no_specified') ?></i>
				<?php else: ?>
					<?php echo $thread->label_name ?>
				<?php endif ?>
			</td>
			<td><?php echo $thread->title ?></td>
			<td>
                <?php if ($thread->coordinator === NULL): ?>
                    <i style="color: #777"><?php echo $tpl->getLang('none') ?></i>
                <?php else: ?>
                    <?php echo $tpl->user_name($thread->coordinator) ?>
                <?php endif ?>
            </td>
            <td>
                <?php echo $tpl->date($thread->create_date) ?>
            </td>
            <td>
                <?php echo $tpl->date($thread->last_activity_date) ?>
            </td>
			<td>
				<?php if ($thread->close_date === NULL): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $tpl->date($thread->close_date) ?><br />
					<?php $s = $tpl->getLang('report_priority').': ' .
                        $tpl->date_diff_days($tpl->get('thread')->create_date,
                                             $tpl->get('thread')->close_date, '%a') ?>
					<?php echo str_replace(' ', '&nbsp;', $s) ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($thread->task_sum_cost === NULL): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $thread->task_sum_cost ?>
				<?php endif ?>
			</td>
			<td>
		<a href="<?php echo $tpl->url('tasks', 'issue', $thread->id, 'state', 0) ?>">
				<?php echo $thread->task_count_closed ?>
		</a>
			/
		<a href="<?php echo $tpl->url('tasks', 'issue', $thread->id) ?>">
				<?php echo $thread->task_count ?>
		</a>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $tpl->getLang('report_total') ?></th>
		<td colspan="6"><?php echo $count ?></td>
		<td colspan="3"><?php echo $total_cost ?></td>
	</tr>
</table>
