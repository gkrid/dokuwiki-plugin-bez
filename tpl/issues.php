<div class="bez_filter_form">
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('issues') ?>" method="POST">
	<label><?php echo $bezlang['state'] ?>:
		<select name="state">
			<option <?php if ($value['state'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['states'] as $key => $name): ?>
			<option <?php if ($value['state'] == (string)$key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $bezlang['just_type'] ?>:
		<select name="type">
			<option <?php if ($value['type'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
			<option <?php if ($value['type'] == '-none') echo 'selected' ?>
			value="-none">--- <?php echo $bezlang['issue_type_no_specified'] ?> ---</option>
		<?php foreach ($template['issue_types'] as $key => $name): ?>
			<option <?php if ($value['type'] == (string)$key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<label><?php echo $bezlang['coordinator'] ?>:
		<select name="coordinator">
			<option <?php if ($value['coordinator'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
			<option <?php if ($value['coordinator'] == '-none') echo 'selected' ?>
				value="-none">--- <?php echo $bezlang['none'] ?> ---</option>
		<optgroup label="<?php echo $bezlang['users'] ?>">
			<?php foreach ($template['coordinators'] as $nick => $name): ?>
				<option <?php if ($value['coordinator'] == $nick) echo 'selected' ?>
					value="<?php echo $nick ?>"><?php echo $name ?></option>
			<?php endforeach ?>
	</optgroup>	
	<optgroup label="<?php echo $bezlang['groups'] ?>">
		<?php foreach ($template['groups'] as $name): ?>
			<?php $group = "@$name" ?>
			<option <?php if ($value['coordinator'] == $group) echo 'selected' ?>
				value="<?php echo $group ?>"><?php echo $group ?></option>
		<?php endforeach ?>
	</optgroup>
	</select>
	</label>
		
	<label><?php echo $bezlang['root_cause'] ?>:
		<select name="rootcause">
			<option <?php if ($value['type'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['rootcauses'] as $key => $name): ?>
			<option <?php if ($value['rootcause'] == $key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<label><?php echo $bezlang['title'] ?>:
		<input name="title" value="<?php echo $value['title'] ?>" />
	</label>


	<label><?php echo $bezlang['year'] ?>:
		<select name="year">
			<option <?php if ($value['year'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['years'] as $year): ?>
			<option <?php if ($value['year'] == $year) echo 'selected' ?>
				value="<?php echo $year ?>"><?php echo $year ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<label><?php echo $bezlang['sort_by_open_date'] ?>:
			<input type="checkbox" name="sort_open"
			<?php if (isset($value['sort_open']) && $value['sort_open'] == 'on') echo 'checked="checked"' ?>>
	</label>
	<label><input type="submit" value="<?php echo $bezlang['filter'] ?>" /></label>
</form>
</div>

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
				<?php echo $this->html_issue_link($issue['id']) ?>
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
