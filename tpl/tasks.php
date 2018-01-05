<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<?php if ($tpl->factory('task')->permission() >= BEZ_TABLE_PERMISSION_INSERT): ?>
    <a href="<?php echo $tpl->url('task_form') ?>" class="bez_start_button" id="bez_report_task_button">
        <?php echo $tpl->getLang('add_task') ?>
    </a>
<?php endif ?>

<br /><br />

<div class="bez_filter_form">
<form action="<?php echo $tpl->url('tasks') ?>" method="POST">


<label><?php echo $tpl->getLang('reporter') ?>:
    <select name="original_poster">
        <option <?php if ($tpl->value('original_poster') == '-all') echo 'selected' ?>
                value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
        <optgroup label="<?php echo $tpl->getLang('users') ?>">
            <?php foreach ($tpl->get('users') as $nick => $name): ?>
                <option <?php if ($tpl->value('original_poster') == $nick) echo 'selected' ?>
                        value="<?php echo $nick ?>"><?php echo $name ?></option>
            <?php endforeach ?>
        </optgroup>
        <optgroup label="<?php echo $tpl->getLang('groups') ?>">
            <?php foreach ($tpl->get('groups') as $name): ?>
                <?php $group = "@$name" ?>
                <option <?php if ($tpl->value('original_poster') == $group) echo 'selected' ?>
                        value="<?php echo $group ?>"><?php echo $group ?></option>
            <?php endforeach ?>
        </optgroup>
    </select>
</label>

<label><?php echo $tpl->getLang('class') ?>:
	<select name="type">
		<option <?php if ($tpl->value('type') == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
	<?php foreach (\dokuwiki\plugin\bez\mdl\Task::get_types() as $type): ?>
		<option <?php if ($tpl->value('type') == $type) echo 'selected' ?>
			value="<?php echo $type ?>"><?php echo $tpl->getLang('task_type_' . $type) ?></option>
	<?php endforeach ?>
	</select>
</label>

<label><?php echo $tpl->getLang('state') ?>:
	<select name="state">
		<option <?php if ($tpl->value('state') == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
	<?php foreach (\dokuwiki\plugin\bez\mdl\Task::get_states() as $state): ?>
		<option <?php if ($tpl->value('state') == $state) echo 'selected' ?>
			value="<?php echo $state ?>"><?php echo lcfirst($tpl->getLang('task_' . $state)) ?></option>
	<?php endforeach ?>
	</select>
</label>

<label><?php echo $tpl->getLang('task_type') ?>:
	<select name="task_program_id">
		<option <?php if ($tpl->value('task_program_id') == '-all') echo 'selected' ?>
			value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
		<option <?php if ($tpl->value('task_program_id') == '-none') echo 'selected' ?>
			value="-none">-- <?php echo $tpl->getLang('none') ?> --</option>
	<?php foreach ($tpl->get('task_programs') as $task_program): ?>
		<option <?php if ($tpl->value('task_program_id') == $task_program->id) echo 'selected' ?>
			value="<?php echo $task_program->id ?>"><?php echo $task_program->name ?></option>
	<?php endforeach ?>
	</select>
</label>

<label><?php echo $tpl->getLang('executor') ?>:
    <select name="assignee">
        <option <?php if ($tpl->value('assignee') == '-all') echo 'selected' ?>
                value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
        <optgroup label="<?php echo $tpl->getLang('users') ?>">
            <?php foreach ($tpl->get('users') as $nick => $name): ?>
                <option <?php if ($tpl->value('assignee') == $nick) echo 'selected' ?>
                        value="<?php echo $nick ?>"><?php echo $name ?></option>
            <?php endforeach ?>
        </optgroup>
        <optgroup label="<?php echo $tpl->getLang('groups') ?>">
            <?php foreach ($tpl->get('groups') as $name): ?>
                <?php $group = "@$name" ?>
                <option <?php if ($tpl->value('assignee') == $group) echo 'selected' ?>
                        value="<?php echo $group ?>"><?php echo $group ?></option>
            <?php endforeach ?>
        </optgroup>
    </select>
</label>


<label><?php echo $tpl->getLang('description') ?>:
	<input name="content" value="<?php echo $tpl->value('content') ?>" />
</label>


<div class="time_filter">
	<label>
		<select name="date_type">
			<option <?php if ($tpl->value('date_type') == 'plan_date') echo 'selected' ?>
				value="plan_date"><?php echo $tpl->getLang('plan_date') ?></option>
			<option <?php if ($tpl->value('date_type') == 'create_date') echo 'selected' ?>
				value="create_date"><?php echo $tpl->getLang('open_date') ?></option>
			<option <?php if ($tpl->value('date_type') == 'close_date') echo 'selected' ?>
				value="close_date"><?php echo $tpl->getLang('close_date') ?></option>
		</select>:
	</label>
	<label><?php echo $tpl->getLang('month') ?>:
		<select name="month">
			<option <?php if ($tpl->value('month') == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
		<?php foreach ($tpl->get('months') as $nr => $month): ?>
			<option <?php if ($tpl->value('month') == $nr) echo 'selected' ?>
				value="<?php echo $nr ?>"><?php echo $tpl->getLang($month) ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<label><?php echo $tpl->getLang('year') ?>:
		<select name="year">
			<option <?php if ($tpl->value('year') == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $tpl->getLang('all') ?> ---</option>
		<?php foreach ($tpl->get('years') as $year): ?>
			<option <?php if ($tpl->value('year') == $year) echo 'selected' ?>
				value="<?php echo $year ?>"><?php echo $year ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<label><input type="submit" value="<?php echo $tpl->getLang('filter') ?>" /></label>
</div>
</form>
</div>


<table class="bez bez_sumarise">
<tr>
	<th><?php echo $tpl->getLang('id') ?></th>
	<th><?php echo $tpl->getLang('state') ?></th>
	<th><?php echo $tpl->getLang('task_type') ?></th>
	<th><?php echo $tpl->getLang('description') ?></th>
	
	<th><?php echo $tpl->getLang('executor') ?></th>
	<th><?php echo $tpl->getLang('plan') ?></th>
	<th><?php echo $tpl->getLang('cost') ?></th>
	
	<th><?php echo $tpl->getLang('closed') ?></th>
	<th><?php echo $tpl->getLang('hours_no') ?></th>
	
</tr>
<?php $count = 0 ?>
<?php $total_cost = 0.0 ?>
<?php $total_hours = 0.0 ?>
<?php foreach ($tpl->get('tasks') as $task): ?>
    <?php $count += 1 ?>
    <?php $total_cost += (float) $task->cost ?>
    <?php $hours = $tpl->date_diff_hours($task->start_time, $task->finish_time) ?>
    <?php $total_hours += $tpl->time_to_float($hours) ?>
	<tr class="pr<?php echo $task->priority ?>" data-bez-row-id="<?php echo $task->id ?>">
		<td>
            <a href="<?php echo $tpl->url('task', 'tid', $task->id) ?>">
               <?php if ($task->thread_id != '') echo '#'.$task->thread_id ?>
		       #z<?php echo $task->id ?>
	       </a>
		</td>
		<td>
			<?php echo lcfirst($tpl->getLang('task_' . $task->state)) ?>
			<?php if ($task->plan_date < date('%Y-%m-%d')): ?>
			(<?php echo lcfirst($tpl->getLang('task_outdated')) ?>)
			<?php endif ?>
		</td>
		<td>
			<?php if ($task->task_program_id == ''): ?>
				<em>---</em>
			<?php else: ?>
				<?php echo $task->task_program_name ?>
			<?php endif ?>
		</td>
		<td>
			<div style="max-width:200px;max-height:60px;overflow:hidden;">
			<?php echo $task->content_html ?>
			</div>
			 <a class="bez_show_single_desc" href="#">(...)</a>
			</td>
		<td><?php echo $tpl->user_name($task->assignee) ?></td>
		
		<td>
        <?php echo $task->plan_date ?>
        <?php if ($task->state == 'opened'): ?>
            (<?php echo $tpl->date_diff_days($task->plan_date) ?>)
        <?php endif ?>
        <?php if ($task->all_day_event == '0'): ?>
            <?php echo $task->start_time ?>&nbsp;-&nbsp;<?php echo $task->finish_time ?>
        <?php endif ?>
		</td>

		<td>
			<?php if ($task->cost == ''): ?>
				<em>---</em>
			<?php else: ?>
				<?php echo $task->cost ?>
			<?php endif ?>
		</td>

		<td>
			<?php if ($task->state == 'opened'): ?>
				<em>---</em>
			<?php else: ?>
				<?php echo $tpl->date($task->close_date) ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($task->start_time == ''): ?>
				<em>---</em>
			<?php else: ?>
                <?php echo $hours ?>
			<?php endif ?>
		</td>
		</tr>
		<tr class="bez_desc_row task<?php echo $task->id ?>">
			<td colspan="10">
				<?php echo $task->content_html ?>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $tpl->getLang('report_total') ?></th>
		<td colspan="5"><?php echo $count ?></td>
		<td colspan="2"><?php echo $total_cost ?></td>
		<td colspan="1"><?php echo $tpl->float_to_time($total_hours) ?></td>
	</tr>
</table>
</div>
