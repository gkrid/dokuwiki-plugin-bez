<table>
<tr>
	<th>Polski</th>
	<th>English</th>
	<th>Coordinator</th>
	<th colspan="3">References</th>
</tr>
</tr>
<?php foreach ($template['types'] as $type): ?>
<tr>
	<?php if ($template['edit'] == $type->id): ?>
	<form action="<?php echo $template['uri'] ?>
				?id=<?php echo $this->id('task_types', 'action', 'update', 'id', $type->id) ?>" method="POST">
		<td><input name="pl" value="<?php echo $value['pl'] ?>" /></td>
		<td><input name="en" value="<?php echo $value['en'] ?>" /></td>
		<td>
		<select name="coordinator" id="coordinator">
				<option value="">--- <?php echo $bezlang['select'] ?>---</option>
			<?php foreach ($this->model->users->get_all() as $nick => $name): ?>
				<option <?php if ($value['coordinator'] == $nick) echo 'selected' ?>
				 value="<?php echo $nick ?>"><?php echo $name ?></option>
			<?php endforeach ?>
			</select>
		</td>
		<td>
			<input type="submit" value="<?php echo $bezlang['save'] ?>" />
		<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('types') ?>">
			<?php echo $bezlang['cancel'] ?>
		</a>
		</td>
	</form>
	<?php else: ?>
	<td><?php echo $type->pl ?></td>
	<td><?php echo $type->en ?></td>
	<td><?php echo $this->model->users->get_user_full_name($type->coordinator) ?></td>
	<td>
	<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('task_types', 'action', 'edit', 'id', $type->id) ?>">
		<?php echo $bezlang['edit'] ?>
	</a>
	</td>
	<td>
	<a onclick="return confirm('Czy na pewno chcesz usunąć ten program?')"
		href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('task_types', 'action', 'remove', 'id', $type->id) ?>">
		<?php echo $bezlang['delete'] ?>
	</a>
	</td>
	<?php endif ?>
	<td>
	<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('tasks', 'tasktype', $type->id) ?>">
		<?php echo $type->refs ?>
	</a>
</td>
</tr>
<?php endforeach ?>

<?php if ($template['edit'] == -1): ?>
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('task_types', 'action', 'add') ?>" method="POST">
<tr>
	<td><input name="pl" value="<?php echo $value['pl'] ?>" /></td>
	<td><input name="en" value="<?php echo $value['en'] ?>" /></td>
	<td>
	<select name="coordinator" id="coordinator">
			<option value="">--- <?php echo $bezlang['select'] ?>---</option>
		<?php foreach ($this->model->users->get_all() as $nick => $name): ?>
			<option <?php if ($value['coordinator'] == $nick) echo 'selected' ?>
			 value="<?php echo $nick ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</td>
	<td colspan="3"><input type="submit" value="<?php echo $bezlang['save'] ?>" /></td>
</tr>
<?php endif ?>
</form>
</table>
