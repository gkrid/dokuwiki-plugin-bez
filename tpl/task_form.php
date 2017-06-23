<?php
if ($nparams['bez'] === 'issue') {
	$id = $this->id('issue', 'id', $template['issue']->id, 'action', $template['action'], 'tid', $template['tid'], 'kid', $template['kid']);
} elseif ($nparams['bez'] === 'task_form') {
	$id = $this->id('task_form', 'action', $template['action'], 'tid', $template['tid']);
} else {
	$id = $this->id('task', 'action', $template['action'], 'tid', $template['tid']);
}
?>
<a name="z_"></a>
<form 	class="bez_form bez_task_form"
		action="?id=<?php echo $id ?>" method="POST">
		<input type="hidden" name="id" value="<?php echo $id ?>">

		<fieldset class="bds_form">
			<?php if ($template['tid'] !== '-1'): ?>
				<div class="row">
				<label for="id"><?php echo $bezlang['id'] ?>:</label>
				<span><strong>#z<?php echo $template['task']->id ?></strong></span>
				</div>
				
				<?php if (isset($template['issue']) &&
                    $template['task']->acl_of('cause') >= BEZ_PERMISSION_CHANGE): ?>
				<div class="row">
					<label for="cause"><?php echo ucfirst($bezlang['cause']) ?>:</label>
					<span>
						<select name="cause" id="cause">
							<option <?php if ($value['cause'] == '') echo 'selected' ?>
								value="">--- <?php echo $bezlang['correction'] ?> ---</option>

							<?php foreach ($template['causes'] as $cause): ?>
								<option <?php if ($value['cause'] === $cause->id) echo 'selected' ?>
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
			<?php if ($template['task']->acl_of('executor') >= BEZ_PERMISSION_CHANGE): ?>	
				<select name="executor" id="executor" data-validation="required">
					<option value="">--- <?php echo $bezlang['select'] ?>---</option>
				<?php foreach ($template['users'] as $nick => $name): ?>
					<option <?php if ($value['executor'] === $nick) echo 'selected' ?>
					 value="<?php echo $nick ?>"><?php echo $name ?></option>
				<?php endforeach ?>
				</select>
			<?php else: ?>
				<input type="hidden" name="executor" value="<?php echo $value['executor'] ?>">
				<strong>
				<?php echo $this->model->users->get_user_full_name($value['executor']) ?>
				</strong>
			<?php endif ?>
			
			
			</span>
			</div>

			<div class="row">
				<label for="task"><?php echo $bezlang['description'] ?>:</label>
				<span>
                    <?php if ($template['task']->acl_of('plan_date') >= BEZ_PERMISSION_CHANGE): ?>
                        <div class="bez_toolbar"></div>
                    <?php endif ?>
					<textarea name="task" id="task" data-validation="required" <?php if ($template['task']->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>><?php echo $value['task'] ?></textarea>
				</span>
			</div>
			
			<div class="row task_plan_field">
				<label for="plan_date"><?php echo $bezlang['plan_date'] ?>:</label>
				<span>
                    <input name="plan_date" style="width:90px;" data-validation="required,date" value="<?php echo $value['plan_date'] ?>"
                    <?php if ($template['task']->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>
                    />
                    <div style="display:inline" id="task_datapair">
                        <?php echo $bezlang['from_hour'] ?>
                        <input name="start_time" style="width:60px;" class="time start" value="<?php echo $value['start_time'] ?>"
                        data-validation="required,custom"
                        data-validation-regexp="^(\d{1,2}):(\d{1,2})$"
                        data-validation-depends-on="all_day_event"
                        <?php if ($template['task']->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>
                        />
                        <?php echo $bezlang['to_hour'] ?>
                        <input name="finish_time" style="width:60px;" class="time end" value="<?php echo $value['finish_time'] ?>"
                        data-validation="required,custom"
                        data-validation-regexp="^(\d{1,2}):(\d{1,2})$"
                        data-validation-depends-on="all_day_event"
                        <?php if ($template['task']->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>
                        />
                    </div>
				</span>
			</div>
			
			<div class="row">
				<label></label>
				<span style="dispaly: block; position:relative; top: -10px;">
					<label>
                        <?php if ($template['task']->acl_of('all_day_event') >= BEZ_PERMISSION_CHANGE): ?>	
                        <input type="checkbox" name="all_day_event" value="1" 
                            <?php if (!isset($value['all_day_event']) ||
                                        $value['all_day_event'] === '1'): ?>
                                checked
                            <?php endif ?> /> 
                        <?php else: ?>
                             <input type="checkbox" disabled
                            <?php if ($template['task']->all_day_event === '1'): ?>
                                checked
                            <?php endif ?> />
                        <?php endif ?> <?php echo $bezlang['all_day_event'] ?>
                    </label>
                
                </span>
			</div>
			
			<div class="row">
			<label for="tasktype"><?php echo $bezlang['task_type'] ?>:</label>
			<span>
                <select id="tasktype" name="tasktype" <?php if ($template['task']->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>>
                    <option <?php if ($value['tasktype'] == '') echo 'selected' ?> value=""><?php echo $bezlang['tasks_no_type'] ?></option>
                    <?php foreach ($template['tasktypes'] as $tasktype): ?>
                        <option <?php if ($value['tasktype'] == $tasktype->id) echo 'selected' ?> value="<?php echo $tasktype->id ?>"><?php echo $tasktype->type ?></option>
                    <?php endforeach ?>
                </select>
			</span>
			</div>

			<div class="row">
				<label for="cost"><?php echo $bezlang['cost'] ?>:</label>
				<span><input 	type="number" name="cost" id="cost"
								min="0" max="100000" step="50"
								value="<?php echo $value['cost'] ?>"
                                <?php if ($template['task']->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>></span>
			</div>
			<?php if ($template['tid'] !== '-1'): ?>
				<div class="row">
				<label for="task_state"><?php echo $bezlang['task_state'] ?>:</label>
				<span>
					<strong><?php echo $template['task']->state_string ?></strong>
				</span>
				</div>
				
				<?php if (  $template['task']->state === '1' ||
                            $template['task']->state === '2'): ?>
					<div class="row">
						<label for="reason">
							<?php if ($template['task']->state === '1'): ?>
								<?php echo $bezlang['evaluation'] ?>:
							<?php elseif ($template['task']->state === '2'): ?>
								<?php echo $bezlang['reason'] ?>:
							<?php endif ?>
						</label>
						<span>
                            <?php if ($template['task']->acl_of('reason') >= BEZ_PERMISSION_CHANGE): ?>
                                <div class="bez_toolbar"></div>
                            <?php endif ?>
                            <textarea name="reason" id="reason" <?php if ($template['task']->acl_of('reason') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>><?php echo $value['reason'] ?></textarea>
                        </span>
					</div>
				<?php endif ?>

			<?php endif ?>
			<div class="row">
				<label></label>
				<span style="padding-top:10px;">
					<input type="submit" value="<?php echo $template['tid'] === '-1' ? $bezlang['add'] : $bezlang['correct'] ?>">
					<a href="?id=<?php
				if ($nparams['bez'] === 'issue') {
					echo $this->id('issue', 'id', $template['issue']->id);
				} else if ($nparams['bez'] === 'task' && $template['task']->id != '') {
					echo $this->id('task', 'tid', $template['task']->id);
				} else if ($template['tasktype'] != '') {
                    echo $this->id('tasks', 'tasktype', $template['tasktype']);
                } else {
                    echo $this->id('tasks');
                }
				?><?php if ($template['tid'] !== '-1') echo '#z'.$template['tid'] ?>"
				class="bez_delete_button bez_link_button">
					<?php echo $bezlang['cancel'] ?>
				</a>
			</span>
        </div>
    </fieldset>
</form>
