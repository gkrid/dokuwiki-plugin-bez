<div id="bez_issue_report" class="bds_block <?php echo $template['action'] ?>">
<form action="<?php echo $template['uri'] ?>?id=bez:issue_report:<?php echo $template['issue_id'] ?>:<?php echo $template['action'] ?>" method="POST">
<fieldset class="bds_form">
<div class="row">
<label for="type"><?php echo $bezlang['type'] ?>:</label>

<span>
<select name="type" id="type">
<?php foreach ($template['issue_types'] as $key => $name): ?>
	<option <?php if ($value['type'] == $key) echo 'selected' ?>
	 value="<?php echo $key ?>"><?php echo $name ?></option>
<?php endforeach ?>
</select>
</span>
</div>

<div class="row">
<label for="entity"><?php echo $bezlang['entity'] ?>:</label>

<span>
<select name="entity" id="entity">
<?php foreach ($template['entities'] as $entity): ?>
	<option <?php if ($value['entity'] == $entity) echo 'selected' ?>
	 value="<?php echo $entity ?>"><?php echo $entity ?></option>
<?php endforeach ?>
</select>
</span>
</div>

<?php if ($template['user_admin']): ?>
<div class="row">
<label for="coordinator"><?php echo $bezlang['coordinator'] ?>:</label>
<span>
<select name="coordinator" id="coordinator">
<option <?php if ($value['coordinator'] == '-proposal') echo 'selected' ?>
	value="-proposal">--- <?php echo $bezlang['state_proposal'] ?> ---</option>
<option <?php if ($value['coordinator'] == '-rejected') echo 'selected' ?>
	value="-rejected">--- <?php echo $bezlang['state_rejected'] ?> ---</option>
<?php foreach ($template['nicks'] as $nick => $name): ?>
	<option <?php if ($value['coordinator'] == $nick) echo 'selected' ?>
	 value="<?php echo $nick ?>"><?php echo $name ?></option>
<?php endforeach ?>
</select>
</span>
</div>
<?php endif ?>

<div class="row">
<label for="title"><?php echo $bezlang['title'] ?>:</label>
<span>
<input name="title" id="title" value="<?php echo $value['title'] ?>">
</span>
</div>

<div class="row">
<label for="description"><?php echo $bezlang['description'] ?>:</label>
<span>
<textarea name="description" id="description" class="edit">
<?php echo $value['description'] ?>
</textarea>
</span>
</div>
<?php if ($template['action'] == 'update' && $template['user_coordinator']): ?>
	<?php if ($template['any_task_open']) : ?>
		<div class="row" >
			<div style="display:table-cell"><br><br></div>
			<label style="position: relative">
				<div class="info" style="position: absolute; left: -10em; width:29em;"><?php echo $bezlang['info_no_all_tasks_closed'] ?></div>
			</label>
		</div>
	<?php endif ?>
	<div class="row">
	<label for="state"><?php echo $bezlang['state'] ?>:</label>
	<span>
	<?php if ($template['any_task_open']): ?>
		<strong><?php echo $bezlang['state_opened'] ?></strong>
	<?php else: ?>
		<?php foreach ($template['issue_states'] as $key => $state): ?>
			<label><input name="state" type="radio" <?php if ($value['state'] == $key) echo 'checked' ?>
			 value="<?php echo $key ?>" /><?php echo $state ?></label>
		<?php endforeach ?>
	<?php endif ?>
	</span>
	</div>

	<div class="row">
	<label for="opinion"><?php echo $bezlang['opinion'] ?>:</label>
	<span>
	<textarea name="opinion" id="opinion" class="edit"><?php echo $value['opinion'] ?></textarea>
	</span>
	</div>
<?php endif ?>
</fieldset>

<input type="submit" value="<?php echo $bezlang['save'] ?>">
<?php if ($template['action'] == 'update'): ?>
	 <a href="<?php echo $tepmlate['uri'] ?>?id=bez:issue_show:<?php echo $template['issue_id'] ?>" class="bez_delete_button bez_link_button">
		<?php echo $bezlang['cancel'] ?>
	</a>
<?php endif ?>
</form>
</div>
