<a style="display:none;" href="#" class="show_tasks_hidden"><?php echo $bezlang['show_tasks_hidden'] ?></a>
<?php foreach ($params['tasks'] as $task): ?>
		<a name="z<?php echo $task['id'] ?>"></a>
		<div id="z<?php echo $task['id'] ?>" class="task
			<?php
				switch($task['state']) {
					case $bezlang['task_opened']:
						echo 'opened';
						break;
					case $bezlang['task_done']:
						echo 'closed';
						break;
					case $bezlang['task_rejected']:
						echo 'rejected';
						break;
				}
			?>">

		<div class="bez_timebox">
			<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($task['date']) ?></span>
			<?php if ($task['state'] != $bezlang['task_opened']): ?>
				<span>
					<strong><?php echo $task['state']?>:</strong>
					<?php echo $helper->time2date($task['close_date']) ?>
				</span>
			<?php endif ?>
		</div>

		<h2>
			<a href="<?php echo $this->issue_uri($task['issue']).'#z'.$task['id'] ?>">#z<?php echo $task['id'] ?></a>
			<?php echo lcfirst($task['action']) ?>
			(<?php echo lcfirst($task['state']) ?>)
		</h2>

		<table>	
		<tr>
				<th><?php echo $bezlang['reporter'] ?>:</th>
				<td><?php echo $task['reporter'] ?></td>

				<th><?php echo $bezlang['executor'] ?>:</th>
				<td><?php echo $task['executor'] ?></td>

				<?php if ($task['cost'] != 0): ?>
					<th><?php echo $bezlang['cost'] ?>:</th>
					<td><?php echo $task['cost'] ?></td>
				<?php endif ?>
		</tr>
		</table>	

		<h3><?php echo $bezlang['description'] ?></h3>
		<?php echo $task['task'] ?>

		<?php if ($task['rejected']): ?>
			<h3><?php echo $bezlang['reason'] ?></h3>
			<?php echo $task['reason'] ?>
		<?php endif ?>

		<?php if ($template['issue_opened'] && ($template['user_is_coordinator'] || $task['executor_nick'] == $template['user'])): ?> 
			<a class="bds_inline_button bds_edit_button"
			href="?id=<?php echo $this->id('issue_show', $template['issue']['id'], 'edit', 'task', $task['id']) ?>#z_">
				<?php echo $bezlang['edit'] ?>

		<a class="bds_inline_button bds_send_button" href="
			<?php echo $helper->mailto($task['executor_email'],
			$bezlang['task'].': #'.$task['issue'].' '.$template['issue']['title'].' | #z'.$task['id'].' '.$task['action'],
			$template['uri'].'#z'.$task['id']) ?>">
			✉ <?php echo $bezlang['send_mail'] ?>
		</a>
		<?php endif ?>
		</div>
<?php endforeach ?>
<?php if ($template['issue_opened'] && ($template['user_is_coordinator'] || strstr($params['task_action'], 'update'))): ?> 
	<a style="display:none;" href="#z_" class="add_task"><?php echo $bezlang['add_task'] ?></a>
	<form class="<?php $e = explode(':', $params['task_action']); echo $e[0] ?> task_form"
			action="<?php echo $template['uri'] ?>:<?php echo $params['task_action'] ?>#z_" method="POST">
			<?php foreach ($params['hidden'] as $name => $value): ?>
				<input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>" />
			<?php endforeach ?>
			<fieldset class="bds_form">
			<?php if ($template['issue_opened'] && $template['user_is_coordinator']): ?> 
				<?php if (count($template['causes']) == 0) : ?>
					<div class="row" >
						<div style="display:table-cell"><br><br></div>
						<label style="position: relative">
							<div class="info" style="position: absolute; left: -7em; width:45em;"><?php echo $bezlang['info_no_causes_added'] ?></div>
						</label>
					</div>
				<?php endif ?>
				<?php if (strpos($params['task_action'], 'update') === 0): ?>
					<div class="row">
					<label for="id"><?php echo $bezlang['id'] ?>:</label>
					<span><strong>#z<?php echo $template['task_id'] ?></strong></span>
					</div>
				<?php endif ?>
				<div class="row">
				<label for="executor"><?php echo $bezlang['executor'] ?>:</label>
				<span>
				<select name="executor" id="executor">
				<?php foreach ($template['users'] as $nick => $name): ?>
					<option <?php if ($value['executor'] == $nick) echo 'selected' ?>
					 value="<?php echo $nick ?>"><?php echo $name ?></option>
				<?php endforeach ?>
				</select>
				</span>
				</div>
				<div class="row">
				<label for="action"><?php echo $bezlang['action'] ?>:</label>
				<span>
				<?php if (isset($params['hidden']['cause'])) : ?>
					<select name="action" id="action">
					<?php foreach ($template['taskactions'] as $key => $name): ?>
						<option <?php if ($value['action'] == $key) echo 'selected' ?>
						 value="<?php echo $key ?>"><?php echo $name ?></option>
					<?php endforeach ?>
					</select>
				<?php else : ?>
					<strong>
						<?php echo $bezlang['correction'] ?>
					</strong>
				<?php endif ?>
				</span>
				</div>

				<div class="row">
					<label for="task"><?php echo $bezlang['description'] ?>:</label>
					<span><textarea name="task" id="task"><?php echo $value['task'] ?></textarea></span>
				</div>

				<div class="row">
					<label for="cost"><?php echo $bezlang['cost'] ?>:</label>
					<span><input name="cost" id="cost" value="<?php echo $value['cost'] ?>"></span>
				</div>
				<?php endif ?>
				<?php if (strstr($params['task_action'], 'update')): ?>
					<div class="row">
					<label for="task_state"><?php echo $bezlang['task_state'] ?>:</label>
					<span>
					<select name="state" id="task_state">
					<?php foreach ($template['task_states'] as $code => $name): ?>
						<option <?php if ($value['state'] == $code) echo 'selected' ?>
						 value="<?php echo $code?>"><?php echo $name ?></option>
					<?php endforeach ?>
					</select>
					</span>
					</div>
					<div class="row">
						<label for="reason"><?php echo $bezlang['reason'] ?>:</label>
						<span><textarea name="reason" id="reason"><?php echo $value['reason'] ?></textarea></span>
					</div>
				<?php endif ?>
			</fieldset>
			<input type="submit" value="<?php echo $template['task_button'] ?>">
			<a href="?id=<?php echo $this->id('issue_show', $template['issue']['id']) ?>"
			 class="bez_delete_button bez_link_button">
				<?php echo $bezlang['cancel'] ?>
			</a>

		</form>
	<?php endif ?>
