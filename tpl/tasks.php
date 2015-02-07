<div class="bez_filter_form">
<form action="<?php echo $template['uri'] ?>?id=bez:tasks" method="POST">
<fieldset>
<div>
<label><?php echo $bezlang['class'] ?>:
	<select name="action">
		<option <?php if ($value['action'] == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
	<?php foreach ($template['actions'] as $key => $name): ?>
		<option <?php if ($value['action'] == (string)$key) echo 'selected' ?>
			value="<?php echo $key ?>"><?php echo $name ?></option>
	<?php endforeach ?>
	</select>
</label>

<label><?php echo $bezlang['executor'] ?>:
	<select name="executor">
		<option <?php if ($value['executor'] == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
	<?php foreach ($template['executors'] as $nick => $name): ?>
		<option <?php if ($value['executor'] == $nick) echo 'selected' ?>
			value="<?php echo $nick ?>"><?php echo $name ?></option>
	<?php endforeach ?>
	</select>
</label>
</div>

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
<input type="submit" value="<?php echo $bezlang['filter'] ?>" />
</div>
</fieldset>
</form>
</div>

<table class="bez bez_sumarise">
<tr>
	<th><?php echo $bezlang['id'] ?></th>
	<th><?php echo $bezlang['class'] ?></th>
	<th><?php echo $bezlang['state'] ?></th>
	<th><?php echo $bezlang['executor'] ?></th>
	<th><?php echo $bezlang['cost'] ?></th>
	<th><?php echo $bezlang['date'] ?></th>
	<th><?php echo $bezlang['closed'] ?></th>
</tr>
<?php foreach ($template['tasks'] as $task): ?>
	<tr class="pr<?php echo $task['priority'] ?>">
		<td><?php echo $helper->html_task_link($task['issue'], $task['id']) ?></td>
		<td><?php echo $task['action'] ?></td>
		<td><?php echo $task['state'] ?></td>
		<td><?php echo $task['executor'] ?></td>
		<td>
			<?php if ($task['cost'] == ''): ?>
				<em><?php echo $bezlang['ns'] ?></em>
			<?php else: ?>
				<?php echo $task['cost'] ?>
			<?php endif ?>
		</td>
		<td><?php echo $helper->string_time_to_now($task['date']) ?></td>
		<td>
				<?php if ($task['state'] == $bezlang['task_opened']): ?>
					<em><?php echo $bezlang['ns'] ?></em>
				<?php else: ?>
					<?php echo $helper->string_time_to_now($task['close_date']) ?>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td colspan="6"><?php echo count($template['tasks']) ?></td>
	</tr>
</table>

</div>
