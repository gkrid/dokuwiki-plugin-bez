<div class="bez_filter_form">
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('tasks') ?>" method="POST">
<label><?php echo $bezlang['issue'] ?>:
	<select name="issue">
		<option <?php if ($value['issue'] == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
	<?php foreach ($template['issues'] as $issue_id): ?>
		<option <?php if ($value['issue'] == $issue_id) echo 'selected' ?>
			value="<?php echo $issue_id ?>">#<?php echo $issue_id ?></option>
	<?php endforeach ?>
	</select>
</label>

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

<label><?php echo $bezlang['state'] ?>:
	<select name="taskstate">
		<option <?php if ($value['taskstate'] == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
	<?php foreach ($template['states'] as $key => $name): ?>
		<option <?php if ($value['taskstate'] == (string)$key) echo 'selected' ?>
			value="<?php echo $key ?>"><?php echo $name ?></option>
	<?php endforeach ?>
	</select>
</label>

<label><?php echo $bezlang['task_type'] ?>:
	<select name="tasktype">
		<option <?php if ($value['tasktype'] == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<option <?php if ($value['tasktype'] == '-none') echo 'selected' ?>
			value="-none">-- <?php echo $bezlang['none'] ?> --</option>
	<?php foreach ($template['tasktypes'] as $key => $name): ?>
		<option <?php if ($value['tasktype'] == (string)$key) echo 'selected' ?>
			value="<?php echo $key ?>"><?php echo $name ?></option>
	<?php endforeach ?>
	</select>
</label>

<label><?php echo $bezlang['executor'] ?>:
	<select name="executor">
		<option <?php if ($value['executor'] == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
	<optgroup label="<?php echo $bezlang['users'] ?>">
		<?php foreach ($template['executors'] as $nick => $name): ?>
			<option <?php if ($value['executor'] == $nick) echo 'selected' ?>
				value="<?php echo $nick ?>"><?php echo $name ?></option>
		<?php endforeach ?>
	</optgroup>
	
	<optgroup label="<?php echo $bezlang['groups'] ?>">
		<?php foreach ($template['groups'] as $name): ?>
			<?php $group = "@$name" ?>
			<option <?php if ($value['executor'] == $group) echo 'selected' ?>
				value="<?php echo $group ?>"><?php echo $group ?></option>
		<?php endforeach ?>
	</optgroup>
	</select>
	
</label>


<label><?php echo $bezlang['description'] ?>:
	<input name="task" value="<?php echo $value['task'] ?>" />
</label>

<label><?php echo $bezlang['evaluation'] ?>:
	<input name="reason" value="<?php echo $value['reason'] ?>" />
</label>

<div class="time_filter">
	<label>
		<strong>
			<?php if ($nparams['taskstate'] == '2'): ?>
				<?php echo $bezlang['reject_date'] ?>:
			<?php elseif ($template['view'] == 'realization'): ?>
				<?php echo $bezlang['close_date'] ?>:
			<?php else: ?>
				<?php echo $bezlang['report_date'] ?>:
			<?php endif ?>
		</strong>
	</label>
	<label><?php echo $bezlang['month'] ?>:
		<select name="month">
			<option <?php if ($value['month'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['months'] as $id => $month): ?>
			<option <?php if ($value['month'] == $id) echo 'selected' ?>
				value="<?php echo $id ?>"><?php echo $bezlang[$month] ?></option>
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
	<label><input type="submit" value="<?php echo $bezlang['filter'] ?>" /></label>
</div>
</form>
</div>

<?php if ($template['view'] == 'realization'): ?>
	[ <a href="#" id="bez_show_desc">
		<span class="show"><?php echo $bezlang['show_desc_and_eval'] ?></span>
		<span class="hide" style="display:none"><?php echo $bezlang['hide_desc_and_eval'] ?></span>
		</a> ]
<?php else: ?>
	[ <a href="#" id="bez_show_desc">
		<span class="show"><?php echo $bezlang['show_desc'] ?></span>
		<span class="hide" style="display:none"><?php echo $bezlang['hide_desc'] ?></span>
	
	</a> ]
<?php endif ?>

<table class="bez bez_sumarise">
<tr>
	<th><?php echo $bezlang['id'] ?></th>
	<th><?php echo $bezlang['class'] ?></th>
	<th><?php echo $bezlang['state'] ?></th>
	<th><?php echo $bezlang['task_type'] ?></th>
	
	<th><?php echo $bezlang['executor'] ?></th>
	
	<?php if ($template['view'] == 'realization'): ?>
		<th><?php echo $bezlang['cost'] ?></th>
	<?php endif ?>
	
	<?php if ($template['view'] == 'plan'): ?>
		<th><?php echo $bezlang['date'] ?></th>
		<th><?php echo $bezlang['plan'] ?></th>
	<?php endif ?>
	
	<?php if ($template['view'] == 'realization'): ?>
		<th><?php echo $bezlang['closed'] ?></th>
		<th><?php echo $bezlang['hours_no'] ?></th>
	<?php endif ?>
	
</tr>
<?php foreach ($template['tasks'] as $task): ?>
	<tr class="pr<?php echo $task['priority'] ?>">
		<td><?php echo $this->html_task_link($task['issue'], $task['id']) ?>
		</td>
		<td><?php echo lcfirst($task['action']) ?></td>
		<td>
			<?php echo lcfirst($task['state']) ?>
		</td>
		<td>
			<?php if ($task['tasktype'] == ''): ?>
				<em>---</em>
			<?php else: ?>
				<?php echo $task['tasktype'] ?>
			<?php endif ?>
		</td>
		<td><?php echo $task['executor'] ?></td>
		
		<?php if ($template['view'] == 'realization'): ?>
		<td>
			<?php if ($task['cost'] == ''): ?>
				<em>---</em>
			<?php else: ?>
				<?php echo $task['cost'] ?>
			<?php endif ?>
		</td>
		<?php endif ?>
		<?php if ($template['view'] == 'plan'): ?>
		<td>
			<?php echo $helper->time2date($task['date']) ?> (<?php echo $helper->string_time_to_now($task['date']) ?>)
		</td>
		<td>
		<?php if ($task['plan_date'] != ''): ?>
			<?php echo $task['plan_date'] ?>
			<?php if ($task['all_day_event'] == '0'): ?>
				<?php echo $task['start_time'] ?>&nbsp;-&nbsp;<?php echo $task['finish_time'] ?>
			<?php endif ?>
		<?php else: ?>
			<em>---</em>
		<?php endif ?>
		</td>
		<?php endif ?>
		<?php if ($template['view'] == 'realization'): ?>
			<td>
				<?php if ($task['state'] == $bezlang['task_opened']): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $helper->time2date($task['close_date']) ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($task['start_time'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $task['hours'] ?>
				<?php endif ?>
			</td>
		<?php endif ?>
		</tr>
		<?php
			if ($template['view'] == 'realization')
				$colspan = 8;
			else
				$colspan = 7;
			?>
		<tr class="bez_desc_row">
			<td colspan="<?php echo $colspan ?>">
				<?php echo $task['task'] ?>
			</td>
		</tr>
		<?php if ($template['view'] == 'realization'): ?>
		<tr class="bez_desc_row">
			<td colspan="<?php echo $colspan ?>">
				<?php if ($task['reason'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $task['reason'] ?>
				<?php endif ?>
			</td>
		</tr>
		<?php endif ?>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<?php if ($template['view'] == 'realization'): ?>
			<td colspan="4"><?php echo $template['tasks_stats']['total'] ?></td>
			<td colspan="2"><?php echo $template['tasks_stats']['totalcost'] ?></td>
			<td colspan="2"><?php echo $template['tasks_stats']['totalhours'] ?></td>
		<?php else: ?>
			<td colspan="6"><?php echo $template['tasks_stats']['total'] ?></td>
		<?php endif ?>
	</tr>
</table>


[ <a class="" href="<?php echo $template['ical_link'] ?>">
	📅 <?php echo $bezlang['download_in_icalendar'] ?>
</a> ]
[ <a class="" href="
	<?php echo $helper->mailto('',
	'[BEZ] '.$bezlang['tasks_juxtaposition'],
	DOKU_URL . 'doku.php?id='.$_GET['id']) ?>">
	✉ <?php echo $bezlang['send_mail'] ?>
</a> ]

</div>
