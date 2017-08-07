<div class="bez_filter_form">
<form action="<?php echo $tpl->url('issues') ?>" method="post">
	<label><?php echo $tpl->getLang('state') ?>:
		<select name="state">
			<option <?php if ($tpl->value('state') === '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
		<?php foreach ($tpl->get_dummy_of('issues')->get_states() as $key => $name): ?>
			<option <?php if ($tpl->value('state') === $key) echo 'selected' ?>
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

<?php die() ?>

<table class="bez bez_sumarise">
	<tr>
		<th><?php echo $bezlang['id'] ?></th>
		<th><?php echo $bezlang['state'] ?></th>
		<th><?php echo $bezlang['type'] ?></th>
		<th><?php echo $bezlang['title'] ?></th>
		<th><?php echo $bezlang['coordinator'] ?></th>
		<th><?php echo $bezlang['date'] ?></th>
		<th><?php echo $bezlang['last_mod_date'] ?></th>
		<th><?php echo $bezlang['closed'] ?></th>
		<th><?php echo $bezlang['cost'] ?></th>
		<th><?php echo $bezlang['closed_tasks'] ?></th>
	</tr>
	<?php foreach ($template['issues'] as $issue): ?>
		<tr class="pr<?php echo $issue['priority'] ?>">
			<td>
				<a href="?id=<?php echo $this->id('issue', 'id', $issue['id']) ?>">
                    #<?php echo $issue['id'] ?>
                </a>
			</td>
			<td>
			<?php echo $issue['state'] ?>
			</td>
			<td>
				<?php if ($issue['type'] == ''): ?>
					<i style="color: #777"><?php echo $bezlang['issue_type_no_specified'] ?></i>
				<?php else: ?>
					<?php echo $issue['type'] ?>
				<?php endif ?>
			</td>
			<td><?php echo $issue['title'] ?></td>
			<td><?php echo $issue['coordinator'] ?></td>
			<td><?php echo $helper->time2date($issue['date']) ?> (<?php echo $helper->string_time_to_now($issue['date']) ?>)</td>
			<td>
				<?php $unix = strtotime($issue['last_activity']) ?>
				<?php echo $helper->time2date($unix) ?> (<?php echo $helper->string_time_to_now($unix) ?>)
			</td>
			<td>
				<?php if ($issue['raw_state'] != 1): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $helper->time2date($issue['last_mod']) ?><br />
					<?php $s = $bezlang['report_priority'].': '.$helper->days((int)$issue['last_mod'] - (int)$issue['date']) ?>
					<?php echo str_replace(' ', '&nbsp;', $s) ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($issue['cost'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $issue['cost'] ?>
				<?php endif ?>
			</td>
			<td>
		<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('tasks', 'issue', $issue['id'], 'state', 0) ?>">
				<?php echo $issue['tasks_closed'] ?>
		</a>
			/
		<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('tasks', 'issue', $issue['id']) ?>">
				<?php echo $issue['tasks_all'] ?>
		</a>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td colspan="7"><?php echo count($template['issues']) ?></td>
		<td colspan="2"><?php echo $template['total_cost'] ?></td>
	</tr>
</table>
[<a class="" href="
		<?php echo $helper->mailto('',
		'[BEZ] '.$bezlang['issues_juxtaposition'],
		DOKU_URL . 'doku.php?id='.$_GET['id']) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>]
</div>
