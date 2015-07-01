<div class="bez_filter_form">
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('issues') ?>" method="POST">
<fieldset>
<div>
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
		<?php foreach ($template['coordinators'] as $nick => $name): ?>
			<option <?php if ($value['coordinator'] == $nick) echo 'selected' ?>
				value="<?php echo $nick ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>
</div>
<div>
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
	<input type="submit" value="<?php echo $bezlang['filter'] ?>" />
</div>

</fieldset>
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
		<th><?php echo $bezlang['cost'] ?></th>
		<th><?php echo $bezlang['opened_tasks'] ?></th>
	</tr>
	<?php foreach ($template['issues'] as $issue): ?>
		<tr class="pr<?php echo $issue['priority'] ?>">
			<td><?php echo $this->html_issue_link($issue['id']) ?></td>
			<td><?php echo $issue['state'] ?></td>
			<td><?php echo $issue['type'] ?></td>
			<td><?php echo $issue['title'] ?></td>
			<td><?php echo $issue['coordinator'] ?></td>
			<td><?php echo $helper->string_time_to_now($issue['date']) ?></td>
			<td>
				<?php if ($issue['last_mod'] == ''): ?>
					<em><?php echo $bezlang['ns'] ?></em>
				<?php else: ?>
					<?php echo $helper->string_time_to_now($issue['last_mod']) ?>
				<?php endif ?>
			</td>
			<td>
				<?php echo $issue['cost'] ?>
			</td>
			<td>
		<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('tasks', 'issue', $issue['id'], 'state', 0) ?>">
				<?php echo $issue['tasks_opened'] ?>
		</a>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td colspan="6"><?php echo count($template['issues']) ?></td>
		<td colspan="2"><?php echo $template['total_cost'] ?></td>
	</tr>
</table>

</div>
