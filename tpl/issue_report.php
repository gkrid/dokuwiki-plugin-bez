<div class="bds_block <?php echo $template['action'] ?>">
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('issue_report', 'id', $template['issue_id'], 'action', $template['action']) ?>" method="POST">
<fieldset id="bds_issue_box"  class="bds_form pr<?php echo $template['priority'] ?>">

<?php if ($template['action'] == 'update'): ?>
<div class="row">
<label for="id"><?php echo $bezlang['id'] ?>:</label>
<span><strong>#<?php echo $template['issue_id'] ?></strong></span>
</div>
<?php endif ?>


<?php if ($template['user_admin']): ?>
<div class="row">
<label for="type"><?php echo $bezlang['type'] ?>:</label>
<span>
<select name="type" id="type">
<option <?php if ($value['type'] == '') echo 'selected' ?>
	value="">--- <?php echo $bezlang['issue_type_no_specified'] ?> ---</option>
<?php foreach ($template['issue_types'] as $key => $name): ?>
	<option <?php if ($value['type'] == $key) echo 'selected' ?>
	 value="<?php echo $key ?>"><?php echo $name ?></option>
<?php endforeach ?>
</select>
</span>
</div>

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
			<label for="opinion">
				<?php if ($template['anytasks']): ?>
					<?php echo $bezlang['opinion'] ?>:
				<?php else: ?>
					<?php echo $bezlang['reason'] ?>:
				<?php endif ?>
			</label>
			<span>
				<textarea name="opinion" id="opinion" class="edit"><?php echo $value['opinion'] ?></textarea>
			</span>
		</div>
	<?php endif ?>
<?php endif ?>
</fieldset>

<input type="submit" value="<?php echo $bezlang['save'] ?>">
 <a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('issue', 'id', $template['issue_id']) ?>" class="bez_delete_button bez_link_button bez_cancel_button">
	<?php echo $bezlang['cancel'] ?>
</a>
</form>
</div>
