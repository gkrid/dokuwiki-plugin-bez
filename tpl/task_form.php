<?php
/* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */
if ($tpl->action() == 'thread') {
	$url = $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', $tpl->param('action', 'add'), 'tid', $tpl->param('tid'), 'kid', $tpl->param('kid'));
    $id = 'bez:thread' . $tpl->get('thread')->id;
} elseif ($tpl->action() == 'task_form') {
    $url = $tpl->url('task_form', 'action', $tpl->param('action', 'add'), 'tid', $tpl->param('tid'));
    $id = 'bez:tasks';
} else {
    $url = $tpl->url('task', 'action', $tpl->param('action', 'add'), 'tid', $tpl->param('tid'));
    $id = 'bez:tasks';
}
?>
<a name="z_"></a>
<form 	class="bez_form bez_task_form"
		action="<?php echo $url ?>" method="POST">
		<input type="hidden" name="id" value="<?php echo $id ?>">

		<fieldset class="bds_form">
			<?php if ($tpl->param('tid') != ''): ?>
				<div class="row">
				<label for="id"><?php echo $tpl->getLang('id') ?>:</label>
				<span><strong>#z<?php echo $tpl->get('task')->id ?></strong></span>
				</div>
				
				<?php if ($tpl->get('thread') != '' &&
                    $tpl->get('task')->acl_of('thread_comment_id') >= BEZ_PERMISSION_CHANGE): ?>
				<div class="row">
					<label for="thread_comment_id"><?php echo ucfirst($tpl->getLang('cause')) ?>:</label>
					<span>
						<select name="thread_comment_id" id="thread_comment_id">
							<option <?php if ($tpl->value('thread_comment_id') == '') echo 'selected' ?>
								value="">--- <?php echo $tpl->getLang('correction') ?> ---</option>

							<?php foreach ($tpl->get('thread')->get_causes() as $cause_id): ?>
								<option <?php if ($tpl->value('thread_comment_id') == $cause_id) echo 'selected' ?>
								 value="<?php echo $cause_id ?>">#k<?php echo $cause_id ?></option>
							<?php endforeach ?>
						</select>
					</span>
				</div>
				<?php endif ?>
			<?php endif ?>
			<div class="row">
			<label for="assignee"><?php echo $tpl->getLang('executor') ?>:</label>
			<span>
			<?php if ($tpl->get('task')->acl_of('assignee') >= BEZ_PERMISSION_CHANGE): ?>
				<select name="assignee" id="assignee" data-validation="required">
					<option value="">--- <?php echo $tpl->getLang('select') ?>---</option>
				<?php foreach ($tpl->get('users') as $nick => $name): ?>
					<option <?php if ($tpl->value('assignee') == $nick) echo 'selected' ?>
					 value="<?php echo $nick ?>"><?php echo $name ?></option>
				<?php endforeach ?>
				</select>
			<?php else: ?>
				<strong>
				<?php echo $tpl->user_name() ?>
				</strong>
			<?php endif ?>
			
			
			</span>
			</div>

			<div class="row">
				<label for="content"><?php echo $tpl->getLang('description') ?>:</label>
				<span>
                    <?php if ($tpl->get('task')->acl_of('content') >= BEZ_PERMISSION_CHANGE): ?>
                        <div class="bez_toolbar"></div>
                    <?php endif ?>
					<textarea name="content" id="content" data-validation="required" <?php if ($tpl->get('task')->acl_of('content') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>><?php echo $tpl->value('content') ?></textarea>
				</span>
			</div>
			
			<div class="row task_plan_field">
				<label for="plan_date"><?php echo $tpl->getLang('plan_date') ?>:</label>
				<span>
                    <input name="plan_date" style="width:90px;" data-validation="required,date" value="<?php echo $tpl->value('plan_date') ?>"
                    <?php if ($tpl->get('task')->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>
                    />
                    <div style="display:inline" id="task_datapair">
                        <?php echo $tpl->getLang('from_hour') ?>
                        <input name="start_time" style="width:60px;" class="time start" value="<?php echo $tpl->value('start_time') ?>"
                        data-validation="required,custom"
                        data-validation-regexp="^(\d{1,2}):(\d{1,2})$"
                        data-validation-depends-on="all_day_event"
                        <?php if ($tpl->get('task')->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>
                        />
                        <?php echo $tpl->getLang('to_hour') ?>
                        <input name="finish_time" style="width:60px;" class="time end" value="<?php echo $tpl->value('finish_time') ?>"
                        data-validation="required,custom"
                        data-validation-regexp="^(\d{1,2}):(\d{1,2})$"
                        data-validation-depends-on="all_day_event"
                        <?php if ($tpl->get('task')->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>
                        />
                    </div>
				</span>
			</div>
			
			<div class="row">
				<label></label>
				<span>
					<label>
                        <?php if ($tpl->get('task')->acl_of('all_day_event') >= BEZ_PERMISSION_CHANGE): ?>
                        <input type="checkbox" name="all_day_event" value="1" 
                            <?php if ($tpl->value('all_day_event') == '' ||
                                        $tpl->value('all_day_event') == '1'): ?>
                                checked
                            <?php endif ?> /> 
                        <?php else: ?>
                             <input type="checkbox" disabled
                            <?php if ($tpl->get('task')->all_day_event == '1'): ?>
                                checked
                            <?php endif ?> />
                        <?php endif ?> <?php echo $tpl->getLang('all_day_event') ?>
                    </label>
                
                </span>
			</div>
			
			<div class="row">
			<label for="tasktype"><?php echo $tpl->getLang('task_type') ?>:</label>
			<span>
                <select id="task_program_id" name="task_program_id" <?php if ($tpl->get('task')->acl_of('task_program_id') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>>
                    <?php if ($tpl->get('task')->can_be_null('task_program_id')): ?>
                        <option <?php if ($tpl->value('task_program_id') == '') echo 'selected' ?> value="">
                            <?php echo $tpl->getLang('tasks_no_type') ?>
                        </option>
                    <?php endif ?>
                    
                    <?php foreach ($tpl->get('task_programs') as $task_program): ?>
                        <option <?php if ($tpl->value('task_program_id') == $task_program->id) echo 'selected' ?> value="<?php echo $task_program->id ?>"><?php echo $task_program->name ?></option>
                    <?php endforeach ?>
                </select>
			</span>
			</div>

			<div class="row">
				<label for="cost"><?php echo $tpl->getLang('cost') ?>:</label>
				<span><input 	type="number" name="cost" id="cost"
								min="0" step="0.01"
                                value="<?php echo $tpl->value('cost') ?>"
                                <?php if ($tpl->get('task')->acl_of('plan_date') < BEZ_PERMISSION_CHANGE) echo 'disabled' ?>></span>
			</div>
			<?php if ($tpl->param('tid') != ''): ?>
				<div class="row">
				<label for="task_state"><?php echo $tpl->getLang('task_state') ?>:</label>
				<span>
					<strong><?php echo $tpl->getLang('task_' . $tpl->get('task')->state) ?></strong>
				</span>
				</div>
			<?php endif ?>
			<div class="row">
				<label></label>
				<span style="padding-top:10px;">
					<input type="submit" value="<?php echo $tpl->param('tid') == '' ? $tpl->getLang('add') : $tpl->getLang('correct') ?>">
					<a href="<?php
				if ($tpl->action() == 'thread') {
					echo $tpl->url('thread', 'id', $tpl->get('thread')->id);
				} else if ($tpl->action() == 'task' && $tpl->get('task')->id != '') {
					echo $tpl->url('task', 'tid', $tpl->get('task')->id);
				} else if ($tpl->get('task')->task_program_id != '') {
                    echo $tpl->url('tasks', 'task_program_id', $tpl->get('task')->task_program_id);
                } else {
                    echo $tpl->url('tasks');
                }
				?><?php if ($tpl->param('tid') != '') echo '#z'.$tpl->param('tid') ?>"
				class="bez_delete_button bez_link_button">
					<?php echo $tpl->getLang('cancel') ?>
				</a>
			</span>
        </div>
    </fieldset>
</form>
