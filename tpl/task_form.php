<?php
if (isset($template['issue'])) {
	$id = $this->id('issue', 'id', $template['issue']->id, 'action', $template['task_action'], 'tid', $template['task_id']);
} else {
	
}	
?>
<a name="z_"></a>
<form 	class="bez_form bez_task_form"
		action="?id=<?php echo $id ?>" method="POST">
		<input type="hidden" name="id" value="<?php echo $id ?>">
		<fieldset class="bds_form">
			<?php if ($template['action'] === 'task_correction_update'): ?>
				<div class="row">
				<label for="id"><?php echo $bezlang['id'] ?>:</label>
				<span><strong>#z<?php echo $nparams['tid'] ?></strong></span>
				</div>
				
				<?php if ($template['auth_level'] >= 15 && isset($template['issue'])): ?>
				<div class="row">
					<label for="cause"><?php echo ucfirst($bezlang['cause']) ?>:</label>
					<span>
						<select name="cause" id="cause">
							<option <?php if ($value['cause'] == '') echo 'selected' ?>
								value="">--- <?php echo $bezlang['correction'] ?> ---</option>

							<?php foreach ($template['causes'] as $cause): ?>
								<option <?php if ($value['cause'] == $cause->id) echo 'selected' ?>
								 value="<?php echo $cause->id ?>">#p<?php echo $cause->id ?></option>
							<?php endforeach ?>
						</select>
					</span>
				</div>
				<?php endif ?>
			<?php endif ?>
			<div class="row">
			<label for="executor"><?php echo $bezlang['executor'] ?>:</label>
			<span>
			<?php if ($template['auth_level'] >= 15): ?>	
				<select name="executor" id="executor" data-validation="required">
					<option value="">--- <?php echo $bezlang['select'] ?>---</option>
				<?php foreach ($template['users'] as $nick => $name): ?>
					<option <?php if ($value['executor'] == $nick) echo 'selected' ?>
					 value="<?php echo $nick ?>"><?php echo $name ?></option>
				<?php endforeach ?>
				</select>
			<?php else: ?>
				<input type="hidden" name="executor" value="<?php echo $template['user'] ?>">
				<strong>
				<?php echo $template['user_name'] ?>
				</strong>
			<?php endif ?>
			
			
			</span>
			</div>
			
			<div class="row">
			<label for="tasktype"><?php echo $bezlang['task_type'] ?>:</label>
			<span>
				<?php if ($template['auth_level'] < 15): ?>
					<input type="hidden" name="tasktype" value="<?php echo $nparams['tasktype'] ?>">
					<strong>
					<?php echo $template['tasktype_name'] ?>
					</strong>
				<?php else: ?>
					<select id="tasktype" name="tasktype">
						<option <?php if ($value['tasktype'] == '') echo 'selected' ?> value=""><?php echo $bezlang['tasks_no_type'] ?></option>
						<?php foreach ($template['tasktypes'] as $tasktype): ?>
							<option <?php if ($value['tasktype'] == $tasktype->id) echo 'selected' ?> value="<?php echo $tasktype->id ?>"><?php echo $tasktype->type ?></option>
						<?php endforeach ?>
					</select>
				<?php endif ?>
			</span>
			</div>
					
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
				<span>
					<div class="bez_toolbar"></div>
					<textarea name="task" id="task" data-validation="required"><?php echo $value['task'] ?></textarea>
				</span>
			</div>
			
			<div class="row task_plan_field">
				<label for="plan_date"><?php echo $bezlang['plan_date'] ?>:</label>
				<span>
					<input name="plan_date" style="width:90px;" data-validation="required,date" value="<?php echo $value['plan_date'] ?>"/>
				<div style="display:inline" id="task_datapair">
					<?php echo $bezlang['from_hour'] ?>
					<input name="start_time" style="width:60px;" class="time start" value="<?php echo $value['start_time'] ?>"
					data-validation="required,custom"
					data-validation-regexp="^(\d{1,2}):(\d{1,2})$"
					data-validation-depends-on="all_day_event"
					/>
					<?php echo $bezlang['to_hour'] ?>
					<input name="finish_time" style="width:60px;" class="time end" value="<?php echo $value['finish_time'] ?>"
					data-validation="required,custom"
					data-validation-regexp="^(\d{1,2}):(\d{1,2})$"
					data-validation-depends-on="all_day_event"
					/>
				</div>
				</span>
			</div>
			
			<div class="row">
				<label></label>
				<span style="dispaly: block; position:relative; top: -10px;">
					<label><input type="checkbox" name="all_day_event" value="1" 
				<?php if (!isset($value['all_day_event']) || $value['all_day_event'] === '1'): ?>
					checked
				<?php endif ?> /> <?php echo $bezlang['all_day_event'] ?></label></span>
			</div>
			

			<div class="row">
				<label for="cost"><?php echo $bezlang['cost'] ?>:</label>
				<span><input 	type="number" name="cost" id="cost"
								min="0" max="100000" step="50"
								value="<?php echo $value['cost'] ?>"></span>
			</div>
			<?php if (isset($nparams['tid']) && $nparams['tid'] !== '-1'): ?>
				<div class="row">
				<label for="task_state"><?php echo $bezlang['task_state'] ?>:</label>
				<span>
					<strong><?php echo $bezlang[$template['state_string']] ?></strong>
				</span>
				</div>
				
				<?php if ($template['state_string'] != 'task_opened'): ?>
				<div class="row">
					<label for="reason">
						<?php if ($template['state_string'] == 'task_done'): ?>
							<?php echo $bezlang['evaluation'] ?>:
						<?php else: ?>
							<?php echo $bezlang['reason'] ?>:
						<?php endif ?>
					</label>
					<span><textarea name="reason" id="reason"><?php echo $value['reason'] ?></textarea></span>
				</div>
				<?php endif ?>
			<?php endif ?>
			<div class="row">
				<label></label>
				<span style="padding-top:10px;">
					<input type="submit" value="<?php echo isset($template['button']) ? $template['button'] : $bezlang['add'] ?>">
					<a href="?id=<?php
				if (isset($template['issue'])) {
					echo $this->id('issue', 'id', $template['issue']->id);
				} else {
					echo $this->id('show_task', 'tid', $nparams['tid']);
				}
				?>"
				class="bez_delete_button bez_link_button">
					<?php echo $bezlang['cancel'] ?>
				</a>
			</span>
		</fieldset>
		

	</form>


