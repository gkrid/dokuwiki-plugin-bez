<form action="<?php echo $_SERVER[REQUEST_URI] ?>" method="POST">
<filedset class="bds_form">
<div class="row">
<label for="type"><?php echo $bezlang['type'] ?>:</label>

<span>
<select name="type" id="type">
<?php foreach ($template['issue_types'] as $key => $name): ?>
	<option <?php if ($value['type'] == $key) echo 'selected' ?>
	 value="<?php echo $key ?>"><?php echo $name ?></opiton>
<?php endforeach ?>
</select>
</span>
</div>

<div class="row">
<label for="entity"><?php echo $bezlang['entity'] ?>:</label>

<span>
<select name="entity" id="entity">
<?php foreach ($template['entities'] as $key => $name): ?>
	<option <?php if ($value['type'] == $key) echo 'selected' ?>
	 value="<?php echo $key ?>"><?php echo $name ?></opiton>
<?php endforeach ?>
</select>
</span>
</div>

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
	<?php if ($helper->user_is_moderator()): ?>
		<div class="row">
		<label for="coordinator"><?php echo $bezlang['coordinator'] ?>:</label>
		<span>
		<select name="coordinator" id="coordinator">
		<?php foreach ($helper->retrieveUsers() as $key => $data): ?>
			<?php if ($helper->user_is_moderator($key)): ?>
				<option <?php if ($value['coordinator'] == $key) echo 'selected' ?>
				 value="<?php echo $key ?>"><?php echo $data['name'] ?></opiton>
			<?php endif ?>
		<?php endforeach ?>
		</select>
		</span>
		</div>

		<div class="row">
		<label for="state"><?php echo $bezlang['state'] ?>:</label>
		<span>
		<select name="state" id="state">
		<?php foreach ($this->issue_states as $key => $state): ?>
			<option <?php if ($value['state'] == $key) echo 'selected' ?>
			 value="<?php echo $key ?>"><?php echo $state ?></opiton>
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
</filedset>

<input type="submit" value="<?php echo $bezlang['save'] ?>">
</form>
