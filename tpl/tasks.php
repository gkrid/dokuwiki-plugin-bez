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
		<select name="date_type">
			<option <?php if ($value['date_type'] == 'plan') echo 'selected' ?>
				value="plan"><?php echo $bezlang['plan_date'] ?></option>
			<option <?php if ($value['date_type'] == 'open') echo 'selected' ?>
				value="open"><?php echo $bezlang['open_date'] ?></option>
			<option <?php if ($value['date_type'] == 'closed') echo 'selected' ?>
				value="closed"><?php echo $bezlang['close_date'] ?></option>
		</select>:
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
	<th><?php echo $bezlang['state'] ?></th>
	<th><?php echo $bezlang['task_type'] ?></th>
	<th><?php echo $bezlang['description'] ?></th>
	
	<th><?php echo $bezlang['executor'] ?></th>
	<th><?php echo $bezlang['plan'] ?></th>
	<th><?php echo $bezlang['cost'] ?></th>
	
	<th><?php echo $bezlang['closed'] ?></th>
	<th><?php echo $bezlang['hours_no'] ?></th>
	
</tr>
<?php foreach ($template['tasks'] as $task): ?>
	<tr class="pr<?php echo $task['priority'] ?>" data-bez-row-id="<?php echo $task['id'] ?>">
		<td>
            <a href="?id=<?php echo $this->id('task', 'tid', $task['id']) ?>">
               <?php if (!empty($task['issue'])) echo '#'.$task['issue'] ?>
		       #z<?php echo $task['id'] ?>
	       </a>
		</td>
		<td>
			<?php echo lcfirst($task['state']) ?>
			<?php if ($task['priority'] == '0'): ?>
			(<?php echo lcfirst($bezlang['task_outdated']) ?>)
			<?php endif ?>
		</td>
		<td>
			<?php if ($task['tasktype'] == ''): ?>
				<em>---</em>
			<?php else: ?>
				<?php echo $task['tasktype'] ?>
			<?php endif ?>
		</td>
		<td>
			<div style="max-width:200px;max-height:60px;overflow:hidden;">
			<?php echo $task['task'] ?>
			</div>
			 <a class="bez_show_single_desc" href="#">(...)</a>
			</td>
		<td><?php echo $task['executor'] ?></td>
		
		<td>
		<?php if ($task['plan_date'] != ''): ?>
			<?php echo $task['plan_date'] ?>
			<?php if ($task['raw_state'] == '0'): ?>
				(<?php echo $helper->days_left($task['plan_date']) ?>)
			<?php endif ?>
			<?php if ($task['all_day_event'] == '0'): ?>
				<?php echo $task['start_time'] ?>&nbsp;-&nbsp;<?php echo $task['finish_time'] ?>
			<?php endif ?>
		<?php else: ?>
			<em>---</em>
		<?php endif ?>
		</td>

		<td>
			<?php if ($task['cost'] == ''): ?>
				<em>---</em>
			<?php else: ?>
				<?php echo $task['cost'] ?>
			<?php endif ?>
		</td>

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
		</tr>
		<tr class="bez_desc_row task<?php echo $task['id'] ?>">
			<td colspan="10">
				<?php echo $task['task'] ?>
			</td>
		</tr>
		<?php if ($template['view'] == 'realization'): ?>
		<tr class="bez_desc_row task<?php echo $task['id'] ?>">
			<td colspan="10">
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
		<td colspan="5"><?php echo $template['tasks_stats']['total'] ?></td>
		<td colspan="2"><?php echo $template['tasks_stats']['totalcost'] ?></td>
		<td colspan="1"><?php echo $template['tasks_stats']['totalhours'] ?></td>
	</tr>
</table>

[ <a class="" href="
	<?php echo $helper->mailto('',
	'[BEZ] '.$bezlang['tasks_juxtaposition'],
	DOKU_URL . 'doku.php?id='.$_GET['id']) ?>">
	✉ <?php echo $bezlang['send_mail'] ?>
</a> ]

</div>
