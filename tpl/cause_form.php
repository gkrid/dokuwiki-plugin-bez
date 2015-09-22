<?php include "issue_box.php" ?>
<br>
<?php if ($template['issue'][raw_state] == 0 && $helper->user_coordinator($template[issue][id])): ?> 
	<form action="?id=<?php echo $template['cause_action'] ?>#p_" method="POST" class="bez_form bez_cause_form">
		<fieldset>
			<div class="row">
				<label for="potential"><?php echo $bezlang['cause_type'] ?>:</label>
				<span>
					<label>
					<input type="radio" name="potential" value="0"
						<?php if($value['potential'] == 0) echo 'checked' ?>/>
						<?php echo $bezlang['cause_type_default'] ?>
					</label>
					&nbsp;&nbsp;
					<label>
					<input type="radio" name="potential" value="1"
						<?php if($value['potential'] == 1) echo 'checked' ?>/>
						<?php echo $bezlang['cause_type_potential'] ?>
					</label>
				</span>
			</div>
			<div class="row">
			<label for="rootcause"><?php echo $bezlang['root_cause'] ?>:</label>
			<span>
				<select name="rootcause" id="rootcause">
				<?php foreach ($template['rootcauses'] as $key => $name): ?>
					<option <?php if ($value['rootcause'] == $key) echo 'selected' ?>
					 value="<?php echo $key ?>"><?php echo $name ?></option>
				<?php endforeach ?>
				</select>
			</span>
			</div>
			<div class="row">
				<label for="cause"><?php echo $bezlang['description'] ?>:</label>
				<span><textarea name="cause" id="cause"><?php echo $value['cause'] ?></textarea></span>
			</div>
		</fieldset>
		<input type="submit" value="<?php echo $template['cause_button'] ?>">
		<a href="?id=<?php echo $this->id('issue_causes', 'id', $template['issue']['id']) ?>"
		 class="bez_delete_button bez_link_button">
			<?php echo $bezlang['cancel'] ?>
		</a>
	</form>
	<a name="p_"></a>
<?php endif ?>

