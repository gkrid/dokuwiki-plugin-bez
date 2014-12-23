<form action="<?php echo $_SERVER[REQUEST_URI] ?>" method="POST">
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
<?php foreach ($template['entities'] as $key => $name): ?>
	<option <?php if ($value['entity'] == $key) echo 'selected' ?>
	 value="<?php echo $key ?>"><?php echo $name ?></option>
<?php endforeach ?>
</select>
</span>
</div>

<?php if ($template['user_admin']): ?>
<div class="row">
<label for="coordinator"><?php echo $bezlang['coordinator'] ?>:</label>
<span>
<select name="coordinator" id="coordinator">
<option value="">--- <?php echo $bezlang['state_proposal'] ?> ---</option>
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
<?php if (isset($template['editcase'])): ?>
	<?php if ($template['user_is_coordinator']): ?>
		<div class="row">
		<label for="state"><?php echo $bezlang['state'] ?>:</label>
		<span>
		<select name="state" id="state">
		<?php foreach ($this->issue_states as $key => $state): ?>
			<option <?php if ($value['state'] == $key) echo 'selected' ?>
			 value="<?php echo $key ?>"><?php echo $state ?></option>
		<?php endforeach ?>
		</select>
		</span>
		</div>

		<div class="row">
		<label for="opinion"><?php echo $bezlang['opinion'] ?>:</label>
		<span>
		<textarea name="opinion" id="opinion" class="edit"><?php echo $value['opinion'] ?></textarea>
		</span>
		</div>
	<?php endif ?>
<?php endif ?>
</fieldset>

<input type="submit" value="<?php echo $bezlang['save'] ?>">
</form>
