<div class="bez_filter_form">
<form action="<?php echo $tpl->url('issues') ?>" method="post">
	<label><?php echo $tpl->getLang('state') ?>:
		<select name="full_state">
			<option <?php if ($tpl->value('full_state') === '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
		<?php foreach ($tpl->get_dummy_of('issues')->get_states() as $key => $name): ?>
            <?php //php automaticly convert numeric strings as '12' to integers. Since 7.2 it will be fixed but since then we need to use "==" operator ?>
            <?php // https://stackoverflow.com/questions/4100488/a-numeric-string-as-array-key-in-php ?>
			<option <?php if ($tpl->value('full_state') === (string)$key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $tpl->getLang($name) ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $tpl->getLang('just_type') ?>:
		<select name="type">
			<option <?php if ($tpl->value('type') === '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
			<option <?php if ($tpl->value('type') === '-none') echo 'selected' ?>
			value="-none">--- <?php echo $tpl->getLang('issue_type_no_specified') ?> ---</option>
		<?php foreach ($tpl->get('issuetypes') as $issuetype): ?>
			<option <?php if ($tpl->value('type') === $issuetype->id) echo 'selected' ?>
				value="<?php echo $issuetype->id ?>"><?php echo $issuetype->type ?></option>
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
	<?php foreach ($tpl->get('issues') as $issue): ?>
        <?php $count += 1 ?>
        <?php $total_cost += (float) $issue->cost ?>
        
		<tr class="pr<?php echo $issue->priority ?>">
			<td>
				<a href="<?php echo $tpl->url('issue', 'id', $issue->id) ?>">
                    #<?php echo $issue->id ?>
                </a>
			</td>
			<td>
			<?php echo $issue->state_string ?>
			</td>
			<td>
				<?php if ($issue->type === ''): ?>
					<i style="color: #777"><?php echo $tpl->getLang('issue_type_no_specified') ?></i>
				<?php else: ?>
					<?php echo $issue->type_string ?>
				<?php endif ?>
			</td>
			<td><?php echo $issue->title ?></td>
			<td>
                <?php if ($issue->coordinator === '-proposal'): ?>
                    <i style="color: #777"><?php echo $tpl->getLang('none') ?></i>
                <?php else: ?>
                    <?php echo $tpl->user_name($issue->coordinator) ?>
                <?php endif ?>
            </td>
			<td><?php echo $issue->date_format('date') ?> (<?php echo $issue->days_ago('date') ?>)</td>
			<td>
				<?php echo $issue->date_format('last_activity') ?> (<?php echo $issue->days_ago('last_activity') ?>)
			</td>
			<td>
				<?php if ($issue->full_state === '0' || $issue->full_state === '-proposal'): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $issue->date_format('last_mod') ?><br />
					<?php $s = $tpl->getLang('report_priority').': '.$issue->days_ago('last_mod', 'date') ?>
					<?php echo str_replace(' ', '&nbsp;', $s) ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($issue->cost === ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $issue->float_localized('cost') ?>
				<?php endif ?>
			</td>
			<td>
		<a href="<?php echo $tpl->url('tasks', 'issue', $issue->id, 'state', 0) ?>">
				<?php echo $issue->assigned_tasks_count - $issue->opened_tasks_count ?>
		</a>
			/
		<a href="<?php echo $tpl->url('tasks', 'issue', $issue->id) ?>">
				<?php echo $issue->assigned_tasks_count ?>
		</a>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $tpl->getLang('report_total') ?></th>
		<td colspan="7"><?php echo $count ?></td>
		<td colspan="2"><?php echo $total_cost ?></td>
	</tr>
</table>
</div>
