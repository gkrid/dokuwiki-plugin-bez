<?php if ($template['issue'][raw_state] == 0 &&
		 ($helper->user_coordinator($template[issue][id]) || isset($nparams[tid]))): ?>
	<?php $issue = $template[issue] ?>
	<?php include "issue_box.php" ?>
	<br>
	<?php if (isset($template[cause])): ?>
		<?php $cause = $template[cause] ?>
		<div class="bds_block" id="bez_causes">
			<?php include "cause.php" ?>
		</div>
	<?php endif ?>
	<br>
	<form class="bez_form bez_task_form" action="?id=<?php echo $template['task_action'] ?>" method="POST">
			<fieldset class="bds_form">
				<?php if (isset($nparams[tid])): ?>
					<div class="row">
					<label for="id"><?php echo $bezlang['id'] ?>:</label>
					<span><strong>#z<?php echo $nparams[tid] ?></strong></span>
					</div>
				<?php endif ?>
				<?php if (!$helper->user_coordinator($template[issue][id])) $disabled = 'disabled' ?> 
				<div class="row">
				<label for="executor"><?php echo $bezlang['executor'] ?>:</label>
				<span>
				<select name="executor" id="executor" <?php echo $disabled ?>>
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
					
					<strong>
						<?php if (!isset($template[cause])): ?>
							<?php echo $bezlang['correction'] ?>
							<input type="hidden" name="action" value="0" />
						<?php elseif ($template[cause][potential] == 0): ?>
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
					<span><textarea name="task" id="task" <?php echo $disabled ?>><?php echo $value['task'] ?></textarea></span>
				</div>

				<div class="row">
					<label for="cost"><?php echo $bezlang['cost'] ?>:</label>
					<span><input name="cost" id="cost" value="<?php echo $value['cost'] ?>" <?php echo $disabled ?>></span>
				</div>
				<?php if (isset($nparams[tid])): ?>
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
						<label for="reason"><?php echo $bezlang['reason'] ?>/<?php echo $bezlang[evaluation] ?>:</label>
						<span><textarea name="reason" id="reason"><?php echo $value['reason'] ?></textarea></span>
					</div>
				<?php endif ?>
			</fieldset>
			<input type="submit" value="<?php echo $template['task_button'] ?>">
			<a href="?id=<?php echo $this->id('issue_task', 'id', $template['issue']['id'], 'tid', $nparams[tid]) ?>"
			 class="bez_delete_button bez_link_button">
				<?php echo $bezlang['cancel'] ?>
			</a>

		</form>
	<?php endif ?>
	<a name="z_"></a>
	</div>
</div>

