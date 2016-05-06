<div id="bez_issue_report" class="bds_block <?php echo $template['action'] ?>">
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('issue_report', 'id', $template['issue_id'], 'action', $template['action']) ?>" method="POST">
<div class="priorities">
<label><input <?php if ($value['priority'] == '0') echo 'checked' ?> type="radio" name="priority" value="0"><?php echo $bezlang['priority_marginal'] ?></label>
<label><input <?php if (!isset($value['priority']) || $value['priority'] == '1') echo 'checked' ?> type="radio" name="priority" value="1"><?php echo $bezlang['priority_important'] ?></label>
<label><input <?php if ($value['priority'] == '2') echo 'checked' ?> type="radio" name="priority" value="2"><?php echo $bezlang['priority_crucial'] ?></label>
</div>
<fieldset class="bds_form">

<?php if ($template['action'] == 'update'): ?>
<div class="row">
<label for="id"><?php echo $bezlang['id'] ?>:</label>
<span><strong>#<?php echo $template['issue_id'] ?></strong></span>
</div>
<?php endif ?>


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

<?php if ($template['user_admin']): ?>
<div class="row">
<label for="coordinator"><?php echo $bezlang['coordinator'] ?>:</label>
<span>
<select name="coordinator" id="coordinator">
<option <?php if ($value['coordinator'] == '-proposal') echo 'selected' ?>
	value="-proposal">--- <?php echo $bezlang['state_proposal'] ?> ---</option>

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
	<div class="row">
	<label for="state"><?php echo $bezlang['state'] ?>:</label>
	<span>
		<strong><?php echo $template['state'] ?></strong>
	</span>
	</div>
	<?php if ($template['raw_state'] == 1) : ?>
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
 <a href="#" onclick="window.history.back()" class="bez_delete_button bez_link_button bez_cancel_button">
	<?php echo $bezlang['cancel'] ?>
</a>
</form>
</div>
