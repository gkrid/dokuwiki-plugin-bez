<?php if (isset($template['issue'])): ?>
	<?php $issue = $template['issue'] ?>
	<?php include "issue_box.php" ?><br>
<?php endif ?>

<?php if (isset($template['cause'])): ?>
	<?php $cause = $template['cause'] ?>
	<div class="bds_block" id="bez_causes">
		<?php include "cause.php" ?>
	</div>
	<br>
<?php endif ?>

<form class="bez_form bez_task_form" action="?id=<?php echo $template['task_action'] ?>" method="POST">
		<fieldset class="bds_form">
			<?php if (isset($nparams['tid'])): ?>
				<div class="row">
				<label for="id"><?php echo $bezlang['id'] ?>:</label>
				<span><strong>#z<?php echo $nparams['tid'] ?></strong></span>
				</div>
				
				<?php if ($template['auth_level'] >= 15 && $template['task_action'] == 'task_form'): ?>
				<div class="row">
					<label for="cause"><?php echo ucfirst($bezlang['cause']) ?>:</label>
					<span>
						<select name="cause" id="cause">
							<option <?php if ($value['cause'] == '') echo 'selected' ?>
								value="">--- <?php echo $bezlang['correction'] ?> ---</option>

							<?php foreach ($template['causes'] as $cause): ?>
								<option <?php if ($value['cause'] == $cause['id']) echo 'selected' ?>
								 value="<?php echo $cause['id'] ?>">#p<?php echo $cause['id'] ?></option>
							<?php endforeach ?>
						</select>
					</span>
				</div>
				<?php endif ?>
			<?php endif ?>
			<div class="row">
			<label for="executor"><?php echo $bezlang['executor'] ?>:</label>
			<span>
			<select name="executor" id="executor">
				<option value="">--- <?php echo $bezlang['select'] ?>---</option>
			<?php foreach ($template['users'] as $nick => $name): ?>
				<option <?php if ($value['executor'] == $nick) echo 'selected' ?>
				 value="<?php echo $nick ?>"><?php echo $name ?></option>
			<?php endforeach ?>
			</select>
			</span>
			</div>
			
			<?php if (!isset($template['issue']) || isset($template['cause'])): ?> 
				<div class="row">
				<label for="executor"><?php echo $bezlang['task_type'] ?>:</label>
				<span>
					<?php if (isset($nparams['tasktype']) && $template['auth_level'] < 20): ?>
						<input type="hidden" name="tasktype" value="<?php echo $value['tasktype'] ?>">
						<strong>
						<?php echo $template['tasktype_name'] ?>
						</strong>
					<?php else: ?>
						<select name="tasktype">
							<option <?php if ($value['tasktype'] == '') echo 'selected' ?> value="">-- <?php echo $bezlang['select'] ?> --</option>
							<?php foreach ($template['tasktypes'] as $tasktype): ?>
								<option <?php if ($value['tasktype'] == $tasktype->id) echo 'selected' ?> value="<?php echo $tasktype->id ?>"><?php echo $tasktype->type ?></option>
							<?php endforeach ?>
						</select>
					<?php endif ?>
				</span>
				</div>
			<?php endif ?>
					
			<div class="row">
			<label for="action"><?php echo $bezlang['class'] ?>:</label>
			<span>
				<strong>
					<?php if (!isset($template['issue'])): ?>
						<?php echo $bezlang['programme'] ?>
					<?php elseif (!isset($template['cause'])): ?>
						<?php echo $bezlang['correction'] ?>
						<input type="hidden" name="action" value="0" />
					<?php elseif ($template['cause']['potential'] == 0): ?>
						<?php echo $bezlang['corrective_action'] ?>
						<input type="hidden" name="action" value="1" />
					<?php else: ?>
						<?php echo $bezlang['preventive_action'] ?>
						<input type="hidden" name="action" value="2" />
					<?php endif ?>
				</strong>
			</span>
			</div>

			<div class="row">
				<label for="task"><?php echo $bezlang['description'] ?>:</label>
				<span><textarea name="task" id="task"><?php echo $value['task'] ?></textarea></span>
			</div>
			
			<div class="row task_plan_field">
				<label for="plan_date"><?php echo $bezlang['plan_date'] ?>:</label>
				<span>
					<input name="plan_date" style="width:90px;" value="<?php echo $value['plan_date'] ?>"/> <label><input type="checkbox" name="all_day_event" value="1" 
				<?php if (isset($value['all_day_event']) && $value['all_day_event'] != '0'): ?>
					checked
				<?php endif ?> /> <?php echo $bezlang['all_day_event'] ?></label>
				</span>
			</div>
			
			<div class="row task_plan_field">
				<label for="start_time"><?php echo $bezlang['start_time'] ?>:</label>
				<span>
					<input name="start_time" style="width:60px;" class="bez_timepicker" value="<?php echo $value['start_time'] ?>"/>
				</span>
			</div>
			
			<div class="row task_plan_field">
				<label for="finish_time"><?php echo $bezlang['finish_time'] ?>:</label>
				<span>
					<input name="finish_time" style="width:60px;" class="bez_timepicker" value="<?php echo $value['finish_time'] ?>"/>
				</span>
			</div>

			<div class="row">
				<label for="cost"><?php echo $bezlang['cost'] ?>:</label>
				<span><input name="cost" id="cost" value="<?php echo $value['cost'] ?>"></span>
			</div>
			<?php if (isset($nparams['tid'])): ?>
				<div class="row">
				<label for="task_state"><?php echo $bezlang['task_state'] ?>:</label>
				<span>
					<strong><?php echo $template['state'] ?></strong>
				</span>
				</div>
				<?php if ($template['raw_state'] != 0): ?>
				<div class="row">
					<label for="reason">
						<?php if ($template['raw_state'] == 1): ?>
							<?php echo $bezlang['evaluation'] ?>
						<?php else: ?>
							<?php echo $bezlang['reason'] ?>
						<?php endif ?>
					</label>
					<span><textarea name="reason" id="reason"><?php echo $value['reason'] ?></textarea></span>
				</div>
				<?php endif ?>
			<?php endif ?>
		</fieldset>
		<input type="submit" value="<?php echo $template['task_button'] ?>">
		<a href="?id=<?php
			if (!isset($template['issue']))
				echo $this->id('show_task', 'tid', $nparams['tid']);
			elseif (isset($nparams[tid]))
				echo $this->id('issue_task', 'id', $template['issue']['id'], 'tid', $nparams[tid]);
			else
				echo $this->id('issue_tasks', 'id', $template['issue']['id']);
			?>"
		 class="bez_delete_button bez_link_button">
			<?php echo $bezlang['cancel'] ?>
		</a>

	</form>

<a name="z_"></a>
